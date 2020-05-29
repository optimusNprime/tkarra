jQuery(document).ready(function () {
  const elems = jQuery('[data-module="inview"]');
  jQuery.fn.inView = function(){
    //Window Object
    const win = jQuery(window);
    //Object to Check
    let obj = jQuery(this);
    //the top Scroll Position in the page
    const scrollPosition = win.scrollTop();
    //the end of the visible area in the page, starting from the scroll position
    const visibleArea = win.scrollTop() + win.height();
    //the end of the object to check
    const objEndPos = (obj.offset().top + obj.outerHeight());
    if (visibleArea + 20 >= objEndPos && scrollPosition <= objEndPos) {
      obj.removeClass('inview--not');
    }
  };


  jQuery.each(elems, (i, el) => {
    jQuery(el).inView();
  });

  jQuery(window).scroll(() => {
    jQuery.each(elems, (i, el) => {
      jQuery(el).inView();
    });
  });
});