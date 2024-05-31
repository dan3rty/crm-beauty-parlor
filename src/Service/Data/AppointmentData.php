<?php
declare(strict_types=1);

namespace App\Service\Data;

class AppointmentData
{
    private int $id;
    private int $barberId;
    private int $clientId;
    private string $date;

    public function __construct(
        int $id,
        int $barberId,
        int $clientId,
        string $date)
    {
        $this->id = $id;
        $this->barberId = $barberId;
        $this->clientId = $clientId;
        $this->date = $date;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBarberId(): int
    {
        return $this->barberId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}