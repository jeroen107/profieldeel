<?php include("header.php")?>

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


    <section class="my-5">
        <div class="container">

            <div class="row">

                <?php
                $data = db_pdo_select_all('SELECT * FROM artikelen');

                foreach ($data as $k => $v){
                    $url = pages_url('artikelen',$v["id"],$v["naam"].'-'.$v["id"]);
                    ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a  href="/<?php echo $url?>" class="artikel">
                            <div class="img">
                                <div class="inner" style="background-image:url(https://inrichterijvantoen.nl/slir/w900<?php echo $v["foto"]?>)"></div>
                            </div>
                            <span class="brand">The Zoo</span>
                            <p class="price">&euro; 49,95</p>
                            <h2><?php echo $v["naam"]?></h2>
                        </a>
                    </div>


                <?php }?>



            </div>

        </div>
    </section>




</main>

<?php include("footer.php")?>
