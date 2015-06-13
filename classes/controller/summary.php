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

		$methods = \DB::select(['pa.campaign', 'method'], [\DB::expr('COUNT(*)'), 'total'], [\DB::expr('COUNT(IF(pr.id IS NULL, 1, NULL))'), 'errors'])
			->from([Model_Participant::table(), 'pa'])
			->join([Model_Prize::table(), 'pr'], 'left')
			->on('pr.participant_id', '=', 'pa.id')
			->group_by('method')
			->order_by('errors', 'desc')
			->execute()
			->as_array('method');

		$content = \View::forge(
			'index/index',
			['methods' => $methods]
		);
		$content->set('fs', $fs, false);

		$this->template->title = 'Competition | Summary';
		$this->template->content = $content;
	}


	public function action_details()
	{
		$participants = Model_Participant::query()
			->related('prize')
			->order_by('created_at', 'asc')
			->get();

		$content = \View::forge(
			'index/details',
			['participants' => $participants]
		);
		$this->template->title = 'Competition | Details';
		$this->template->content = $content;
	}


}
