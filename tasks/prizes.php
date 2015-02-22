<?php

namespace Fuel\Tasks;

use \Competition\Model_Prize;

class Prizes
{

	public static function run()
	{
		echo <<<HELP
php.fuel oil r competition:populate_vouchers
HELP;
	}

	public function populate()
	{
		for ($i = 1; $i <= 1000; ++$i) {
			$prize = Model_Prize::forge();
			$prize->title = sprintf('Your prize %04d', $i);
			$prize->voucher = md5(time());
			$prize->save();
		}
	}

	/**
	 * Remove all sing-ups
	 */
	public function purge()
	{
		$query = \DB::query("UPDATE `competition__prizes` SET participant_id = NULL");
		$query->execute();

		$query = \DB::query("DELETE FROM `competition__participants`");
		$query->execute();
	}

}

/* End of file tasks/competition.php */
