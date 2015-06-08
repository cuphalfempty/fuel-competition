<?php

namespace Competition;

class Controller_Draw extends \Controller_Template
{

	public function action_index($method)
	{
		$fs = \Fieldset::forge('competition');
		$fs->add_model('Competition\Model_Participant');
		$fs->add('submit', null, ['type' => 'submit', 'value' => 'Enter competition']);

		if (\Input::post()) {
			if ($fs->validation()->run()) {
				try {
					$p = Model_Participant::forge([
						'name' => $fs->field('name')->validated(),
							'campaign' => $method,
						]);
					$p->save();
					Model_Prize::{'draw_' . $method}($p);

					\Session::set_flash('success', 'Saved');
					\Response::redirect(\Uri::main());
				}
				catch (\Exception $e) {
					\Session::set_flash('error', $e->getMessage());
				}
			}
			$fs->repopulate();
		}

		$this->template->title = "Competition | Draw $method";
		$this->template->content = \View::forge(
			'draw',
			['fs' => $fs],
			false
		);
	}


	public function action_orm()
	{
		return $this->action_index('orm');
	}


	public function action_orm_transaction()
	{
		return $this->action_index('orm_transaction');
	}


	public function action_orm_lock()
	{
		return $this->action_index('orm_lock');
	}


	public function action_sql()
	{
		return $this->action_index('sql');
	}


	public function action_sql_lock()
	{
		return $this->action_index('sql_lock');
	}


	public function action_sql_sub()
	{
		return $this->action_index('sql_sub');
	}

}
