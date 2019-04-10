<?php
		include("header.php");
		global $arginfo;
		?>

    <section class="header">
        <div class="container">
            <h1>Winkelwagen</h1>
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





    <section class="cart my-5">

        <div class="container">
            <div class="steps">
                <div class="step step1">
                    <h2>1. Factuuradres</h2>

                    <div class="input input2">
                        <input placeholder="Voornaam">
                        <input placeholder="Achternaam">
                    </div>

                    <div class="input">
                        <input class="zipcode" placeholder="Postcode">
                        <input class="number" placeholder="Nr.">
                    </div>
                    <div class="input">
                        <input class="email" placeholder="E-mail">
                    </div>

                    <div class="adres">
                        <i class="fas fa-home"></i>
                        <div>
                            Elzenlaan 4<br>
                            3286XE Klaaswaal
                        </div>
                    </div>


                    <br><br>
                    <h2>Bezorgadres</h2>
                    <div class="input input2">
                        <input placeholder="Voornaam">
                        <input placeholder="Achternaam">
                    </div>

                    <div class="input">
                        <input class="zipcode" placeholder="Postcode">
                        <input class="number" placeholder="Nr.">
                    </div>
                    <div class="input">
                        <input class="email" placeholder="E-mail">
                    </div>

                    <div class="adres">
                        <i class="fas fa-home"></i>
                        <div>
                            Elzenlaan 4<br>
                            3286XE Klaaswaal
                        </div>
                    </div>
                </div>
                <div class="step step2">
                    <h2>2. Betaalmethode</h2>
                    <div class="select-container">
                        <i class="fas fa-chevron-down"></i>
                        <select class="banks">
                            <option>Rabobank</option>
                        </select>
                    </div>
                    <br>
                    <img width="60%" src="https://www.artisklas-haarlem.nl/wp-content/uploads/2016/11/ideal-logo-veilig-online-betalen.jpg">
                    <br><br>
                    <hr><br>
                    <h2>3. Aflevermethode</h2>
                    <div class="select-container">
                        <i class="fas fa-chevron-down"></i>
                        <select class="banks">
                            <option>Normaal pakket &euro; 3,95</option>
                            <option>Ophalen in de winkel</option>
                        </select>
                    </div>
                    <br>



                </div>
                <div class="step step3">
                    <h2>4. Je bestelling</h2>

                    <div class="order">
                        <?php foreach ($_SESSION["cart"] as $k => $v){
                            $artikelen = db_pdo_fetch_array(db_pdo_select("SELECT * FROM artikelen WHERE id = '".$v."'"));
                           
                            ?>
                        <div class="line">
                            <div class="img"><img src="https://inrichterijvantoen.nl/slir/w300/<?php echo $artikelen["foto"]?>"></div>
                            <div class="title">
                                <?php echo $artikelen["naam"]?>
                                <div class="prijs">1x <?php echo prijs($artikelen["prijs"])?></div>
                                <a href="/controllers/cart/remove/<?php echo $k?>">Verwijderen</a>
                            </div>
                        </div>
                       <?php }?>
                    </div>
                    <br>
                    <a href="" class="btn btn-green">Afrekenen (&euro; 149,68)</a>
                </div>
            </div>
        </div>
    </section>

<?php include("footer.php")?>