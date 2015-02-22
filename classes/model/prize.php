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
			->where('participant_id', 'IS', $participant_id)
			->get_one();
	}

}
