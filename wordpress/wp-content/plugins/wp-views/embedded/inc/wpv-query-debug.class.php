<?php

global $WPVDebug;

$WPVDebug = new WPV_Debug();

function wpv_debuger()
{
	return;
}


class WPV_Debug{

	function __construct(){
		$this->options = null;
		$this->depth = 0;
		$this->log_array = array();
		$this->status = false;
		$this->total_memory = memory_get_usage ( false );
		$this->show_type = 'compact';
		$options = get_option('wpv_options');

		if ( !isset($options['wpv_debug_mode']) ) {
			$options['wpv_debug_mode'] = '';
		}
		if ( !isset($options['wpv-debug-mode-type']) ) {
			$options['wpv-debug-mode-type'] = 'compact';
		}
		if ( isset($options['wpv_debug_mode']) && !empty($options['wpv_debug_mode'])   ){
			$this->status = true;
			$this->show_type = $options['wpv-debug-mode-type'];
			if ( !defined('SAVEQUERIES') ){
					define('SAVEQUERIES', true);
			}
		}

		add_filter('wpv_shortcode_debug', array($this, 'wpv_shortcode_debug_callback'),10,5);
		add_action('wp_footer', array($this, 'wpv_show_debug_output'),30,3);
	}

	function wpv_show_debug_output() {

		if ( !$this->user_can_debug() ){return;}

		$out = $this->generate_output();

		if ( !isset($this->log_array[1]) || count($this->log_array[1]) == 0){
			return;
		}
		ob_start();
		?>

		<div class="wpv-hidden" style="display:none">
			<a class="js-wpv-open-debug-window" href="#">open</a>
			<div class="js-debuger-output">
				<div class="wpv-debug-table-wrapper">
					<?php echo $out;?>
				</div> <!-- .wpv-debug-table-wrapper -->
			</div> <!-- .js-debuger-output -->
		</div>

		<?php // TODO: Can we register these files? ?>
		<script type="text/javascript" src="<?php echo WPV_URL_EMBEDDED;?>/res/js/lib/jstorage.min.js"></script>
		<script type="text/javascript" src="<?php echo WPV_URL_EMBEDDED;?>/res/js/lib/prism.js" data-manual></script>
		<script type="text/javascript">

			jQuery(document).ready(function($) {
				
				// Define popup size
				var popupWidth = $.jStorage.get('debugger_popup_width', 1100); // get width saved in local storage, set 1100 if vaule is not set yet
				var popupHeight = $.jStorage.get('debugger_popup_height', 800); // get height saved in local storage, set 800 if vaule is not set yet

				// Reduce popup size for small screens
				if ( screen.width < 1000 ) { /* less than .wpv-debug-table-wrapper width */
					popupWidth = screen.width;
				}
				if ( screen.height < popupHeight ) {
					popupHeight = screen.height;
				}

				// Get popup position from local storage. Set to center if local storage values are not set
				var popupPosLeft = $.jStorage.get('debugger_popup_pos_left', (screen.width/2) - (popupWidth/2) );
				var popupPosTop = $.jStorage.get('debugger_popup_pos_top', (screen.height/2) - (popupHeight/2) );
				
				// Get the debug info
				var debug_info = $('.js-debuger-output').clone().html();
				$('.js-debuger-output').remove();
				
				function nWin() {

				// Open popup
				var winobj = window.open(
					'',
					'blank',
					'resizable=yes,scrollbars=yes,status=no,location=no,width='+popupWidth+',height='+popupHeight+',left='+popupPosLeft+',top='+popupPosTop
				);

				//var windoc = winobj.document;
				var $debugCSS = $('<link rel="stylesheet" href="<?php echo WPV_URL_EMBEDDED; ?>/res/css/debug.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" type="text/css" media="all" />');
				var $prismCSS = $('<link rel="stylesheet" href="<?php echo WPV_URL_EMBEDDED; ?>/res/css/prism.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" type="text/css" media="all" />');

				var cssone = '<link rel="stylesheet" href="<?php echo WPV_URL_EMBEDDED; ?>/res/css/debug.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" type="text/css" media="all" />';
				var csstwo = '<link rel="stylesheet" href="<?php echo WPV_URL_EMBEDDED; ?>/res/css/prism.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" type="text/css" media="all" />';
				// Append debug data into popup
				
				//debug_info.appendTo( winobj.document.body );
				//winobj.document.append(debug_info.html());
				winobj.document.write(cssone + csstwo + debug_info);

				winobj.document.title = "<?php  _e('Views/Content Templates debug information', 'wpv-views'); ?>";
				
				winobj.focus();

				// Append CSS
				//$(windoc).find('head').append( $debugCSS );
				//$(windoc).find('head').append( $prismCSS );

				// Save popup settings to local storage
				var savePopupSettings = function() {
					$.jStorage.set('debugger_popup_width', winobj.outerWidth );
					$.jStorage.set('debugger_popup_height', winobj.innerHeight ); // I have NO IDEA why innerHeight works in a way how outerHeight is suppposed to work...
					$.jStorage.set('debugger_popup_pos_left', winobj.screenX );
					$.jStorage.set('debugger_popup_pos_top', winobj.screenY );
				}

				// Close previously opened popups and save popup settings to local storage on parent window unload event
				$(window).on('unload', function() {
					if ( $(winobj).length !== 0 ) {
						savePopupSettings();
						winobj.close();
					}
				});

				// Save popup position to local storage on popup unload event
				$(winobj).on('unload', function() {
					savePopupSettings();
				});
				
				$(winobj.document).find('.js-code-wrapper, .wpv-loop-table-wrapper').each(function(){
					$(this).addClass("hidden");
				});

				// Display notice once js-toggle-higlighter is checked
				$(winobj.document).find('#js-toggle-higlighter').on('change', function(e) {
					var $notice = $(winobj.document).find('.js-higlighter-notice');
					var $body = $(winobj.document).find('body');
					if ( $(this).is(':checked') ) {
						$notice.removeClass('hidden');
					}
					else {
						$notice.addClass('hidden');
					}
				});

				// Add nobreak class to <body> tag if linebreaks is disabled
				$(winobj.document).find('#js-toggle-linebreaks').on('change', function(e) {
					var $body = $(winobj.document).find('body');
					if ( $(this).is(':checked') ) {
						$body.removeClass('nowrap');
					}
					else {
						$body.addClass('nowrap');
					}
				});

				// Expand/collapse code blocks
				$(winobj.document).find('.js-show-code').on('click', function(e) {
					e.preventDefault();
					var $this = $(this);
					var $target = $this.next('.js-code-wrapper');
					var codeToHighlight = $target.find('.js-code-highlight')[0];
					var expanded = $this.data('expanded');
					var isHighlighterEnabled = $(winobj.document).find('#js-toggle-higlighter').is(':checked');

					if ( expanded ) {
						$target.addClass("hidden");
						$this
							.text( $this.data('text-collapsed') )
							.data('expanded', false);
					}
					else {
						if ( isHighlighterEnabled && ( typeof( $target.data('highlighted') ) === 'undefined' ) ) {

							$this
								.text( $this.data('text-disabled') )
								.prop('disabled', true);

							Prism.highlightElement( codeToHighlight, true, function() {
								$target
									.data('highlighted', true)
									.removeClass("hidden");
								$this
									.prop('disabled', false)
									.text( $this.data('text-expanded') )
									.data('expanded', true);
							});
						}
						else {
							$target.removeClass("hidden");
							$this
								.text( $this.data('text-expanded') )
								.data('expanded', true)
						}

					}

					return false;
				});

				// Expand/collapse top level section
				$(winobj.document).find('.js-show-view').on('click', function(e) {
					e.preventDefault();
					
					$elem = $(this).data('target');

					var $target = $(winobj.document).find( $elem );
					var expanded = $(this).data('expanded');

					if ( expanded ) {
						$target.addClass("hidden");
						$(this).find('.js-wpv-debug-corner').eq(0).text('+');
						$(this).data('expanded', false);
					}
					else {
						$target.removeClass("hidden");
						$(this).find('.js-wpv-debug-corner').eq(0).text('-');
						$(this).data('expanded', true);
					}

					return false;
				});
				
				}
				
				$(document).on("click", ".js-wpv-open-debug-window", function(e){
					e.preventDefault();
					nWin();
					return false;
				});
				
				$(".js-wpv-open-debug-window").click();
				
			});

		</script>

		<?php
		$output = ob_get_clean();

		echo $output;
	}

