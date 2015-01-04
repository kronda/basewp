jQuery(document).ready(function($){
    wpv_register_add_filter_callback('wpv_post_id_add_filter');
    jQuery('.wpv_id_url_param_missing,.wpv_id_url_param_ilegal,.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal,.wpv_id_helper').hide();

	jQuery('input[name=id_mode\\[\\]]').change(function() {
      jQuery('.wpv_id_url_param_missing,.wpv_id_url_param_ilegal,.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal').hide();
	  wpv_add_id_help();
    });
    jQuery('input[name=post_ids_url]').change(wpv_add_id_help);
    jQuery('input[name=post_ids_shortcode]').change(wpv_add_id_help);
    
    
});

function wpv_add_id_help() {
  jQuery('.wpv_id_url_param_missing,.wpv_id_url_param_ilegal,.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal').hide();
  if (jQuery('input[name=id_mode\\[\\]]:checked').val() == 'by_url') {
    var url_value = jQuery('input[name=post_ids_url]').val();
    if (url_value == '') {
      jQuery('.wpv_id_url_param_missing').show();
      jQuery('.wpv_id_helper').html('');
    } else {
      var pat = /^[a-z0-9\-\_]+$/;
      if (pat.test(url_value) == false) {
	  jQuery('.wpv_id_helper').html('');
	  jQuery('.wpv_id_url_param_ilegal').show();
      } else {
		var id_help = '<small>To control the IDs, link to the page that includes this View with the argument set as '+
		'<strong class="author_url_param">\''+ url_value + '\'</strong>. <br>For example:';
		id_help += '<br />yoursite/page-with-this-view/?<strong class="author_url_param">'+ url_value + '</strong>=1';
		id_help += '<br />This will filter by Post ID with ID=1</small>';
		jQuery('.wpv_id_helper').show();
		jQuery('.wpv_id_helper').html(id_help);
		jQuery('.wpv_id_url_param_ilegal').hide();
      }
    }
  }
  if (jQuery('input[name=id_mode\\[\\]]:checked').val() == 'shortcode') {
    var view_name = jQuery('input[name=post_title]').val();
    var short_value = jQuery('input[name=post_ids_shortcode]').val();
    if (short_value == '') {
      jQuery('.wpv_id_shortcode_param_missing').show();
      jQuery('.wpv_id_helper').html('');
    } else {
      var pat = /^[a-z0-9]+$/;
      if (pat.test(short_value) == false) {
	jQuery('.wpv_id_helper').html('');
	jQuery('.wpv_id_shortcode_param_ilegal').show();
      } else {
	var id_short_help = '<small>To control the IDs, edit the shortcode to this View and add the <strong class="author_url_param">\''+ short_value + '\'</strong> attribute to it. <br>For example:';
	id_short_help += '<br />[wpv-view name="' + view_name + '" <strong class="author_url_param">'+ short_value + '</strong>="1"]';
	id_short_help += '<br />This will filter by Post ID with ID=1</small>';
	jQuery('.wpv_id_helper').show();
	jQuery('.wpv_id_helper').html(id_short_help);
	jQuery('.wpv_id_shortcode_param_ilegal').hide();
      }
    }
	
  }
  if (jQuery('input[name=id_mode\\[\\]]:checked').val() == 'by_ids') {
	var id_short_help = '<small>You can use comma for multiple IDs</smal>';
	jQuery('.wpv_id_helper').show();
	jQuery('.wpv_id_helper').html(id_short_help);		
  }
}

/*
 * function: wpv_id_add_filter
*/

function wpv_post_id_add_filter(data) {
    
    var id_mode = '';
    var post_id_ids_list = '';
    var post_ids_url = '';
    var post_ids_shortcode = '';
    if (jQuery('input[name=id_mode\\[\\]]').length) {
        id_mode = jQuery('input[name=id_mode\\[\\]]:checked').val();
		post_id_ids_list = jQuery('input[name=post_id_ids_list]').val();
		post_ids_url = jQuery('input[name=post_ids_url]').val();
		post_ids_shortcode = jQuery('input[name=post_ids_shortcode]').val();
    }
    
    if (id_mode != '') {
        data['id_mode'] = id_mode;
        data['post_id_ids_list'] = post_id_ids_list;
		data['post_ids_url'] = post_ids_url;
		data['post_ids_shortcode'] = post_ids_shortcode;
    }
    
    return data;
}

var previous_id_mode;
var previous_post_id_ids_list;
var previous_post_ids_shortcode;
var previous_post_ids_url;

/* Show the edit screen */

function wpv_show_filter_id_edit() {

    wpv_edit_id_help();

    previous_id_mode = jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked');
    previous_post_id_ids_list = jQuery('input[name=_wpv_settings\\[post_id_ids_list\\]]').val();
    previous_post_ids_shortcode = jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').val();
    previous_post_ids_url = jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').val();
    jQuery('.wpv_id_url_param_missing,.wpv_id_shortcode_param_missing').hide();
    
    jQuery('#wpv-filter-id-edit').parent().parent().css('background-color', jQuery('#wpv-filter-id-edit').css('background-color'));

    jQuery('#wpv-filter-id-edit').show();
    jQuery('#wpv-filter-id-show').hide();
    
    jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]').change(function() {
      jQuery('.wpv_id_url_param_missing,.wpv_id_url_param_ilegal,.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal').hide();
      wpv_edit_id_help();
    });
    jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').change(wpv_edit_id_help);
    jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').change(wpv_edit_id_help);
    
}

/* Save the edit results and get the summary */
                                               
