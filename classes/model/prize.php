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

}
