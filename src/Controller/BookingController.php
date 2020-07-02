<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Bookings;
use Symfony\Component\Mime\Email;
use DateTime;
use DateInterval;

class BookingController extends AbstractController
{
    /**
     * @Route("/booktime", name="booktime")
     */
    public function index()
    {
        return $this->render('booking/index.html.twig');
    }


    /**
     * @Route("book/{slug}", name="book")
     */
    public function bookAppointment(string $slug)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $currentDate = new DateTime(date("Y-m-d"));
        $bookedAppointment = $entityManager->getRepository(Bookings::class)->getBookedAppointment($currentDate, 8);
        $bookingTimes = $this->getBookingTimings($currentDate);
        foreach ($bookedAppointment as $appointment) {
            if (isset($bookingTimes[$appointment->getBookingDate()->format('Y-m-d')]) && 
                isset($bookingTimes[$appointment->getBookingDate()->format('Y-m-d')][$appointment->getFromTime().' - '.$appointment->getToTime()])
            ) {
                $bookingTimes[$appointment->getBookingDate()->format('Y-m-d')][$appointment->getFromTime().' - '.$appointment->getToTime()] = 'unavailable';
            }
        }

        return $this->render('booking/book-appointment.html.twig', ['level_name' => $slug,'max_persons' => $this->getParameter('app.max_persons'),'bookingTimes' => $bookingTimes]);
    }

    /**
     * @Route("/add-appointment", name="add-appointment")
     */
    public function addAppointment() {
        $request = Request::createFromGlobals();
        $params = $request->request->all();
        $entityManager = $this->getDoctrine()->getManager();
        $booking_time = explode('-', $params['booking_time']);
        $booking_date = \DateTime::createFromFormat('Y-m-d', $params['booking_date']);
        $available = $entityManager->getRepository(Bookings::class)->findOneBy(['booking_date' => $booking_date, 'from_time' => trim($booking_time[0]), 'to_time' => trim($booking_time[1])]);
        if (!$available) {
            $booking = new Bookings();
            $booking->setFirstName($params['firstname'])
                    ->setLastName($params['lastname'])
                    ->setEmail($params['email'])
                    ->setPhoneno($params['phoneno'])
                    ->setBookingDate($booking_date)
                    ->setFromTime(trim($booking_time[0]))
                    ->setToTime(trim($booking_time[1]))
                    ->setPersons($params['persons'])
                    ->setCreatedAt(new DateTime(date('Y-m-d H:i:s')));
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->sendEmail($booking);
            $this->addFlash('success', 'Appointment Booked Successfully!');
        } else {
            $this->addFlash('error', 'Appointment Not Available For That Date And Time!');
        }
        return $this->redirect($this->generateUrl('book-appointment'));
    }

    /**
     * @Route("/fetch-appointment-days", name="fetch-appointment-days")
     */
    public function fetchAppointmentDays() {
        $request = Request::createFromGlobals();
        $date = new DateTime($request->request->get('date'));
        $date->modify('+1 day');
        $lastDate = new DateTime(date("Y-m-d"));
        $lastDate->modify('+'.(intval($this->getParameter('app.max_weeks'))*7).' days'); // X7 for Future WeekDays and +8 for Current WeekDays
        if ($date > $lastDate) {
            return new JsonResponse(['status' => 'error']);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $bookedAppointment = $entityManager->getRepository(Bookings::class)->getBookedAppointment($date, 7);
        $bookingTimes = $this->getBookingTimings($date, 7);
        foreach ($bookedAppointment as $appointment) {
            if (isset($bookingTimes[$appointment->getBookingDate()->format('Y-m-d')]) && 
                isset($bookingTimes[$appointment->getBookingDate()->format('Y-m-d')][$appointment->getFromTime().' - '.$appointment->getToTime()])
            ) {
                $bookingTimes[$appointment->getBookingDate()->format('Y-m-d')][$appointment->getFromTime().' - '.$appointment->getToTime()] = 'unavailable';
            }
        }
        $html = $this->renderView('booking/bookingTimings.html.twig', ['bookingTimes' => $bookingTimes]);
        return new JsonResponse(['status' => 'success', 'html' => $html]);
    }


    public function getBookingTimings($startDate, $noOfDays = 8) {
        $bookingTimes = [];
        $currentTime = new DateTime(date("Y-m-d H:i"));
        $timeIntervals = (intval($this->getParameter('app.session_length')) + intval($this->getParameter('app.break_bw_sessions')));
        for ($i=0; $i<$noOfDays; $i++) {
            $startTime = new DateTime(date("Y-m-d H:i", strtotime($startDate->format('Y-m-d') . ' ' . $this->getParameter('app.start_time'))));;
            $endTime = new DateTime(date("Y-m-d H:i", strtotime($startDate->format('Y-m-d') . ' ' . $this->getParameter('app.start_time'))));
            $endTime->add(new DateInterval('PT' . $this->getParameter('app.session_length') . 'M'));
            for ($j=0; $j<$this->getParameter('app.max_sessions'); $j++) {
                $time = $startTime->format('H:i') . ' - ' . $endTime->format('H:i');
                $bookingTimes[$startDate->format('Y-m-d')][$time] = ($currentTime > $endTime)? 'disabled': 'available';
                if ($j!=($this->getParameter('app.max_sessions')-1)) {
                    $startTime->add(new DateInterval('PT' . $timeIntervals . 'M'));
                    $endTime->add(new DateInterval('PT' . $timeIntervals . 'M'));
                }
                $bookingTimes[$startDate->format('Y-m-d')][$time] = ($currentTime > $startTime)? 'disabled': 'available';
            }
            $startDate->modify('+1 day');
        }
        return $bookingTimes;
    }

    public function sendEmail($booking) {
        $toEmails = [
            $this->getParameter('app.admin_email'),
            $booking->getEmail()
        ];
        $messageBody = $this->renderView('emails/booking-confirmation.html.twig', [
            'firstname' => $booking->getFirstName(),
            'lastname' => $booking->getLastName(),
            'email' => $booking->getEmail(),
            'phoneno' => $booking->getPhoneno(),
            'booking_date' => $booking->getBookingDate()->format('Y-m-d'),
            'from_time' => $booking->getFromTime(),
            'to_time' => $booking->getToTime(),
            'persons' => $booking->getPersons()
        ]);
        foreach ($toEmails as $receiverEmail) {
            (new Email())
            ->subject('Booking Confirmed!!!')
            ->from($this->getParameter('app.sender_email'))
            ->to($receiverEmail)
            ->html($messageBody);
        }
    }
}
