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


	/**
	 * This will not work because ORM inserts automatic table aliases into query
	 */
	public function action_orm_lock()
	{
		$fs = \Fieldset::forge('competition');
		$fs->add_model('Competition\Model_Participant');
		$fs->add('submit', null, ['type' => 'submit', 'value' => 'Enter competition']);

		if (\Input::post()) {
			if ($fs->validation()->run()) {
				try {
					\DB::query("LOCK TABLES `" . Model_Prize::table() . "` WRITE")->execute();
					$p = Model_Participant::forge([
						'name' => $fs->field('name')->validated(),
							'campaign' => 'orm_lock',
						]);
					$p->prize = Model_Prize::query()
						->where('participant_id', 'IS', null)
						->order_by('id', 'ASC')
						->get_one();
					$p->save();
					\DB::query("UNLOCK TABLES")->execute();

					\Session::set_flash('success', 'Saved');
					\Response::redirect('/competition/draw/orm_lock');
				}
				catch (\Exception $e) {
					\Session::set_flash('error', $e->getMessage());
				}
			}
			$fs->repopulate();
		}

		$this->template->title = 'Competition | Draw ORM';
		$this->template->content = \View::forge(
			'draw',
			['fs' => $fs],
			false
		);
	}


	public function action_sql()
	{
		$fs = \Fieldset::forge('competition');
		$fs->add_model('Competition\Model_Participant');
		$fs->add('submit', null, ['type' => 'submit', 'value' => 'Enter competition']);

		if (\Input::post()) {
			if ($fs->validation()->run()) {
				try {
					$p = Model_Participant::forge([
						'name' => $fs->field('name')->validated(),
							'campaign' => 'sql',
						]);
					$p->save();

					$sub_query = 'SELECT id FROM `competition__prizes` WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1';
					$query = \DB::query("UPDATE `competition__prizes` SET participant_id = '{$p->id}' WHERE id = (SELECT * FROM ($sub_query) available)");
					$result = $query->execute();

					\Session::set_flash('success', 'Saved');
					\Response::redirect('/competition/draw/sql');
				}
				catch (\Exception $e) {
					\Session::set_flash('error', $e->getMessage());
				}
			}
			$fs->repopulate();
		}

		$this->template->title = 'Competition | Draw ORM';
		$this->template->content = \View::forge(
			'draw',
			['fs' => $fs],
			false
		);
	}


	public function action_sql_lock()
	{
		$fs = \Fieldset::forge('competition');
		$fs->add_model('Competition\Model_Participant');
		$fs->add('submit', null, ['type' => 'submit', 'value' => 'Enter competition']);

		if (\Input::post()) {
			if ($fs->validation()->run()) {
				try {
					$p = Model_Participant::forge([
						'name' => $fs->field('name')->validated(),
							'campaign' => 'sql_lock',
						]);
					$p->save();

					// WRITE implies READ
					\DB::query("LOCK TABLES `" . Model_Prize::table() . "` WRITE")->execute();
					$id = \DB::query("SELECT id FROM `" . Model_Prize::table() . "` WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1")
						->execute()
						->get('id', null);
					\DB::query("UPDATE `" . Model_Prize::table() . "` SET participant_id = '{$p->id}' WHERE id = $id")->execute();
					\DB::query("UNLOCK TABLES")->execute();

					\Session::set_flash('success', 'Saved');
					\Response::redirect('/competition/draw/sql_lock');
				}
				catch (\Exception $e) {
					\Session::set_flash('error', $e->getMessage());
				}
			}
			$fs->repopulate();
		}

		$this->template->title = 'Competition | Draw ORM';
		$this->template->content = \View::forge(
			'draw',
			['fs' => $fs],
			false
		);
	}


	public function action_sql_sub()
	{
		$fs = \Fieldset::forge('competition');
		$fs->add_model('Competition\Model_Participant');
		$fs->add('submit', null, ['type' => 'submit', 'value' => 'Enter competition']);

		if (\Input::post()) {
			if ($fs->validation()->run()) {
				try {
					$p = Model_Participant::forge([
						'name' => $fs->field('name')->validated(),
							'campaign' => 'sql_sub',
						]);
					$p->save();

					// WRITE implies READ
					\DB::query("LOCK TABLES `" . Model_Prize::table() . "` WRITE, " . Model_Prize::table() . " AS c_p READ")->execute();
					\DB::query("UPDATE `" . Model_Prize::table() . "`"
						. " SET participant_id = '{$p->id}' "
						. " WHERE id = (SELECT * FROM ("
						. " SELECT id FROM " . Model_Prize::table() . " AS c_p WHERE participant_id IS NULL ORDER BY id ASC LIMIT 1"
						. " ) available)")
						->execute();
					\DB::query("UNLOCK TABLES")->execute();

					\Session::set_flash('success', 'Saved');
					\Response::redirect('/competition/draw/sql_lock');
				}
				catch (\Exception $e) {
					\Session::set_flash('error', $e->getMessage());
				}
			}
			$fs->repopulate();
		}

		$this->template->title = 'Competition | Draw ORM';
		$this->template->content = \View::forge(
			'draw',
			['fs' => $fs],
			false
		);
	}

}
