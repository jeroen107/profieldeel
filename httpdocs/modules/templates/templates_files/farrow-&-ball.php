<?php
		include("header.php");
		global $arginfo;
		?>test
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

    <section class="my-5">
        <div class="container">

            <div class="row">

                <?php
                $data = db_pdo_select_all('SELECT * FROM kleurnamen');

                foreach ($data as $k => $v){
                    $url = pages_url('kleurnamen',$v["id"],$v["naam"].'-'.$v["id"]);
                    ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a  href="/<?php echo $url?>" class="artikel">
                            <div class="img">
                                <div class="inner" style="background-image:url(https://inrichterijvantoen.nl/slir/w900<?php echo $v["foto"]?>)"></div>
                            </div>
                            <span class="brand">No. 212</span>

                            <h2><?php echo $v["naam"]?></h2>
                        </a>
                    </div>


                <?php }?>



            </div>

        </div>
    </section>

		<?php include("footer.php")?>