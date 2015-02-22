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

	public function draw()
	{
		// clear table
		$query = \DB::query("UPDATE `competition__prizes` SET participant_id = NULL");
		$query->execute();

		$max = \DB::query("SELECT COUNT(*) AS max FROM `competition__prizes`")->execute()->as_array(null, 'max');
		$max = reset($max);

		for ($i = 1; $i <= $max; ++$i) {
			$sub_query = 'SELECT id FROM `competition__prizes` WHERE participant_id IS NULL ORDER BY RAND() LIMIT 1';
			$query = \DB::query("UPDATE `competition__prizes` SET participant_id = '$i' WHERE id = (SELECT * FROM ($sub_query) available)");
			$result = $query->execute();
		}
		$prizes = Model_Prize::query()->get();
		foreach ($prizes as $prize) {
			\Cli::write(sprintf('%d %d %s', $prize->id, $prize->participant_id, $prize->voucher));
		}
	}

}

/* End of file tasks/competition.php */