    function __destruct(){

    }

	function user_can_debug(){
		if ( !function_exists('wpv_view_defaults') ){
			return false;
		}
		if ( !$this->status){
			return false;
		}
		if ( !is_user_logged_in() ){
			return false;
		}
		global $user_level;
		if ( $user_level !== 10 ){
			return false;
		}
		return true;
	}

	function wpv_debug_start( $view_id, $atts="", $type = 'view' ){
	if ( !$this->user_can_debug() ){return;}
		$this->depth++;
		static $correspondences = array();
			$this->log_array[$this->depth][] = array( 'view_id'=>$view_id, 'view_parent' => $this->get_parent_view(),'type'=>$type );

			/*	if ( isset($atts[0]['name']) ){
					$this->log_array[$this->depth][$this->get_index()]['view_name'] = $atts[0]['name'] . json_encode($atts[0]) . $view_id;
				}else{*/
					if ( is_numeric( $view_id ) ) {
						$view = get_post($view_id);
						$this->log_array[$this->depth][$this->get_index()]['view_name'] = $view->post_title;
					}
					else{
						if ( $type == 'content-template' && ( $view_id == 'None' || $view_id == 'none' ) ) {
							$title = __('Current post body', 'wpv-views');
						} else {
							global $wpdb;
							$title = '';
							if ( !isset( $correspondences[$view_id] ) ) {
								$title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE post_name = '$view_id'");
								if ( empty( $title ) && !empty( $view_id ) ) {
									$title = $view_id;
								}
								$correspondences[$view_id] = $title;
							} else {
								$title = $correspondences[$view_id];
							}
							/*
							$title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE post_name = '$view_id'");
							if ( empty( $title ) && !empty( $view_id ) ) {
								$title = $view_id;
							}
							*/
						}
						$this->log_array[$this->depth][$this->get_index()]['view_name'] = $title;
					}
			//	}

			if ( $type == 'view' ){
				$view_settings = get_post_meta($view_id, '_wpv_settings', true);
				if ( isset( $view_settings['view-query-mode'] ) && $view_settings['view-query-mode'] == 'archive' ){
					$this->log_array[$this->depth][$this->get_index()]['type'] = 'archive';
				}
			}

			$this->log_array[$this->depth][$this->get_index()]['mysql_query'] = '';
			$this->log_array[$this->depth][$this->get_index()]['render_time'] = timer_stop( 0, 5 );
			$this->log_array[$this->depth][$this->get_index()]['memory_usage'] = memory_get_usage ( false );
			$this->log_array[$this->depth][$this->get_index()]['current_index'] = 0;
			$this->log_array[$this->depth][$this->get_index()]['parent_index'] = 0;
			$this->log_array[$this->depth][$this->get_index()]['items_found'] = null;
			if ( $this->depth > 1 ){
				$temp_array = array_keys($this->log_array[($this->depth-1)]);
				$this->log_array[$this->depth][$this->get_index()]['parent_index'] = $this->log_array[$this->depth-1][end($temp_array)]['current_index'];
			}

		//print '<br><br><br><br>'.$this->log_array[$this->depth][$this->get_index()]['view_name'].'<br><br><br><br>';

	}


