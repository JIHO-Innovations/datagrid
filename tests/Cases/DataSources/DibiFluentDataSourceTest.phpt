<?php declare(strict_types = 1);

namespace Ublaboo\DataGrid\Tests\Cases\DataSources;

use dibi;
use Dibi\Connection;
use Ublaboo\DataGrid\DataSource\DibiFluentDataSource;
use Ublaboo\DataGrid\Tests\Files\TestingDataGridFactory;

require __DIR__ . '/BaseDataSourceTest.phpt';

// E_NOTICE: Trying to access array offset on value of type null
error_reporting(E_ERROR | E_PARSE);

final class DibiFluentDataSourceTest extends BaseDataSourceTest
{

	private Connection $db;

	public function setUp(): void
	{
		$this->setUpDatabase();
		$this->ds = new DibiFluentDataSource($this->db->select('*')->from('users'), 'id');
		$factory = new TestingDataGridFactory();
		$this->grid = $factory->createTestingDataGrid();
	}

	protected function setUpDatabase(): void
	{
		$this->db = dibi::connect([
			'driver' => 'pdo',
			'dsn' => 'sqlite::memory:',
		]);

		$this->db->query(
			'CREATE TABLE users (
				id      INTEGER      PRIMARY KEY AUTOINCREMENT,
				name    VARCHAR (50),
				age     INTEGER (3),
				address VARCHAR (50)
			);'
		);

		foreach ($this->data as $row) {
			$this->db->insert('users', $row)->execute();
		}
	}

}


$test_case = new DibiFluentDataSourceTest();
$test_case->run();
