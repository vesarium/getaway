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



class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function main_page()
    {
        return $this->render('base.html.twig');
    }


}
