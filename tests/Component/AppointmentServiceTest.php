<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Database\AppointmentTable;
use App\Database\BarberTable;
use App\Database\ClientTable;
use App\Entity\Appointment;
use App\Service\AppointmentService;
use App\Service\BarberService;
use App\Service\ClientService;
use App\Tests\Common\AbstractDatabaseTestCase;

class AppointmentServiceTest extends AbstractDatabaseTestCase
{
    public function testCreateAppointment(): void
    {
        // Arrange
        $barberService = $this->createBarberService();
        $clientService = $this->createClientService();
        $barberId = $barberService->addBarber();
        $clientId = $clientService->addClient();
        $appointmentService = $this->createAppointmentService();
        $appointmentParams = [
            'barber_id' => $barberId,
            'client_id' => $clientId,
            'date' => date('Y-m-d H:i:s')
        ];

        // Act
        $appointmentService->addAppointment(
            $appointmentParams['barber_id'],
            $appointmentParams['client_id'],
            $appointmentParams['date']
        );

        // Assert
        $clientAppointments = $appointmentService->getAppointmentsByClientId($clientId);
        $barberAppointments = $appointmentService->getAppointmentsByBarberId($barberId);
        $this->assertNotEmpty($clientAppointments);
        $this->assertNotEmpty($barberAppointments);
        $this->assertEquals($clientAppointments, $barberAppointments);
    }

    private function createBarberService(): BarberService
    {
        return new BarberService(
            new BarberTable()
        );
    }

    private function createClientService(): ClientService
    {
        return new ClientService(
            new ClientTable()
        );
    }

    private function createAppointmentService(): AppointmentService
    {
        return new AppointmentService(
            new AppointmentTable()
        );
    }
}