function wpv_show_filter_id_edit_ok() {

    // find the filter row in the table.
    var tr = jQuery('#wpv-filter-id-show').parent().parent();
    var row = tr.attr('id').substr(15);
    var data = {
        action : 'wpv_get_table_row_ui',
        type_data : 'post_id',
        row : row,
        id_mode : jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val(),
        post_id_ids_list : jQuery('input[name=_wpv_settings\\[post_id_ids_list\\]]').val(),
        post_ids_url : jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').val(),
        post_ids_shortcode : jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').val(),
        wpv_nonce : jQuery('#wpv_get_table_row_ui_nonce').attr('value')
    };
    var pat = /^[a-z0-9]+$/;
    var t = jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').val();
    var paturl = /^[a-z0-9\-\_]+$/;
    var turl = jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').val();
    
    if (jQuery('input[name=_wpv_settings\\[id\\]\\[\\]]:checked').val() == 'by_url' 
		&& jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').val() == '') {
      jQuery('.wpv_id_url_param_missing').show();
      jQuery('.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal').hide();
    } 
	else if (jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val() == 'by_url' 
		&& paturl.test(turl) == false) {
      jQuery('.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal,.wpv_id_url_param_missing').hide();
      jQuery('.wpv_id_url_param_ilegal').show();
    } 
	else if (jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val() == 'shortcode' 
		&& jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').val() == '') {
      jQuery('.wpv_id_shortcode_param_missing').show();
      jQuery('.wpv_id_url_param_missing,.wpv_id_shortcode_param_ilegal').hide();
    } else if (jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val() == 'post_ids_shortcode' && pat.test(t) == false) {
      jQuery('.wpv_id_shortcode_param_missing,.wpv_id_url_param_ilegal,.wpv_id_url_param_missing').hide();
      jQuery('.wpv_id_shortcode_param_ilegal').show();
    } else {    
    var td = '';
    jQuery.post(ajaxurl, data, function(response) {
        td = response;
        jQuery('#wpv_filter_row_' + row).html(td);
        jQuery('#wpv-filter-id-edit').parent().parent().css('background-color', '');
        jQuery('#wpv-filter-id-edit').hide();
        jQuery('#wpv-filter-id-show').show();
        on_generate_wpv_filter();
    });
   show_view_changed_message();
   
    }
}

function wpv_edit_id_help() {
	
  jQuery('.wpv_id_url_param_missing,.wpv_id_url_param_ilegal,.wpv_id_shortcode_param_missing,.wpv_id_shortcode_param_ilegal').hide();
  if (jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val() == 'by_url') {
    var url_value = jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').val();
    if (url_value == '') {
      jQuery('.wpv_id_url_param_missing').show();
      jQuery('.wpv_id_helper').html('');
    } else {
      var pat = /^[a-z0-9\-\_]+$/;
      if (pat.test(url_value) == false) {
	jQuery('.wpv_id_helper').html('');
	jQuery('.wpv_id_url_param_ilegal').show();
      } else {
		var id_help = '<small>To control the IDs, link to the page that includes this View with the argument set as '+
			'<strong class="author_url_param">\''+ url_value + '\'</strong>. <br>For example:';
			id_help += '<br />yoursite/page-with-this-view/?<strong class="author_url_param">'+ url_value + '</strong>=1';
			id_help += '<br />This will filter by Post ID with ID=1</small>';
		jQuery('.wpv_id_helper').show();
		jQuery('.wpv_id_helper').html(id_help);
      }
    }
  }
  if (jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val() == 'shortcode') {
    var view_name = jQuery('input[name=post_title]').val();
    var short_value = jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').val();
    if (short_value == '') {
      jQuery('.wpv_id_shortcode_param_missing').show();
      jQuery('.wpv_id_helper').html('');
    } else {
      var pat = /^[a-z0-9]+$/;
      if (pat.test(short_value) == false) {
	jQuery('.wpv_id_helper').html('');
	jQuery('.wpv_id_shortcode_param_ilegal').show();
      } else {
	var id_short_help = '<small>To control the IDs, edit the shortcode to this View and add the <strong class="author_url_param">\''+ short_value + '\'</strong> attribute to it. <br>For example:';
	id_short_help += '<br />[wpv-view name="' + view_name + '" <strong class="author_url_param">'+ short_value + '</strong>="1"]';
	id_short_help += '<br />This will filter by Post ID with ID=1</small>';
	jQuery('.wpv_id_helper').show();
	jQuery('.wpv_id_helper').html(id_short_help);
      }
    }
  }
  if (jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]:checked').val() == 'by_ids') {
	var id_short_help = '<small>You can use comma for multiple IDs</smal>';
	jQuery('.wpv_id_helper').show();
	jQuery('.wpv_id_helper').html(id_short_help);	
  }
}

/* Cancel the edit operation and set the values back to the way they were
*/

function wpv_show_filter_id_edit_cancel() {

    jQuery('input[name=_wpv_settings\\[id_mode\\]\\[\\]]').each( function(index) {
        jQuery(this).attr('checked', false); 
    });
    previous_id_mode.attr('checked', true);
    jQuery('input[name=_wpv_settings\\[post_id_ids_list\\]]').val(previous_post_id_ids_list);
    jQuery('input[name=_wpv_settings\\[post_ids_shortcode\\]]').val(previous_post_ids_shortcode);
    jQuery('input[name=_wpv_settings\\[post_ids_url\\]]').val(previous_post_ids_url);

    jQuery('#wpv-filter-id-edit').parent().parent().css('background-color', '');
    jQuery('#wpv-filter-id-edit').hide();
    jQuery('#wpv-filter-id-show').show();
}
