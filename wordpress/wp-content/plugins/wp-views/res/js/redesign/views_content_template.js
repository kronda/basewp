jQuery(document).ready(function($){

	$('.js-wpv-content-template-mode-tip').click(function(){

		var $thiz = $(this);
		$('.wp-pointer').fadeOut(100);

		$(this).pointer({
			content: '<h3>'+ $(this).attr('title') +'</h3><p>'+ $thiz.data('pointer-content-firstp') +'</p><p>'+ $thiz.data('pointer-content-secondp') +'</p>',
			position: {
				edge: 'right',
				align: 'left',
				offset: '-5 0'
			}
		}).pointer('open');
	});
	
	$(document).on('input','#title', function(e){
                    $('#titlewrap .toolset-alert').remove();
    });
	
	$(document).on('click','#content-template-show-help', function() {
                    var close_this = 1;
                    if ( $(this).prop('checked') == false ){
                        $('.toolset-help ').css('display','none');                      
                    }else{
                        $('.toolset-help ').css('display','block');   
                        close_this = 0;
                    }
                    var data = {
                         action : 'close_ct_help_box',
                         wpnonce : jQuery('#set_view_template').attr('value'),
                         close_this : close_this
                    };
                    jQuery.post(ajaxurl, data, function(response) {});
    });
	
	var $dropdownLists = $('.js-wpv-content-template-dropdown-list:visible');
	$.each($dropdownLists,function(){

		$(this).prev('p').find('[class^="icon-"]')
			.removeClass('icon-caret-down')
			.addClass('icon-caret-up');
	});

	$('.js-wpv-content-template-open').click(function(e) {

		e.preventDefault();
		var $dropdownList = $(this).parent().next('.js-wpv-content-template-dropdown-list');
		$dropdownList.toggle('fast',function(){

			if ( $dropdownList.is(':hidden') ) {

				$(this).prev('p').find('[class^="icon-"]')
					.removeClass('icon-caret-up')
					.addClass('icon-caret-down');
				$dropdownList.find('input[type=hidden]').val('0');	

			} else {

				$(this).prev('p').find('[class^="icon-"]')
					.removeClass('icon-caret-down')
					.addClass('icon-caret-up');
				$dropdownList.find('input[type=hidden]').val('1');
			}
		});
		return false;

	});

	$('.js-wpv-content-template-alert').colorbox({
		inline: false,
		onComplete:function(){
			$('#cboxClose').html('');
		}
	});

	$('.js-wpv-check-for-icon').change(function(){
		if ( $(this).attr('checked') != 'checked' ){
			$(this).parent().find('.js-wpv-content-template-alert').hide();
		}else{
			$(this).parent().find('.js-wpv-content-template-alert').show();
		}
	});
	$(document).on('click','.js-wpv-content-template-update-posts-process', function() {
		var type = $(this).data('type');
		var tid = $(this).data('id');
		var data = {
			action : 'set_view_template',
			view_template_id : tid,
			wpnonce : $('#set_view_template').attr('value'),
			type : type,
			lang : ''
		};

		var updateMessage = $.post(ajaxurl, data, function(response) {
			$('.wpv-dialog').html(response);
			$('#wpv-content-template-alert-link-'+type).hide();
		});

		updateMessage.always(function(){ //function executed always - no matter if ajax has succeed or not
			console.log('ajax request complete');
		});

		updateMessage.fail(function(){ //function executed if ajax request fails
			console.log('fail');
		});


	});

});