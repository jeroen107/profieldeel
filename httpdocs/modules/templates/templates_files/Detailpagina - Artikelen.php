<?php include("header.php");
global $arginfo;

$merk = db_pdo_fetch_array(db_pdo_select("SELECT * FROM merken WHERE id = :id", array(':id' => $arginfo["merk"])));

?>

<main role="main">


    <section class="header">
        <div class="container">
            <h1>Producten</h1>
        </div>
        <div class="overlay"></div>
    </section>

    <section class="usp bg-grey py-4 d-none d-sm-block">
        <div class="container">
            <div class="row">
                <div class="col-3 item">
                    <b>Voor 15:00 bestel</b>
                    vandaag nog op de post!
                </div>
                <div class="col-3 item">
                    <b>Voor 15:00 bestel</b>
                    vandaag nog op de post!
                </div>
                <div class="col-3 item">
                    <b>Voor 15:00 bestel</b>
                    vandaag nog op de post!
                </div>
                <div class="col-3 item">
                    <b>Voor 15:00 bestel</b>
                    vandaag nog op de post!
                </div>
            </div>
        </div>
    </section>


    <br><br>
    <section>
        <div class="container">
            <a href="/artikelen"><i class="fas fa-long-arrow-alt-left"></i> Terug naar artikelen</a>
        </div>
    </section>
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
                        <a href="" class="add-to-bag" data-id='12'>Voeg toe aan winkelwagen <i class="fas fa-shopping-cart"></i></a>


                        <p class="brand-info mt-5">
                            <?php echo output($merk["omschrijving"]);?>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <section class="title  py-5 my-4">
        <div class="container">
            <h2>Andere</h2>
            <h3><span>Mooie artikelen</span></h3>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <a  href="/artikel.html" class="artikel">
                        <div class="img">
                            <div class="inner" style="background-image:url(@@img)"></div>
                        </div>
                        <span class="brand">The Zoo</span>
                        <p class="price">&euro; 49,95</p>
                        <h2>Een vrije lange naam</h2>
                    </a>
                </div>

                <div class="col-lg-3 col-md-4 col-6">
                    <a  href="/artikel.html" class="artikel">
                        <div class="img">
                            <div class="inner" style="background-image:url(@@img)"></div>
                        </div>
                        <span class="brand">The Zoo</span>
                        <p class="price">&euro; 49,95</p>
                        <h2>Een vrije lange naam</h2>
                    </a>
                </div>

                <div class="col-lg-3 col-md-4 col-6">
                    <a  href="/artikel.html" class="artikel">
                        <div class="img">
                            <div class="inner" style="background-image:url(@@img)"></div>
                        </div>
                        <span class="brand">The Zoo</span>
                        <p class="price">&euro; 49,95</p>
                        <h2>Een vrije lange naam</h2>
                    </a>
                </div>

                <div class="col-lg-3 col-md-4 col-6">
                    <a  href="/artikel.html" class="artikel">
                        <div class="img">
                            <div class="inner" style="background-image:url(@@img)"></div>
                        </div>
                        <span class="brand">The Zoo</span>
                        <p class="price">&euro; 49,95</p>
                        <h2>Een vrije lange naam</h2>
                    </a>
                </div>

            </div>
        </div>
    </section>



    <section class="title  py-5 my-4">
        <div class="container">
            <h2>Laat je inspireren</h2>
            <h3><span>Kies een onderwerp</span></h3>
        </div>
    </section>
    <section class="themes">
        <div class="container">
            <div class="box">
                <div class="left">
                    <a href="" class="line" style="background-image:url('https://images.pexels.com/photos/584399/living-room-couch-interior-room-584399.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');">
                        <span>Slaapkamer <p>Bekijk de producten</p></span>
                    </a>
                </div>
                <div class="right">
                    <div class="small">
                        <a href="" class="line" style="background-image:url('https://images.pexels.com/photos/584399/living-room-couch-interior-room-584399.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');">
                            <span>Slaapkamer</span>
                        </a>
                    </div>
                    <div class="small">
                        <a href="" class="line" style="background-image:url('https://images.pexels.com/photos/584399/living-room-couch-interior-room-584399.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');">
                            <span>Slaapkamer</span>
                        </a>
                    </div>
                    <div class="small">
                        <a href="" class="line" style="background-image:url('https://images.pexels.com/photos/584399/living-room-couch-interior-room-584399.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');">
                            <span>Slaapkamer</span>
                        </a>
                    </div>
                    <div class="small">
                        <a href="" class="line" style="background-image:url('https://images.pexels.com/photos/584399/living-room-couch-interior-room-584399.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');">
                            <span>Slaapkamer</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>



</main>



<?php include("footer.php")?>