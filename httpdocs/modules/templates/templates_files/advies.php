<?php
		include("header.php");
		global $arginfo;
		?>

    <section class="services">



<?php
$data = db_pdo_select_all('SELECT * FROM advies');
$alt = '';
foreach ($data as $k => $v){

    if ($alt == ''){
        $alt = 'alt';
    }else{
        $alt = '';
    }
    //$url = pages_url('artikelen',$v["id"],$v["naam"].'-'.$v["id"]);
    ?>
    
    <div class="item <?php echo $alt?>">
        <div class="container">
            <div class="row">
                <div class="text">

                    <h2>Kleuradvies</h2>
                    <h3>Kleuradvies</h3>
                    <p>
                        We zijn dealer van Farrow & Ball verf en behang voor de Hoeksche Waard. Daarnaast verkopen we krijtverf, wassen en toebehoren van Abbondanza.
                        We geven kleuradvies in onze winkel, waarbij we jouw wensen inventariseren, welke sfeer je belangrijk vindt en hoe je thuis wilt leven.
                        Daarbij kan je eventueel stofstalen, foto's e.d. uit je interieur meebrengen zodat we een zo goed mogelijk beeld krijgen.
                        Van Farrow & Ball hebben we sample pots in de winkel zodat je thuis in alle rust de kleuren kunt uitproberen.
                    </p>
                </div>
                <div class="image" style="background-image:url('/slir/w800<?php echo $v["foto"]?>')">
                    <span class="line"></div>
            </div>
        </div>
    </div>
    <?php }?>
    </section>
		<?php include("footer.php")?>