	function update_template_id( $id ){
		if ( !$this->user_can_debug() ){return;}
		$this->log_array[$this->depth][$this->get_index()]['view_id'] = $id;
	}

	function get_index( $depth = 0 ) {
		if ( !$this->user_can_debug() ){
			return;
		}
		if ( is_array($this->log_array) && isset( $this->log_array[$this->depth] ) && is_array( $this->log_array[$this->depth] ) ) {
			$tem_array = array_keys($this->log_array[$this->depth]);
			return end($tem_array);
		}
		else{
			return 0;
		}

	}

	function get_parent_view(){
		if ( !$this->user_can_debug() ){return;}
		$out = 0;
		if ( $this->depth >1  ){
			$temp_array = array_keys($this->log_array[($this->depth-1)]);
			$out = $this->log_array[($this->depth-1)][end($temp_array)]['view_id'];
		}
		return $out;
	}


	function wpv_debug_end(){
		if ( !$this->user_can_debug() ){return;}


			$time = timer_stop( 0, 3 ) - $this->log_array[$this->depth][$this->get_index()]['render_time'];
			$memory =  memory_get_usage ( false ) - $this->log_array[$this->depth][$this->get_index()]['memory_usage'];
			$this->log_array[$this->depth][$this->get_index()]['render_time'] = $time;
			$this->log_array[$this->depth][$this->get_index()]['memory_usage'] = round(($memory/8/1024/1024),4).'MB';

		 $this->depth--;


	}

