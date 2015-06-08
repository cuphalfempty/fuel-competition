<?php

namespace Competition;

class Model_Prize extends \Orm\Model
{

	protected static $_table_name = 'competition__prizes';

	protected static $_properties = [
		'id',
		'title',
		'voucher',
		'participant_id',
		'created_at',
		'updated_at',
	];

	protected static $_belongs_to = [
		'participant' => [
			'key_from' => 'participant_id',
			'model_to' => '\Competition\Model_Participant',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		],
	];

	protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array(
			'property' => 'created_at',
		),
		'Orm\\Observer_UpdatedAt' => array(
			'property' => 'updated_at',
		),
	);


	public static function generate_prizes()
	{
		for ($i = 1; $i <= 1000; ++$i) {
			$prize = Model_Prize::forge();
			$prize->title = sprintf('Your prize %04d', $i);
			$prize->voucher = md5(time());
			$prize->save();
		}
	}


	public static function draw_orm(Model_Participant $p)
	{
		$p->prize = Model_Prize::query()
			->where('participant_id', 'IS', null)
			->order_by('id', 'ASC')
			->get_one();
		$p->save();
		return $p;
	}


	public static function draw_orm_transaction(Model_Participant $p)
	{
		\DB::start_transaction();
		$result = static::draw_orm($p);
		\DB::commit_transaction();
		return $result;
	}


	/**
	 * This will not work because ORM inserts automatic table aliases into query
	 *
	 * @param Model_Participant $p
	 * @return void
	 */
	public static function draw_orm_lock(Model_Participant $p)
	{
		\DB::query("LOCK TABLES `" . static::table() . "` WRITE")->execute();
		$p->prize = Model_Prize::query()
			->where('participant_id', 'IS', null)
			->order_by('id', 'ASC')
			->get_one();
		$p->save();
		\DB::query("UNLOCK TABLES")->execute();
	}


	/**
	 * @param Model_Participant $p
	 * @return void
	 */
	public static function draw_sql(Model_Participant $p)
	{
		$sub_query = "SELECT id FROM `" . static::table() . "` WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1";
		$query = \DB::query("UPDATE `" . static::table() . "` SET participant_id = '{$p->id}' WHERE id = (SELECT * FROM ($sub_query) available)");
		$result = $query->execute();
	}


	/**
	 * @param Model_Participant $p
	 * @return void
	 */
	public static function draw_sql_lock(Model_Participant $p)
	{
		// WRITE implies READ
		\DB::query("LOCK TABLES `" . static::table() . "` WRITE")->execute();
		$id = \DB::query("SELECT id FROM `" . static::table() . "` WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1")
			->execute()
			->get('id', null);
		\DB::query("UPDATE `" . static::table() . "` SET participant_id = '{$p->id}' WHERE id = $id")->execute();
		\DB::query("UNLOCK TABLES")->execute();
	}


	/**
	 * @param Model_Participant $p
	 * @return void
	 */
	public static function draw_sql_sub(Model_Participant $p)
	{
		// WRITE implies READ
		\DB::query("LOCK TABLES `" . Model_Prize::table() . "` WRITE, " . Model_Prize::table() . " AS c_p READ")->execute();
		\DB::query("UPDATE `" . Model_Prize::table() . "`"
			. " SET participant_id = '{$p->id}' "
			. " WHERE id = (SELECT * FROM ("
			. " SELECT id FROM " . Model_Prize::table() . " AS c_p WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1"
			. " ) available)")
			->execute();
		\DB::query("UNLOCK TABLES")->execute();
	}

}
