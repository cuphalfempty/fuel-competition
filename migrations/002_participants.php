<?php

namespace Fuel\Migrations;

class Participants
{

	protected $_table_name = 'competition__participants';

	function up()
	{
		try {
			\DB::start_transaction();

			\DBUtil::create_table(
				$this->_table_name,
				[
					'id' => ['type' => 'int', 'auto_increment' => true, 'unsigned' => true],
					'name' => ['type' => 'varchar', 'constraint' => 64],
					'created_at' => ['type' => 'int', 'unsigned' => true],
					'updated_at' => ['type' => 'int', 'unsigned' => true, 'null' => true],
				],
				['id']
			);
			\DBUtil::create_index('competition__prizes', 'participant_id');
			\DBUtil::add_foreign_key('competition__prizes', array(
				'constraint' => 'fk_participants',
				'key' => 'participant_id',
				'reference' => array(
					'table' => 'competition__participants',
					'column' => 'id',
				),
				'on_update' => 'CASCADE',
				'on_delete' => 'RESTRICT',
			));

			\DB::commit_transaction();
		}
		catch (\Exception $e) {
			\DB::rollback_transaction();
			\Cli::error($e->getMessage());
			return false;
		}
	}

	function down()
	{
		try {
			\DB::start_transaction();
			\DBUtil::drop_foreign_key('competition__participants', 'fk_participants');
			\DBUtil::drop_table($this->_table_name);
			\DB::commit_transaction();
		}
		catch (\Exception $e) {
			\DB::rollback_transaction();
			\Cli::error($e->getMessage());
			return false;
		}
	}
}

