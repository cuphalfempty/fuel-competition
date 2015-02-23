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

	public static function draw_orm()
	{
		$prizes = Model_Prize::query()
			->where('participant_id', 'IS', null)
			->order_by('id', 'ASC')
			->get();

		return $prizes ? reset($prizes) : null;
	}

	/**
	 * Let MySQL select element
	 */
	public static function draw_better_orm()
	{
		return Model_Prize::query()
			->where('participant_id', 'IS', null)
			->order_by('id', 'ASC')
			->get_one();
	}

	/**
	 * This will not work because ORM inserts automatic table aliases into query
	 */
	public static function draw_orm_lock(Model_Participant $participant)
	{
		\DB::query("LOCK TABLES `" . static::$_table_name . "` WRITE")->execute();
		$prize = Model_Prize::query()
			->where('participant_id', 'IS', null)
			->order_by('id', 'ASC')
			->get_one();
		$prize->participant = $participant;
		$prize->save();
		\DB::query("UNLOCK TABLES")->execute();

		return $prize;
	}

	/**
	 * Let MySQL select element
	 */
	public static function draw_sql(Model_Participant $participant)
	{
		if ( ! $participant->id) {
			throw new Exception('Participant not saved.');
		}

		$sub_query = 'SELECT id FROM `competition__prizes` WHERE participant_id IS NULL ORDER BY RAND() LIMIT 1';
		$query = \DB::query("UPDATE `competition__prizes` SET participant_id = '{$participant->id}' WHERE id = (SELECT * FROM ($sub_query) available)");
		$result = $query->execute();

		return Model_Prize::query()
			->where('participant_id', $participant->id)
			->get_one();
	}

	/**
	 * Let MySQL select element, but lock table before
	 */
	public static function draw_sql_lock(Model_Participant $participant)
	{
		if ( ! $participant->id) {
			throw new Exception('Participant not saved.');
		}

		// WRITE implies READ
		\DB::query("LOCK TABLES `" . static::$_table_name . "` WRITE")->execute();
		$ids = \DB::query("SELECT id FROM `" . static::$_table_name . "` WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1")
			->execute()
			->as_array(null, 'id');
		$id = reset($ids);
		\DB::query("UPDATE `" . static::$_table_name . "` SET participant_id = '{$participant->id}' WHERE id = $id")->execute();
		\DB::query("UNLOCK TABLES")->execute();

		return Model_Prize::query()
			->where('participant_id', $participant->id)
			->get_one();
	}

}
