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

}

/* End of file tasks/competition.php */
