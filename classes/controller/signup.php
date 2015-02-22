<?php

namespace Competition;

class Controller_Signup extends \Controller_Template
{

	public function action_draw()
	{
		$method = \Input::get('method', 'orm');
		$participant = Model_Participant::forge([
			'name' => $method,
		]);
		$participant->save();

		if ($prize = Model_Prize::{'draw_'.$method}($participant)) {
		 $participant->prize = $prize;
		 $participant->save();
		}

		$this->template->title = 'Competition | Signup';
		$this->template->content = \View::forge(
			'competition/index',
			['participant' => $participant]
		);
	}

	public function action_summary()
	{
		$methods = \DB::select('name')
			->from(Model_Participant::table())
			->group_by('name')
			->execute()
			->as_array(null, 'name');

		$results = [];
		foreach ($methods as $method) {
			$results[$method]['participants'] = Model_Participant::query()
				->where('name', $method)
				->get();
			$results[$method]['winners'] = Model_Participant::query()
				->related('prize')
				->where('name', $method)
				->where('prize.id', 'IS NOT', null)
				->count();
			$results[$method]['rate'] = $results[$method]['winners'] / count($results[$method]['participants']) * 100;
		}

		$this->template->title = 'Competition | Summary';
		$this->template->content = \View::forge(
			'competition/summary',
			['results' => $results]
		);
	}
}
