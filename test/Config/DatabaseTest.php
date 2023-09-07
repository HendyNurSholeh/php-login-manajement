<?php
namespace HendyNurSholeh\Config;

use PDO;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotNull;

class DatabaseTest extends TestCase{
    public function testGetConnection(): void {
        $connection = Database::getConnection();
        assertNotNull($connection);
        assertInstanceOf(PDO::class, $connection);
    }

    public function testGetConnectionSingleton(): void {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}