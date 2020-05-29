jQuery(document).ready(function(){
    let body = jQuery("html, body");
    let totop = jQuery('[data-module="to-top"]');

    totop.on('click', (e) => gototop(e));

    function gototop(e) {
        body.stop().animate(
            {
                scrollTop:0
            },
            500,
            'swing'
        );
    }
});