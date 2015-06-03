<?php

namespace Competition;

class Controller_Summary extends \Controller_Template
{

	public function action_index()
	{
		$fs = \Fieldset::forge('competition_summary');
		$fs->add('confirm', 'Clear all input data.', ['type' => 'checkbox', 'value' => 1], ['required']);
		$fs->add('submit', null, ['type' => 'submit', 'value' => 'Confirm']);

		if (\Input::post()) {
			if ($fs->validation()->run()) {
				Model_Participant::purge();
				\Response::redirect('/competition/summary');
			}
			$fs->repopulate();
		}

		$methods = \DB::select('campaign')
			->from(Model_Participant::table())
			->group_by('campaign')
			->execute()
			->as_array(null, 'campaign');

		$results = [];
		foreach ($methods as $method) {
			$results[$method]['participants'] = Model_Participant::query()
				->where('campaign', $method)
				->get();
			$results[$method]['winners'] = Model_Participant::query()
				->related('prize')
				->where('campaign', $method)
				->where('prize.id', 'IS NOT', null)
				->count();
			$results[$method]['rate'] = $results[$method]['winners'] / count($results[$method]['participants']) * 100;
		}

		$content = \View::forge(
			'index/index',
			['results' => $results]
		);
		$content->set('fs', $fs, false);

		$this->template->title = 'Competition | Summary';
		$this->template->content = $content;
	}

}
