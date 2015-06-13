<nav>
	<a href="/competition/">Summary</a>
	<a href="/competition/details">Details</a> |
	<a href="/competition/draw/orm/">Draw ORM</a>
	<a href="/competition/draw/orm_lock/">Draw ORM (lock table)</a>
	<a href="/competition/draw/sql/">Draw SQL</a>
	<a href="/competition/draw/sql_lock/">Draw SQL (lock table)</a>
	<a href="/competition/draw/sql_sub/">Draw SQL Sub</a>
</nav>
<?php if ($msg = \Session::get_flash('error')): ?>
	<div class="color: red"><?= $msg ?></div>
<?php elseif ($msg = \Session::get_flash('success')): ?>
	<div class="color: blue"><?= $msg ?></div>
<?php endif ?>
