<?= \View::forge('nav') ?>
<h2>Success Ratio</h2>
<table>
<tr><th>Method</th><th>Total records</th><th>Errors</th><th>Errors %</th></tr>
<?php foreach ($methods as $method => $data): ?>
	<tr>
		<td><?= $method ?></td>
		<td><?= $data['total'] ?></td>
		<td><?= $data['errors'] ?></td>
		<td><?= sprintf('%.2f', $data['errors'] / $data['total'] * 100) ?>%</td>
	</tr>
<?php endforeach ?>
</table>
<h3>Options</h3>
<div>
	<?= $fs ?>
</div>
