<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingsRepository")
 */
class Bookings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $phoneno;

    /**
     * @ORM\Column(type="date")
     */
    private $booking_date;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $from_time;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $to_time;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $persons;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneno(): ?string
    {
        return $this->phoneno;
    }

    public function setPhoneno(string $phoneno): self
    {
        $this->phoneno = $phoneno;

        return $this;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->booking_date;
    }

    public function setBookingDate(\DateTimeInterface $booking_date): self
    {
        $this->booking_date = $booking_date;

        return $this;
    }

    public function getFromTime(): ?string
    {
        return $this->from_time;
    }

    public function setFromTime(string $from_time): self
    {
        $this->from_time = $from_time;

        return $this;
    }

    public function getToTime(): ?string
    {
        return $this->to_time;
    }

    public function setToTime(string $to_time): self
    {
        $this->to_time = $to_time;

        return $this;
    }

    public function getPersons(): ?string
    {
        return $this->persons;
    }

    public function setPersons(string $persons): self
    {
        $this->persons = $persons;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
