jQuery(document).ready(function(){
    var sliders = document.querySelectorAll(".carrousel");

    sliders.forEach((slider) => {
        var hasArrows = slider.dataset.arrows;
        var isAuto = slider.dataset.auto;
        var isInfinite = (slider.dataset.infinite === 'true');
        var isFading = (slider.dataset.fade === 'true');

        if (typeof hasArrows == 'undefined' || hasArrows == null)
            hasArrows = false;

        if (typeof isAuto == 'undefined' || isAuto == null)
            isAuto = false;

        if (typeof isInfinite == 'undefined' || isInfinite == null) {
            isInfinite = false;
        }


        if (typeof isFading == 'undefined' || isFading == null) {
            isFading = false;
        }

        jQuery(slider).slick({
            slidesToShow: 1,
            arrows: hasArrows,
            autoplay: isAuto,
            autoplaySpeed: 3000,
            infinite: isInfinite,
            fade: isFading,
            cssEase: 'linear',
        });
    });
});