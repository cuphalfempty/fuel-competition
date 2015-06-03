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

}
