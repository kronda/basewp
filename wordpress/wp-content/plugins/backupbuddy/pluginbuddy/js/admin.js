jQuery(document).ready(function() {
	
	jQuery('.pb_debug_show').click(function(e) {
		jQuery(this).hide();
		jQuery(this).parent().children( '.pb_debug_hide').show();
		jQuery(this).parent().css( 'float', 'left' );
		jQuery(this).parent().css( 'width', '80%' );
		jQuery(this).parent().children( 'div').show();
	});
	jQuery('.pb_debug_hide').click(function(e) {
		jQuery(this).hide();
		jQuery(this).parent().children( '.pb_debug_show').show();
		jQuery(this).parent().css( 'float', 'right' );
		jQuery(this).parent().css( 'width', '40px' );
		jQuery(this).parent().children( 'div').hide();
	});

	
	jQuery('.pluginbuddy_tip').tooltip({ 
		track: true, 
		delay: 0, 
		showURL: false, 
		showBody: " - ", 
		fade: 250 
	});
	
	if (typeof jQuery.tableDnD !== 'undefined') { // If tableDnD function loaded.
		jQuery('.pb_reorder').tableDnD({
			onDrop: function(tbody, row) {
				var new_order = new Array();
				var rows = tbody.rows;
				for (var i=0; i<rows.length; i++) {
					new_order.push( rows[i].id.substring(11) );
				}
				new_order = new_order.join( ',' );
				jQuery( '#pb_order' ).val( new_order )
			},
			dragHandle: "pb_draghandle"
		});
	}
	
	jQuery('.pb_toggle').click(function(e) {
		jQuery( '#pb_toggle-' + jQuery(this).attr('id') ).slideToggle();
	});
	
	
	
	// Hide a dismissable alert and send AJAX call so it won't be shown in the future.
	jQuery( '.pb_backupbuddy_disalert' ).click( function(e) {
		
		var this_unique_id = jQuery(this).parents('.pb_backupbuddy_alert').attr('rel');
		var this_disalert_url = jQuery(this).attr('alt');
		//alert( unique_id );
		
		jQuery.post( this_disalert_url,
			{ unique_id: this_unique_id }, 
			function(data) {
				data = jQuery.trim( data );
				if ( data != '1' ) {
					alert( 'Error saving dismissal. The alert may return. Error: ' + data );
				}
			}
		);
		
		jQuery(this).parents('.pb_backupbuddy_alert').slideUp();
		
	});
	
	
	
});