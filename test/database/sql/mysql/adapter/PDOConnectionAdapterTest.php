<?php
use chilimatic\lib\database\sql\mysql\connection\adapter\PDOConnectionAdapter;
use chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings;

/**
 *
 * @author j
 * Date: 9/28/15
 * Time: 9:24 PM
 *
 * File: PDOMySQLAdapterTest.php
 */

class PDOConnectionAdapterTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        if (!extension_loaded('mysqli')) {
            $this->markTestSkipped('The MySQLi extension is not available.');
        }
    }

    /**
     * @return array
     */
    public function readTheConfig()
    {
        static $config;
        if (!$config) {
            $config = parse_ini_file(__DIR__ .'/../../../../testdata/mysql/connection.ini', true);
        }

        return $config;
    }


    /**
     * @return MySQLConnectionSettings
     */
    public function prepareAdapterConnection() {
        $config = $this->readTheConfig();

        return new MySQLConnectionSettings(
            $config['database']['mysql.host'],
            $config['database']['mysql.username'],
            $config['database']['mysql.password'],
            $config['database']['mysql.database']
        );
    }

    /**
     * @test
     */
    public function checkIfItImplementsTheIDatbaseConnectionAdapterInterface() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf('\chilimatic\lib\database\connection\IDatabaseConnectionAdapter', $adapter);
    }

    /**
     * @test
     */
    public function checkIfItExtendsTheAbstractSqlConnectionAdapterClass() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\AbstractSqlConnectionAdapter', $adapter);
    }

    /**
     * @test
     */
    public function checkIfMysqlAdapterCanPingTheDatabase() {

        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertTrue($adapter->ping());
    }

    /**
     * @test
     */
    public function checkIfMysqlAdapterCanQueryTheDatabase() {

        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }


        $stmt = $adapter->query("SHOW DATABASES");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertArrayHasKey('Database', $result[0]);
    }

    /**
     * @test
     */
    public function checkIfMysqlAdapterReturnsPreparedStatement() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $stmt = $adapter->prepare("SELECT * FROM test_table1 WHERE aString = :string AND aDate = :date");

        $this->isInstanceOf('\PdoStatement', $stmt);
    }

    /**
     * @test
     */
    public function checkIfMysqlAdapterReturnsConnectionString() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $dsn = $adapter->getConnectionString();

        $this->isInstanceOf('mysql:127.0.0.1', $dsn);
    }

    /**
     * @test
     */
    public function checkIfSyntaxErrorCodeOfMysqlIsReturned() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $adapter->execute('SELECT FROM test_table1');
        $this->assertEquals(42000, $adapter->getErrorCode());
    }

    /**
     * @test
     */
    public function checkIfErrorInfoIsReturnedAndSQLSyntaxErrorIsAvailable() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $adapter->execute('SELECT FROM test_table1');
        $this->assertEquals(42000, $adapter->getErrorInfo()[0]);
    }

    /**
     * @test
     */
    public function checkIfErrorInfoIsReturnedAndSQLDriverErrorIsAvailable() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $adapter->execute('SELECT FROM test_table1');
        $this->assertEquals(1064, $adapter->getErrorInfo()[1]);
    }

    /**
     * @test
     */
    public function checkIfErrorInfoIsReturnedAndSQLSyntaxErrorMessageIsAvailable() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $adapter->execute('SELECT FROM test_table1');
        $this->assertEquals("You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM test_table1' at line 1", $adapter->getErrorInfo()[2]);
    }

    /**
     * @test
     */
    public function checkIfExecuteIsWorkingWithThePdoAdapter() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $result = $adapter->execute('SELECT * FROM test_table1');
        $this->assertEquals(0, $result);
    }

    /**
     * @test
     */
    public function checkIfAdapterCanStartTransaction() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $result = $adapter->beginTransaction();

        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function checkIfAdapterCanCommitTransaction() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $adapter->beginTransaction();
        $result = $adapter->commit();

        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function checkIfAdapterCanRollbackTransaction() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $adapter->beginTransaction();
        $result = $adapter->rollback();

        $this->assertEquals(true, $result);
    }

    /**
     * @test
     */
    public function checkIfConnectionInTransaction() {
        try {
            $adapter = new PDOConnectionAdapter($this->prepareAdapterConnection());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $adapter->beginTransaction();
        $result = $adapter->inTransaction();
        $adapter->rollback();

        $this->assertEquals(true, $result);
    }

}