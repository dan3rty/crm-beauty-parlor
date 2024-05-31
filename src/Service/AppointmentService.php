<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Appointment;
use App\Database\AppointmentTable;

class AppointmentService
{
    private AppointmentTable $appointmentTable;

    public function __construct(AppointmentTable $appointmentTable)
    {
        $this->appointmentTable = $appointmentTable;
    }

    public function getAppointmentsByBarberId(int $barberId): array
    {
        $appointments = $this->appointmentTable->findByBarberId($barberId);

        foreach ($appointments as &$appointment)
        {
            $appointment = new Appointment(
                $appointment['id'],
                $appointment['barber_id'],
                $appointment['client_id'],
                $appointment['date']
            );
        }

        return $appointments;
    }

    public function getAppointmentsByClientId(int $clientId): array
    {
        $appointments = $this->appointmentTable->findByClientId($clientId);

        foreach ($appointments as &$appointment)
        {
            $appointment = new Appointment(
                $appointment['id'],
                $appointment['barber_id'],
                $appointment['client_id'],
                $appointment['date']
            );
        }

        return $appointments;
    }

    public function getLastAppointmentDateByClientId(int $clientId): string
    {
        $appointment = $this->appointmentTable->findLastByClientId($clientId);

        return $appointment ? $appointment['datetime'] : '';
    }

    public function addAppointment(int $barber_id, int $client_id, string $date): Appointment
    {
        $appointment = new Appointment(
            null,
            $barber_id,
            $client_id,
            $date
        );

        $this->appointmentTable->insert($appointment);

        return $appointment;
    }
}