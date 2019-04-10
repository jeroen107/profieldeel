$('.add-to-bag').click(function(e) {

    $.post("/controllers/add2cart", { id: $(this).data('id') })
        .done(function( data ) {
            $('#cart-count').html('(' + data + ') artikelen');
        });


    $('#triggerAdd').trigger('click');
    e.preventDefault();
});


$('.fa-bars').click(function(e) {
    $('.mobileNav').toggleClass('active');
    e.preventDefault();
});