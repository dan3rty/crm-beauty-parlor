<?php
declare(strict_types=1);

namespace App\Service;

use App\Database\ClientTable;
use App\Entity\Client;
use App\Service\Data\ClientData;

class ClientService
{
    private ClientTable $clientTable;

    private const NEW_CLIENT_NAME = 'Имя';
    private const NEW_CLIENT_SURNAME = 'Фамилия';

    public function __construct(ClientTable $clientTable)
    {
        $this->clientTable = $clientTable;
    }

    public function listClients(): array
    {
        return $this->clientTable->listAll();
    }

    public function updateClient(int $id, string $name, string $surname, string $phone): Client
    {
        $client = $this->clientTable->findById($id);
        $client = $this->getClientFromArray($client);

        $client->setName($name);
        $client->setSurname($surname);
        $client->setPhone($phone);

        $this->clientTable->update($client);

        return $client;
    }

    public function getClient(int $clientId): ?Client
    {
        $client = $this->clientTable->findById($clientId);

        if (!$client)
        {
            return null;
        }

        return new Client(
            $client['id'],
            $client['name'],
            $client['surname'],
            $client['phone']
        );
    }

    public function addClient(): int
    {
        $client = new Client(
            null,
            self::NEW_CLIENT_NAME,
            self::NEW_CLIENT_SURNAME,
            null
        );
        return $this->clientTable->insert($client);
    }

    private function getClientFromArray(array $params): Client
    {
        return new Client(
            $params['id'],
            $params['name'],
            $params['surname'],
            $params['phone']
        );
    }
}