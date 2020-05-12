;(function($) {//Protect it from other jQuery instances

    $(window).ready(function(){

        $('.media-boxes-grid').each(function(){

            var settings     			= $.parseJSON($(this).attr('data-settings'));

			settings['getSortData'] 	= {
											date			: function (elem) { return Date.parse( $(elem).find('.media-box-sort-date').text() ); },
											author			: '.media-box-sort-author',
											ID				: function (elem) { return parseFloat( $(elem).find('.media-box-sort-id').text() ); },
											title			: '.media-box-sort-title',
											name			: '.media-box-sort-name',
											modified		: function (elem) { return Date.parse( $(elem).find('.media-box-sort-modified').text() ); },
											comment_count	: function (elem) { return parseFloat( $(elem).find('.media-box-sort-comment_count').text() ); },
								        };


          	$(this).mediaBoxes(settings);
        });

    });

})(jQuery);