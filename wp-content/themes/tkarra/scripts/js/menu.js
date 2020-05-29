jQuery(document).ready(function(){
    let menu = document.querySelector(".js-menu");
    let burger = document.querySelector('.menu__burger');

    jQuery(burger).on('click', () => {
        jQuery(menu).hasClass('is-opened') ? jQuery(menu).removeClass('is-opened') : jQuery(menu).addClass('is-opened');
    })

    jQuery(window).resize(() => {
        if(jQuery(window).width() > 992) {
            jQuery(menu).removeClass('is-opened');
        }
    });
});