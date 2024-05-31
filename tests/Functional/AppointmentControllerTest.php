<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Database\AppointmentTable;
use App\Tests\Common\AbstractWebTestCase;

class AppointmentControllerTest extends AbstractWebTestCase
{
    private AppointmentTable $appointmentTable;
    private const UPDATE_SNAPSHOTS = false;
    private const SNAPSHOT_BARBER_INFO_PATH = '../snapshots/appointment/barber_info.html';
    private const SNAPSHOT_CLIENT_INFO_PATH = '../snapshots/appointment/client_info.html';

    protected function setUp(): void
    {
        parent::setUp();

        /** @var AppointmentTable $table */
        $table = $this->getContainer()->get(AppointmentTable::class);
        $this->appointmentTable = $table;
    }

    public function testCreateAppointment()
    {
        // Arrange
        $barberId = $this->doNewBarber();
        $clientId = $this->doNewClient();
        $appointmentParams = [
          'barber_id' => $barberId,
          'client_id' => $clientId,
          'date' => date('Y-m-d H:i:s')
        ];

        // Act
        $this->doAddAppointment($appointmentParams);

        // Assert
        $this->assertResponseRedirects();
        $barberAppointments = $this->appointmentTable->findByBarberId($barberId);
        $clientAppointments = $this->appointmentTable->findByClientId($clientId);
        $this->assertNotEmpty($barberAppointments);
        $this->assertNotEmpty($clientAppointments);
        $this->assertEquals($barberAppointments, $clientAppointments);
    }

    public function testShowBarberAppointmentsSnapshot()
    {
        // Arrange
        $barberId = $this->doNewBarber();
        $clientId = $this->doNewClient();
        $date = date('Y-m-d H:i:s');
        $time = date('H:i', strtotime($date));
        $appointmentParams = [
            'barber_id' => $barberId,
            'client_id' => $clientId,
            'date' => $date
        ];
        $this->doAddAppointment($appointmentParams);

        // Act
        $content = $this->doGetBarberInfoContent($barberId);

        // Assert
        $replacements = $this->getTenNearDates();
        $replacements['{ barber_id }'] = (string)$barberId;
        $replacements['{ time }'] = $time;
        $this->doSnapshotTest(self::SNAPSHOT_BARBER_INFO_PATH, $content, $replacements);
    }

    public function testShowClientAppointmentsSnapshot()
    {
        // Arrange
        $barberId = $this->doNewBarber();
        $clientId = $this->doNewClient();
        $date = date('Y-m-d H:i:s');
        $appointmentParams = [
            'barber_id' => $barberId,
            'client_id' => $clientId,
            'date' => $date
        ];
        $this->doAddAppointment($appointmentParams);

        // Act
        $content = $this->doGetClientInfoContent($clientId);

        // Assert
        $replacements = [
            '{ client_id }' => (string)$clientId,
            '{ datetime }' => date('j F Y H:i', strtotime($date))
        ];
        $this->doSnapshotTest(self::SNAPSHOT_CLIENT_INFO_PATH, $content, $replacements);
    }

    protected function shouldUpdateSnapshots(): bool
    {
        return self::UPDATE_SNAPSHOTS;
    }
}
