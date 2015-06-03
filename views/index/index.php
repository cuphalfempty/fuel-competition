<?= \View::forge('nav') ?>
<h2>Success Ratio</h2>
<table>
<tr><th>Method</th><th>Ratio</th><th>Winners</th><th>Total</th></tr>
<?php foreach ($results as $method => $result): ?>
	<tr>
		<td><?= $method ?></td>
		<td><?= sprintf('%.2f', $result['rate']) ?>%</td>
		<td><?= $result['winners'] ?></td>
		<td><?= count($result['participants']) ?></td>
	</tr>
<?php endforeach ?>
</table>
<h2>Participants</h2>
<?php foreach ($results as $method => $result): ?>
	<h2><?= $method ?></h2>
	<table>
	<tr><td>Id</td><td>Name</td><td>Voucher</td><th>Prize id</th></tr>
	<?php foreach ($result['participants'] as $participant): ?>
	<tr>
		<td><?= $participant->id ?></td>
		<td><?= $participant->name ?></td>
		<td><?= $participant->prize ? $participant->prize->title : '' ?></td>
		<td><?= $participant->prize ? $participant->prize->id : '' ?></td>
	</tr>
	<?php endforeach ?>
	</table>
<?php endforeach ?>
<h3>Options</h3>
<div>
	<?= $fs ?>
</div>
