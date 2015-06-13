<?= \View::forge('nav') ?>
<h2>All Records</h2>
<table>
<tr><td>Id</td><td>Name</td><td>Prize ID</td><th>Method</th></tr>
<?php foreach ($participants as $participant): ?>
<tr>
	<td><?= $participant->id ?></td>
	<td><?= $participant->name ?></td>
	<td><?= $participant->prize ? $participant->prize->id : '' ?></td>
	<td><?= $participant->campaign ?></td>
</tr>
<?php endforeach ?>
</table>
