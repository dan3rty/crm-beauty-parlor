<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Database\BarberTable;
use App\Entity\Barber;
use App\Entity\Client;
use App\Service\BarberService;
use App\Tests\Common\AbstractDatabaseTestCase;

class BarberServiceTest extends AbstractDatabaseTestCase
{
    public function testCreateUpdateAndDeleteBarber(): void
    {
        // Arrange
        $service = $this->createBarberService();

        // Act
        $barberId = $service->addBarber();

        // Assert
        $barber = $service->getBarber($barberId);
        $this->assertNotNull($barber);
        $this->assertNewBarberData($barber);

        // Arrange
        $newBarberData = [
            'id' => $barberId,
            'name' => 'Александр',
            'surname' => 'Блок',
            'phone' => '+79875992570'
        ];

        // Act
        $service->updateBarber(
            $newBarberData['id'],
            $newBarberData['name'],
            $newBarberData['surname'],
            $newBarberData['phone']
        );

        // Assert
        $barber = $service->getBarber($barberId);
        $this->assertBarberValues($newBarberData, $barber);

        // Act
        $service->deleteBarber($barber->getId());

        // Assert
        $barber = $service->getBarber($barberId);
        $this->assertNull($barber);
    }

    private function assertNewBarberData(Barber $entity): void
    {
        $expected = ['Имя', 'Фамилия', ''];
        $actual = [$entity->getName(), $entity->getSurname(), $entity->getPhone()];
        $this->assertEquals($expected, $actual, 'default values');
    }

    private function createBarberService(): BarberService
    {
        return new BarberService(
            new BarberTable()
        );
    }

    private function assertBarberValues(array $expected, Barber $actual): void
    {
        $actual = [
            'id' => $actual->getId(),
            'name' => $actual->getName(),
            'surname' => $actual->getSurname(),
            'phone' => $actual->getPhone(),
        ];

        $this->assertEquals($expected, $actual, 'barber values');
    }
}
