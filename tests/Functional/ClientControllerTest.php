<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Database\ClientTable;
use App\Tests\Common\AbstractWebTestCase;

class ClientControllerTest extends AbstractWebTestCase
{
    private ClientTable $clientTable;
    private const UPDATE_SNAPSHOTS = false;
    private const SNAPSHOT_INFO_PATH = '../snapshots/client/info.html';
    private const SNAPSHOT_LIST_PATH = '../snapshots/client/list.html';

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ClientTable $table */
        $table = $this->getContainer()->get(ClientTable::class);
        $this->clientTable = $table;
    }

    public function testCreateEditClient()
    {
        // Act
        $clientId = $this->doNewClient();

        // Assert
        $this->assertResponseRedirects();
        $client = $this->clientTable->findById($clientId);
        $this->assertNotNull($client);

        // Arrange
        $newClientData = [
            'id' => $clientId,
            'name' => 'Михаил',
            'surname' => 'Задорнов',
            'phone' => '+79026458923'
        ];

        // Act
        $this->doUpdateClient($newClientData);

        // Assert
        $this->assertResponseRedirects();
        $client = $this->clientTable->findById($clientId);
        $this->assertEquals($newClientData, $client);
    }

    public function testShowClientInfoSnapshot()
    {
        // Arrange
        $clientId = $this->doNewClient();

        // Act
        $content = $this->doGetClientInfoContent($clientId);

        // Assert
        $replacements = [
            '{ client_id }' => (string)$clientId
        ];
        $this->doSnapshotTest(self::SNAPSHOT_INFO_PATH, $content, $replacements);
    }

    public function testShowClientListSnapshot()
    {
        // Arrange
        $clientId = $this->doNewClient();

        // Act
        $content = $this->doGetClientsListContent();

        // Assert
        $replacements = ['{ client_id }' => (string)$clientId];
        $this->doSnapshotTest(self::SNAPSHOT_LIST_PATH, $content, $replacements);
    }

    protected function shouldUpdateSnapshots(): bool
    {
        return self::UPDATE_SNAPSHOTS;
    }
}
