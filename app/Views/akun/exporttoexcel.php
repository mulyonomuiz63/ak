<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data Akun.xlsx");
?>
<!DOCTYPE html>
<html>

<head>
	<title>Export Data Ke Excel </title>
</head>

<body>
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th style="text-align: center;">kdakun</th>
					<th style="text-align: left;">nmakun</th>
					<th style="text-align: center;">level</th>
					<th style="text-align: center;">saldonormal</th>
					<th style="text-align: center;">idperusahaan</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no = 1;
				foreach ($rsakun->getResult() as $row) { ?>

					<tr>
						<td style="text-align: center;"><?= $row->kdakun; ?></td>
						<td style="text-align: left;"><?= $row->nmakun; ?></td>
						<td style="text-align: center;"><?= $row->level; ?></td>
						<td style="text-align: center;"><?= $row->saldonormal; ?></td>
						<td style="text-align: center;"><?= $row->idperusahaan; ?></td>
					</tr>

				<?php }
				?>
			</tbody>
		</table>
	</div>
</body>

</html>