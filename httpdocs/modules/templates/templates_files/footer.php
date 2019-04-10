
</main>

<footer class="mt-5">
    <div class="container">
        <hr>
        <div class="row">
            <div class="col-lg-5 pt-5 pb-5">
                Boompjesstraat 6<br>
                3291AB Strijen<br>
                info@inrichterijvantoen.nl<br>
                06 53 96 01 59
            </div>
            <div class="col-lg-2 col-6">
                <img class="img-fluid" src="https://inrichterijvantoen.nl/img/zwart_transparant.png">
            </div>
            <div class="col-lg-5"></div>
        </div>
    </div>
</footer>
<div id="cart-added">
    <div class="content">
        <div class="row">
            <div class="col-4"><img src="https://inrichterijvantoen.nl/slir/w900/<?php echo output($arginfo["foto"]);?>" class="img-fluid"></div>
            <div class="col-8">
                <h2>Toegevoegd aan je winkelwagen</h2>
                <div class="buttons">
                    <a href="" class="btn">Verder winkelen <i class="fas fa-long-arrow-alt-right"></i></a>
                    <a href="" class="btn">Naar betalen <i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<a id="triggerAdd" data-fancybox="added" data-src="#cart-added" href="javascript:;"></a>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="/assets/js/init.js"></script>

</body>

</html>
