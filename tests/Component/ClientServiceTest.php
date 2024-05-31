<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Database\ClientTable;
use App\Entity\Client;
use App\Service\ClientService;
use App\Tests\Common\AbstractDatabaseTestCase;

class ClientServiceTest extends AbstractDatabaseTestCase
{
    public function testCreateUpdateClient(): void
    {
        // Arrange
        $service = $this->createClientService();

        // Act
        $clientId = $service->addClient();

        // Assert
        $client = $service->getClient($clientId);
        $this->assertNotNull($client);
        $this->assertNewClientData($client);

        // Arrange
        $newClientData = [
            'id' => $clientId,
            'name' => 'Михаил',
            'surname' => 'Задорнов',
            'phone' => '+79021159394'
        ];

        // Act
        $service->updateClient(
            $newClientData['id'],
            $newClientData['name'],
            $newClientData['surname'],
            $newClientData['phone']
        );

        // Assert
        $client = $service->getClient($clientId);
        $this->assertClientValues($newClientData, $client);
    }

    private function assertNewClientData(Client $entity): void
    {
        $expected = ['Имя', 'Фамилия', ''];
        $actual = [$entity->getName(), $entity->getSurname(), $entity->getPhone()];
        $this->assertEquals($expected, $actual, 'default values');
    }

    private function assertClientValues(array $expected, Client $actual): void
    {
        $actual = [
            'id' => $actual->getId(),
            'name' => $actual->getName(),
            'surname' => $actual->getSurname(),
            'phone' => $actual->getPhone(),
        ];

        $this->assertEquals($expected, $actual, 'client values');
    }

    private function createClientService(): ClientService
    {
        return new ClientService(
            new ClientTable()
        );
    }
}
