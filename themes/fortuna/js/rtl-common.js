jQuery(document).ready(function($){
    
    $(window).smartresize(function() {
        $('.vc_row[data-vc-full-width="true"]').each(function(k) {
			
			if($(this).find(".rev_slider.fullwidthabanner, .rev_slider.fullscreenbanner, .upb_row_bg[data-bg-override='full']").length < 1){
				
				var $this = $(this);
				var temp = $this.css('left');
				
				$this.css({ 
					'right': temp,
					'left'	: "auto"
			//		'visibility': 'visible'
				});
			}
        });
    });
    
    $(window).load(function() {   
        $('.icon.icon-arrow-right6').each(function(k) {
            var $this = $(this);
            $this.removeClass('icon-arrow-right6');
            $this.addClass('icon-arrow-left6');
        });
    });
});