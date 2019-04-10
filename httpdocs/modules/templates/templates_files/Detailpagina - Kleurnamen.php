<?php
		include("header.php");
		global $arginfo;
		?>
		<div class="container">
				<h1>TEST</h1>
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
                <div class="col-lg-5 text-center">
                    <img src="https://inrichterijvantoen.nl/slir/w900/<?php echo output($arginfo["foto"]);?>" class="img-fluid">
                </div>
                <div class="col-lg-7">
                    <div class="artikel-info">
                        <a href="/<?php echo pages_url("merken",$merk["id"],$merk["naam"])?>" class="brand"><?php echo $merk["naam"]?></a>
                        <p class="price"><?php echo price($arginfo["prijs"]);?></p>
                        <h2><?php echo output($arginfo["naam"]);?></h2>
                        <div class="info">
                            <p><?php echo output($arginfo["omschrijving"]);?>
                            </p>
                        </div>
                        <br>
                        <div class="info">
                            <p>aOwl Bluetooth speaker in zwart kunststof
                                70 mm speaker - intens helder geluid
                                volume aan/uit knop aan achterzijde - de ogen zijn de speakers</p>
                            </p>
                            <div class="select-container">
                                <i class="fas fa-chevron-down"></i>
                                <select>
                                    <option>Kies je toepassing</option>
                                </select>
                            </div>

                            <div class="select-container">
                                <i class="fas fa-chevron-down"></i>
                                <select>
                                    <option>Kies je toepassing</option>
                                </select>
                            </div>

                            <div class="select-container">
                                <i class="fas fa-chevron-down"></i>
                                <select>
                                    <option>Kies je toepassing</option>
                                </select>
                            </div>


                        </div>
                        <br>
                        <a href="" class="add-to-bag" data-id='12'>Voeg toe aan winkelwagen <i class="fas fa-shopping-cart"></i></a>


                        <p class="brand-info mt-5">
                            Farrow & Ball verf wordt gemaakt met ruime hoeveelheden natuurlijke pigmenten en de mooiste producten als bindmiddel, dit geeft de verf zijn ongeëvenaarde kleurdiepte en prachtige werking van het licht. Farrow & Ball verf laat zich door
                            het gebruik van de natuurlijke pigmenten niet mengen op de mengmachine maar wordt ambachtelijk gemaakt in Wimborne Dorset, UK.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>



		<?php include("footer.php")?>