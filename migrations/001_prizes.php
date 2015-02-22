<?php

namespace Fuel\Migrations;

class Prizes
{

	protected $_table_name = 'competition__prizes';

	function up()
	{
		try {
			\DB::start_transaction();

			\DBUtil::create_table(
				$this->_table_name,
				[
					'id' => ['type' => 'int', 'auto_increment' => true, 'unsigned' => true],
					'title' => ['type' => 'varchar', 'constraint' => 64],
					'voucher' => ['type' => 'varchar', 'constraint' => 64],
					'participant_id' => ['type' => 'int', 'unsigned' => true, 'null' => true],
					'created_at' => ['type' => 'int', 'unsigned' => true],
					'updated_at' => ['type' => 'int', 'unsigned' => true, 'null' => true],
				],
				['id']
			);

			\DB::commit_transaction();
		}
		catch (\Exception $e) {
			\DB::rollback_transaction();
			\Cli::error($e->getMessage());
			\Cli::error($e->getFile() . ':' . $e->getLine());
			return false;
		}
	}

	function down()
	{
		try {
			\DB::start_transaction();
			\DBUtil::drop_table($this->_table_name);
			\DB::commit_transaction();
		}
		catch (\Exception $e) {
			\DB::rollback_transaction();
			\Cli::error($e->getMessage());
			\Cli::error($e->getFile() . ':' . $e->getLine());
			return false;
		}
	}
}

