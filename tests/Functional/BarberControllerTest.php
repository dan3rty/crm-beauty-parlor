<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Database\BarberTable;
use App\Tests\Common\AbstractWebTestCase;

class BarberControllerTest extends AbstractWebTestCase
{
    private BarberTable $barberTable;
    private const UPDATE_SNAPSHOTS = false;
    private const SNAPSHOT_INFO_PATH = '../snapshots/barber/info.html';
    private const SNAPSHOT_LIST_PATH = '../snapshots/barber/list.html';

    protected function setUp(): void
    {
        parent::setUp();

        /** @var BarberTable $table */
        $table = $this->getContainer()->get(BarberTable::class);
        $this->barberTable = $table;
    }

    public function testCreateEditDeleteBarber()
    {
        // Act
        $barberId = $this->doNewBarber();

        // Assert
        $this->assertResponseRedirects();
        $barber = $this->barberTable->findById($barberId);
        $this->assertNotNull($barber);

        // Arrange
        $newBarberData = [
            'id' => $barberId,
            'name' => 'Александр',
            'surname' => 'Блок',
            'phone' => '+79875992570'
        ];

        // Act
        $this->doUpdateBarber($newBarberData);

        // Assert
        $this->assertResponseRedirects();
        $barber = $this->barberTable->findById($barberId);
        $this->assertEquals($newBarberData, $barber);

        // Act
        $this->doDeleteBarber($barberId);

        // Assert
        $this->assertResponseRedirects();
        $barber = $this->barberTable->findById($barberId);
        $this->assertEmpty($barber);
    }

    public function testShowBarberInfoSnapshot()
    {
        // Arrange
        $barberId = $this->doNewBarber();

        // Act
        $content = $this->doGetBarberInfoContent($barberId);

        // Assert
        $replacements = $this->getTenNearDates();
        $replacements['{ barber_id }'] = (string)$barberId;
        $this->doSnapshotTest(self::SNAPSHOT_INFO_PATH, $content, $replacements);
    }

    public function testShowBarbersListSnapshot()
    {
        // Arrange
        $barberId = $this->doNewBarber();

        // Act
        $content = $this->doGetBarbersListContent();

        // Assert
        $replacements = [
            '{ barber_id }' => (string)$barberId
        ];
        $this->doSnapshotTest(self::SNAPSHOT_LIST_PATH, $content, $replacements);
    }

    protected function shouldUpdateSnapshots(): bool
    {
        return self::UPDATE_SNAPSHOTS;
    }
}