	function set_index( $index = '' ){
		if ( !$this->status || !$this->user_can_debug() ){return;}
		if ( !isset($this->log_array[$this->depth][$this->get_index()]['current_index']) ){ return; }
		if ( empty($index) ){
			$this->log_array[$this->depth][$this->get_index()]['current_index']++;
		}else{
			$this->log_array[$this->depth][$this->get_index()]['current_index'] = $index;
		}
	}
	function clean_index( ){
		if ( !$this->status || !$this->user_can_debug() ){return;}
		if ( !isset($this->log_array[$this->depth][$this->get_index()]['current_index']) ){ return; }
		$this->log_array[$this->depth][$this->get_index()]['current_index'] = -1;
	}
	function unset_index( $index ){
		if ( !$this->status || !$this->user_can_debug() ){return;}
		$this->log_array[$this->depth][$this->get_index()]['current_index'] = 0;
	}



	function add_log( $method = 'info', $in, $index = '', $short = '', $multiple = false ){
		if ( !$this->status || !$this->user_can_debug() ){return;}
		if ( !isset($this->log_array[$this->depth][$this->get_index()]['render_time']) ){return; }

		if ( $method == 'filters' ){
			$this->log_array[$this->depth][$this->get_index()]['filters'][] = $in;
			$this->log_array[$this->depth][$this->get_index()]['filters_short'][] = $short;
		}
		elseif( $method == 'items_count'){
			$this->log_array[$this->depth][$this->get_index()]['items_found'] = $in;
		}
		elseif( $method == "content-template" ){
			$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']] =  array(
			//	'title' => "Title: ". $in->post_title .", ID: ". $in->ID ."", NOTE no need of title here, we already display it when opening the table
				'data_array' => esc_attr( print_r($in,true) )
			);
		}
		elseif( $method == "posts" ){
			$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']] =  array(
				'title' => __('Title', 'wpv-views') . ": ". $in->post_title .", " . __('ID', 'wpv-views') . ": ". $in->ID ."",
				'data_array' => esc_attr( print_r($in,true) )
			);
		}
		elseif( $method == "taxonomy" ){

			$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']] =  array(
				'title' => __('Name', 'wpv-views') . ": ". $in->name .", " . __('ID', 'wpv-views') . ": ". $in->term_id ."",
				'data_array' => esc_attr( print_r($in,true) )
			);
		}
		elseif( $method == "users" ){
			$login = $in->data->user_login;
			$id = $in->data->ID;
			$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']] =
			array(
				'title' => __('User', 'wpv-views') . ": $login, " . __('ID', 'wpv-views') . ": $id",
				'data_array' => esc_attr( print_r($in,true) )
			);
		}
		elseif( $method == 'info'){
			if ( isset( $this->log_array[$this->depth][$this->get_index()][$index] ) && !empty( $this->log_array[$this->depth][$this->get_index()][$index] ) && $multiple ) {
				$this->log_array[$this->depth][$this->get_index()][$index] .= "\n" . $in;
			} else {
				$this->log_array[$this->depth][$this->get_index()][$index] = $in;
			}
			if ( !empty($short) ){
				$this->log_array[$this->depth][$this->get_index()][$index.'_short'] = $short;
			}
		}
		elseif( $method == 'mysql_query'){
			global $post_query;

			if ( $index == 'users' ){
				for ($i=count($in)-1;$i>=0;$i--){
					if( preg_match("/WP_User_Query->query$/",$in[$i][2]) ){
						$this->log_array[$this->depth][$this->get_index()]['mysql_query'] = $in[($i-1)][0];
						$i = 0;
					}
				}
			}elseif( $index == 'taxonomy' ){
				for ($i=count($in)-1;$i>=0;$i--){
					if( preg_match("/wp_get_object_terms$/",$in[$i][2]) ){
						$this->log_array[$this->depth][$this->get_index()]['mysql_query'] = $in[($i)][0].
						"\n\n" . __('Object received from cache. MySQL query was cached in', 'wpv-views') . ": ".$in[($i)][2];
						$i = 0;
					}
				}
			}
			elseif( $index == 'posts' ){
				/*$cur_index = count($in);
				$this->log_array[$this->depth][$this->get_index()]['mysql_query'] = 'Posts: '. $in[($cur_index-2)][0];
				$this->log_array[$this->depth][$this->get_index()]['mysql_query'] .= "\n\nPost meta: ". $in[($cur_index-1)][0];
				 * */
				if ( isset( $this->log_array[$this->depth][$this->get_index()]['mysql_query'] ) && !empty( $this->log_array[$this->depth][$this->get_index()]['mysql_query'] ) && $multiple ) {
					$this->log_array[$this->depth][$this->get_index()]['mysql_query'] .= "\n\n" . $in;
				} else {
					$this->log_array[$this->depth][$this->get_index()]['mysql_query'] = $in;
				}
			}
		}

	}

	function add_log_item( $index, $in ){
		if ( !$this->status || !$this->user_can_debug() ){return;}
		if ( !isset($this->log_array[$this->depth][$this->get_index()]['current_index']) ){ return;}
		if ( !isset($this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']][$index]) ){
			$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']][$index] = '';
		}
		$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']][$index] .= $in;

	}

	function generate_output( $level = 1, $parent_view = 0, $index = 0){
		    $out = '';
			if ( !isset($this->log_array[$level]) ){ return;}
		    for ( $i=0; $i<count($this->log_array[$level]);$i++){
				$current = $this->log_array[$level][$i];

				if ($level > 1 && $current['parent_index'] != $index){
					continue;
				}
				if ( $current['view_parent'] == $parent_view){

					$out .= $this->generate_view_table($current, $level);

				}
			}

		return $out;
	}

	function generate_view_table( $current, $level = 1 ){
		$out = '';
		if ( $level == 1 && !empty($this->total_memory) ){
			global $wp, $wpdb;
			$load = '';
			if ( function_exists('sys_getloadavg') ){
				$load = sys_getloadavg();
				if ( is_array($load) ){
					$load = $load[0];
				}
			}
			$current_url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$time = timer_stop( 0, 2 );
			$memory =  memory_get_usage ( false ) - $this->total_memory;
			$memory = round(($memory/8/1024/1024),4).'MB';
			$out .= '<div class="wpv-settings-wrapper">';
			$out .= '<p>';
			$out .= '<input type="checkbox" id="js-toggle-linebreaks" checked> <label for="js-toggle-linebreaks">'. __('Enable line breaks', 'wpv-views') .'</label> ';
			$out .= '<input type="checkbox" id="js-toggle-higlighter"> <label for="js-toggle-higlighter">'. __('Enable syntax higlighter', 'wpv-views') .'</label> ';
			$out .= '<span class="desc js-higlighter-notice">'. __('Notice: Syntax higlighter can cause performance issues for large blocks of code','wpv-views'). '</span>';
			$out .= '</p>';
			$out .= '</div> <!-- .wpv-settings-wrapper -->';
			$out .= '<h2>' . __('Page info', 'wpv-views') . '</h2>';
			$out .= '<table class="wpv-debug-table">'.
			'<tr><td>' . __('Current page', 'wpv-views') . '</td><td>'. $current_url .'</td></tr>'.
			'<tr><td>' . __('Total memory used', 'wpv-views') . '</td><td>'. $memory .'</td></tr>'.
			'<tr><td>' . __('Render time', 'wpv-views') . '</td><td>'. $time .'</td></tr>'.
			'<tr><td>' . __('Total MySQL queries', 'wpv-views') . '</td><td>'. count($wpdb->queries) .'</td></tr>';
			if ( !empty($load) ){
				$out .= '<tr><td>' . __('CPU usage', 'wpv-views') . '</td><td>'. $load .'%</td></tr>';
			}
			$out .= '</table>';
			$out .= '<h2>' . __('Elements info', 'wpv-views') . '</h2>';
			$this->total_memory = '';
		}
					$edit_link = admin_url().'admin.php?page=views-editor&view_id='.$current['view_id'];
					if ( $current['type'] == 'content-template' ){
						$edit_link = admin_url().'post.php?post='.$current['view_id'].'&action=edit';
					}elseif( $current['type'] == 'archive' ){
						$edit_link = admin_url().'admin.php?page=view-archives-editor&view_id='.$current['view_id'];
					}
					$kind = __('View', 'wpv-views');
					if ( $current['type'] == 'content-template' ) {
						$kind = __('Content Template', 'wpv-views');
						if ( $current['view_id'] == 0 ) {
							$kind = __('Post body', 'wpv-views');
						}
					} else if ( $current['type'] == 'archive' ) {
						$kind = __('WordPress Archive', 'wpv-views');
					}
					$table_id = str_replace( '.','', uniqid('table-', true )); // generate unique ID attributes. Str replace is to remove dot character which is not valid for ID attributes.
					$out .= '<p><button class="js-show-view wpv-debug-toggle" data-expanded="true" data-target="#'. $table_id .'">';
					$out .= '<span class="js-wpv-debug-corner">-</span> '.$current['view_name'];
					if ( isset($current['short_query']) ){
						$out .= ' ('. $current['short_query'] .')';
					}
					$out .= '</button></p>';
					$out .= '<table class="wpv-debug-table" id="' . $table_id . '">';
					if ( $current['type'] == 'content-template' && $current['view_id'] == 0 ) {
					//	$out .= '<tr><td>' . __('Name', 'wpv-views') . '</td><td>'. $current['view_name'] .' (<a href="'.$edit_link.'" target="_blank">' . __('Edit', 'wpv-views') . '</a>)</td></tr>';
					} else {
						$out .= '<tr><td>' . __('ID', 'wpv-views') . '</td><td>'. $current['view_id'] .'</td></tr>'.
						'<tr><td>' . __('Name', 'wpv-views') . '</td><td>'. $current['view_name'] .' (<a href="'.$edit_link.'" target="_blank">' . __('Edit', 'wpv-views') . '</a>)</td></tr>';
					}
					$out .= '<tr><td>' . __('Kind of element', 'wpv-views') . '</td><td>'. $kind .'</td></tr>'.
					'<tr><td>' . __('Render time', 'wpv-views') . '</td><td>'. $current['render_time'] .'</td></tr>'.
					'<tr><td>' . __('Memory used', 'wpv-views') . '</td><td>'. $current['memory_usage'] .'</td></tr>';

					if ( isset($current['short_query']) && !empty($current['short_query'])){
						$out .= '<tr><td>' . __('Summary', 'wpv-views') . '</td><td>'. str_replace( "'", '&#39;', $current['short_query']) .'</td></tr>';
					}

					if ( isset($current['additional_info']) && !empty($current['additional_info']) &&  $this->show_type == 'full'){
						$out .= '<tr><td>' . __('Additional info', 'wpv-views') . '</td><td>'. esc_html($current['additional_info']) .'</td></tr>';
					}

					if ( isset($current['query_args']) && !empty($current['query_args']) &&  $this->show_type == 'full'){
						$out .= '<tr><td>' . __('Query args', 'wpv-views') . '</td><td><button class="js-show-code wpv-code-toggle wpv-debug-toggle" data-text-disabled="'.__('Loading','wpv-views').'&hellip;" data-text-expanded="'.__('Hide','wpv-views').'" data-text-collapsed="'.__('Show','wpv-views').'">'.__('Show','wpv-views').'</button><pre class="js-code-wrapper"><code class="language-php js-code-highlight">'. esc_html($current['query_args']) .'</code></pre></td></tr>';
					}

					if ( isset($current['mysql_query']) && !empty($current['mysql_query']) ){
						$out .= '<tr><td>' . __('MySQL query', 'wpv-views') . '</td><td><button class="js-show-code wpv-code-toggle wpv-debug-toggle" data-text-disabled="'.__('Loading','wpv-views').'&hellip;" data-text-expanded="'.__('Hide','wpv-views').'" data-text-collapsed="'.__('Show','wpv-views').'">'.__('Show','wpv-views').'</button><pre class="js-code-wrapper"><code class="language-sql js-code-highlight">'. esc_html($current['mysql_query']) .'</code></pre></td></tr>';
					}

					if ( isset( $current['items_found'] ) ) {
						$out .= '<tr><td>' . __('Items found', 'wpv-views') . '</td><td>'. $current['items_found'] .'</td></tr>';
					}

					if ( isset($current['query_results']) && !empty($current['query_results']) &&  $this->show_type == 'full'){
						$out .= '<tr><td>' . __('Query results', 'wpv-views') . '</td><td><button class="js-show-code wpv-code-toggle wpv-debug-toggle" data-text-disabled="'.__('Loading','wpv-views').'&hellip;" data-text-expanded="'.__('Hide','wpv-views').'" data-text-collapsed="'.__('Show','wpv-views').'">'.__('Show','wpv-views').'</button><pre class="js-code-wrapper"><code class="language-php js-code-highlight">'. esc_html($current['query_results']) .'</code></pre></td></tr>';
					}

					if ( isset($current['filters_short']) && is_array($current['filters_short'])  &&  $this->show_type == 'full' ){

						$out .= '<tr><td>' . __('Filters', 'wpv-views') . '</td><td>';
						$filter_count = 0;
						$filter_count = count($current['filters_short']);
						for ($h=0;$h<$filter_count;$h++){
							$out .= '<div>'.$current['filters_short'][$h].' <button class="js-show-code wpv-code-toggle wpv-debug-toggle" data-text-disabled="'.__('Loading','wpv-views').'&hellip;" data-text-expanded="'.__('Hide','wpv-views').'" data-text-collapsed="'.__('Show','wpv-views').'">'.__('Show','wpv-views').'</button><pre class="js-code-wrapper"><code class="language-php js-code-highlight">'. esc_html($current['filters'][$h]) .'</code></pre></div>';
						}
						$out .= '</td></tr>';
					}

					//Show Views that added outside loop
					if ( isset( $this->log_array[($level+1)] ) && count($this->log_array[($level+1)]) > 0){
						$out_loop = $this->log_array[$level+1];
						$zout = '';
						$loop_count = 0;
						$loop_count = count($out_loop);
						for ($p=0;$p<$loop_count; $p++){
							if ( $out_loop[$p]['view_parent'] == $current['view_id'] && $out_loop[$p]['parent_index'] == -1){
								$zout .= $this->generate_view_table($out_loop[$p],$level+1);
							}
						}
						if ( !empty($zout) ){
							$out .= '<tr><td>' . __('Views', 'wpv-views') . '</td><td>'.$zout.'</td></tr>';
						}

					}

					//Start Loop
					if ( isset($current['log']) && count($current['log']) > 0 ) {
						$loop_label = $kind . ' ' . __('Loop', 'wpv-views');
						if ( $current['type'] == 'content-template' ) {
							$loop_label = __('Template results', 'wpv-views');
							if ( $current['view_id'] == 0 ) {
								$loop_label = __('Post body data', 'wpv-views');
							}
						}
						$table_id = str_replace( '.','', uniqid('table-', true )); // generate unique ID attributes. Str replace is to remove dot character which is not valid for ID attributes.
						$out .= '<tr><td valign=top colspan=2 class="wpv-debug-loop"><p class="wpv-debug-toggle-wrap"><button class="js-show-view wpv-debug-toggle" data-target="#'. $table_id .'" data-expanded="false"><span class="js-wpv-debug-corner">+</span> ' . $loop_label . '</button></p>';
						$out .= '<div class="wpv-loop-table-wrapper" id="'. $table_id .'">';

							$log_count = 0;
							$log_count = count($current['log']);
							for ($k=1;$k<=$log_count;$k++){
							$data = $current['log'][$k];

							$out .= '<table cellpadding="4" class="wpv-debug-table">';
							if ( isset($data['title']) ) {
								$out .= '<tr><td>' . __('Title', 'wpv-views') . '</td><td>'.  str_replace("'",'&#39;',$data['title']) .'</td></tr>';
							}
							if ( $this->show_type == 'full' ){
								$out .= '<tr><td>' . __('Received array', 'wpv-views') . '</td><td>';
								$out .= '<button class="js-show-code wpv-code-toggle wpv-debug-toggle" data-text-disabled="'.__('Loading','wpv-views').'&hellip;" data-text-expanded="'.__('Hide','wpv-views').'" data-text-collapsed="'.__('Show','wpv-views').'">'.__('Show','wpv-views').'</button>';
								$out .= '<pre class="js-code-wrapper"><code class="language-php js-code-highlight">';
								$out .= isset($data['data_array']) ? $data['data_array'] : '';
								$out .= '</code></pre></td></tr>';
							}
							$out .= '<tr><td>' . __('Original content', 'wpv-views') . '</td><td><code class="language-markup js-code-highlight">'. esc_attr( isset($data['shortcodes'])? $data['shortcodes']:'' ).'</code></td></tr>';
							if ( isset($data['shortcode_info']) && count($data['shortcode_info']) > 0){
								$out .= '<tr><td>' . __('Shortcodes', 'wpv-views') . '</td><td>';
								$shortcode_info_length = count($data['shortcode_info']);
								for ( $j=0; $j<$shortcode_info_length; $j++){
									$out .= '<div>'. $data['shortcode_info'][$j]['shortcode'] .': '.
									'<button class="js-show-code wpv-code-toggle wpv-debug-toggle" data-text-disabled="'.__('Loading','wpv-views').'&hellip;" data-text-expanded="'.__('Hide','wpv-views').'" data-text-collapsed="'.__('Show','wpv-views').'">'.__('Show','wpv-views').'</button><pre class="js-code-wrapper"><code class="language-php js-code-highlight">';
										$out .= (!empty($data['shortcode_info'][$j]['atts']) && $data['shortcode_info'][$j]['atts'] != '""') ? __('Attributes', 'wpv-views') . ": " . esc_attr( $data['shortcode_info'][$j]['atts'] ) . "\n" : "";
										$out .= !empty($data['shortcode_info'][$j]['query']) ? __('Query', 'wpv-views') . ": " . esc_attr( $data['shortcode_info'][$j]['query'] ) . "\n" : "";
										$out .= !empty($data['shortcode_info'][$j]['info']) ? __('Info', 'wpv-views') . ": " . esc_attr( $data['shortcode_info'][$j]['info'] ) . "\n" : "";
										$out .= !empty($data['shortcode_info'][$j]['additional']) ? __('Output', 'wpv-views') . ": " . esc_attr( $data['shortcode_info'][$j]['additional'] ) : "";
									$out .= '</code></pre></div>';
								}
								$out .= '</td></tr>';
							}
							if ( count($this->log_array) > $level ){
								$nested = '';
								$nested = $this->generate_output( ($level+1), $current['view_id'], $k );
								if ( isset( $nested ) && !empty( $nested ) ) {
									$out .= '<tr><td>' . __('Nested elements', 'wpv-views') . '</td><td>'. $nested .'</td></tr>';
								}
							}
							if ( isset( $data['output'] ) ) {
								$out .= '<tr><td>' . __('Output (RAW)', 'wpv-views') . '</td><td><code class="language-markup js-code-highlight">' . esc_attr( $data['output'] ) . '</code></td></tr>';
							}
							$out .= '</table>';
						}
						$out .= '</div></td></tr>'; //End loop
					}

					$out .= '</table>';
		return $out;
	}

	function wpv_shortcode_debug_callback( $shortcode,  $atts = "", $query = "", $info = "", $additional = "" ){
		if ( !$this->status || !$this->user_can_debug() ){return;}
		if ( isset($this->log_array[$this->depth][$this->get_index()]['current_index']) &&
		isset($this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']]) &&
			count($this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']]) > 0 ){
				$index = count($this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']]);

				$shortcode = array(
					'shortcode' => $shortcode,
					'atts' => $atts,
					'query' => $query,
					'info' => $info,
					'additional' => $additional
				);

				$this->log_array[$this->depth][$this->get_index()]['log'][$this->log_array[$this->depth][$this->get_index()]['current_index']]['shortcode_info'][] = $shortcode;
		}

	}

	function show_log(){
		if ( !$this->user_can_debug() ){return;}

	}

	function js(){
		if ( !$this->user_can_debug() ){return;}

	}
	function get_mysql_last(){
		if ( !$this->user_can_debug() ){return;}
		global $wpdb;
		$out = '';
		if ( !empty($wpdb->queries) ){
			$out = $wpdb->queries[(count($wpdb->queries)-1)][0];
		}
		return $out;
	}
}