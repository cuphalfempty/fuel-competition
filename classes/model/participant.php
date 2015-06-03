<?php

namespace Competition;

class Model_Participant extends \Orm\Model
{

	protected static $_table_name = 'competition__participants';

	protected static $_properties = [
		'id',
		'name' => [
			'form' => [
				'label' => 'Your Name',
			],
			'validation' => ['trim', 'required'],
		],
		'campaign' => [
			'skip' => true,
		],
		'created_at' => [
			'skip' => true,
		],
		'updated_at' => [
			'skip' => true,
		],
	];

	protected static $_has_one = [
		'prize' => [
			'key_from' => 'id',
			'model_to' => '\Competition\Model_Prize',
			'key_to' => 'participant_id',
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


	public static function purge()
	{
		$query = \DB::query("UPDATE `competition__prizes` SET participant_id = NULL");
		$query->execute();
		$query = \DB::query("DELETE FROM `" . static::table() . "`");
		$query->execute();
	}

}
