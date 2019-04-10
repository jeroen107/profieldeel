<?php
		include("header.php");
		global $arginfo;
		?>
		<div class="container">
				<h1>Hi Sexy</h1>
				<h2>This seems to be a new template. Add some code and love</h2>
				<br><br>
				<table class="table">
					<?php
						if(is_array($arginfo)){ foreach($arginfo as $k => $v){ ?>
					<tr>
						<td><?php echo $k?></td><td><?php echo $v?></td>
					</tr>
				<?php }}?>
				</table>
		</div>
		<?php include("footer.php")?>