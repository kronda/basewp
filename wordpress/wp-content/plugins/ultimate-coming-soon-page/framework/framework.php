<?php
/**
 * SeedProd Framework - Inspired by Yoast's Plugins and WooThemes Framework
 *
 * @package WordPress
 * @subpackage Ultimate_Coming_Soon_Page
 * @since 0.1
 */
if (!class_exists('SeedProd_Framework')) {
	class SeedProd_Framework {
	
        /**
         * Define the Version of the Plugin
         */
        public $plugin_version = '';
        public $plugin_type = ''; // free,lite and pro
        public $plugin_name = '';
        public $plugin_support_url = '';
        public $plugin_short_url = '';
        public $plugin_seedprod_url = '';
        public $plugin_donate_url = '';
        public $plugin_official_url = '';
        private $framework_version = '0.1';

        /**
         * Define if we are deploying a theme and add the theme params
         */
        public $deploy_theme = 0;
        public $deploy_theme_name = array('template' =>'', 'stylesheet' => '');

        /**
         * Global we set in seedprod_admin_enqueue_scripts and use in create_menu
         */
        public $pages = array();

        /**
         *  Define the menus that will be rendered.
         *  Do not replace callback function.
         */
        public $menu = array();
        
        /**
         *  Define options, sections and fields
         */
        public $options = array();
	
    	/**
    	 * Load Hooks
    	 */
    	function __construct() {
    	    add_action('admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts'));
    	    add_action('admin_menu',array(&$this,'create_menu'));
    	    add_action('admin_init', array(&$this,'set_settings'));
    	}
    	
    	/**
         * Set the base url to use in the plugin
         *
         * @since  0.1
         * @return string
         */
    	function base_url(){
            return plugins_url('',dirname(__FILE__));
        }
    	    
	
        /**
         * Properly enqueue styles and scripts for our theme options page.
         *
         * This function is attached to the admin_enqueue_scripts action hook.
         *
         * @since  0.1
         * @param string $hook_suffix The name of the current page we are on.
         */
        function admin_enqueue_scripts( $hook_suffix ) {
            wp_enqueue_style( 'seedprod_mm_plugin', plugins_url('inc/css/admin-style.css',dirname(__FILE__)), false, $this->plugin_version );
            if(!in_array($hook_suffix, $this->pages))
                return;
            wp_enqueue_script('dashboard');
        	wp_enqueue_script( 'seedprod_framework', plugins_url('framework.js',__FILE__), array( 'jquery','media-upload','thickbox','farbtastic' ), $this->plugin_version );
        	wp_enqueue_style( 'seedprod_framework', plugins_url('framework.css',__FILE__), false, $this->plugin_version );
        	wp_enqueue_script( 'seedprod_plugin', plugins_url('inc/js/admin-script.js',dirname(__FILE__)), array( 'jquery','media-upload','thickbox','farbtastic' ), $this->plugin_version );
        	wp_enqueue_style( 'seedprod_plugin', plugins_url('inc/css/admin-style.css',dirname(__FILE__)), false, $this->plugin_version );
        	wp_enqueue_style('thickbox');
            wp_enqueue_script( 'theme-preview' );
            wp_enqueue_style('farbtastic'); 
        }

        /**
         * Creates WordPress Menu pages from an array in the config file.
         *
         * This function is attached to the admin_menu action hook.
         *
         * @since 0.1
         */
        function create_menu(){
            foreach ($this->menu as $v) {
                $this->pages[] = call_user_func_array($v['type'],array($v['page_name'],$v['menu_name'],$v['capability'],$v['menu_slug'],$v['callback'],$v['icon_url']));
            }
    
        }

        /**
         * Render the option pages.
         *
         * @since 0.1
         */
        function option_page() {
            $page = $_REQUEST['page'];
        	?>
        	<div class="wrap seedprod">
        	    <?php screen_icon(); ?>
        		<h2><?php echo $this->plugin_name; ?> </h2>
                <a href="http://www.seedprod.com/ultimate-coming-soon-page-vs-coming-soon-pro/?utm_source=plugin&utm_medium=banner&utm_campaign=coming-soon-pro-in-plugin-banner" target="_blank">
                <img src="http://static.seedprod.com.s3.amazonaws.com/ads/ultimate-coming-soon-page-banner-772x250.jpg" style="max-width:100%">
                </a>
        		<?php //settings_errors(); ?> 
                <h2 class="nav-tab-wrapper" style="padding-left:20px">
                    <a class="nav-tab seed_csp3-support" href="options-general.php?page=seedprod_coming_soon"><?php _e('Settings','ultimate-coming-soon-page') ?></a>
                    <a class="nav-tab seed_csp3-preview thickbox-preview" href="<?php echo home_url(); ?>?cs_preview=true&TB_iframe=true&width=640&height=632" title="<?php _e('&larr; Close Window','ultimate-coming-soon-page') ?>"><?php _e('Live Preview','ultimate-coming-soon-page'); ?></a>
                </h2>
        		<div id="poststuff" class="metabox-holder">
                    <!--<div id="side-info-column" class="inner-sidebar">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
					     	<a href="http://www.seedprod.com/plugins/wordpress-coming-soon-pro-plugin/?utm_source=plugin&utm_medium=banner&utm_campaign=coming-soon-pro-in-plugin-banner" target="_blank"><img src="http://static.seedprod.com/ads/coming-soon-pro-sidebar.png" /></a>
                            <br><br>
                            <div class="postbox support-postbox">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                				<h3 class="hndle"><span><?php _e('Plugin Support', 'ultimate-coming-soon-page') ?></span></h3>
                				<div class="inside">
                					<div class="support-widget">
                					<p>
                					   <?php _e('Got a Question, Idea, Problem or Praise?') ?>
                					</p>
                					<ul>
                					    <li>&raquo; <a href="<?php echo (empty($this->plugin_support_url) ? 'http://seedprod.com/support/' : $this->plugin_support_url) ?>" target="_blank"><?php _e('Support Request', 'ultimate-coming-soon-page') ?></a></li>
                				    </ul>
                					
                					</div>
                				</div>
                            </div>
                            <?php if($this->plugin_type != 'pro'){ ?>
                            <div class="postbox like-postbox">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                				<h3 class="hndle"><span><?php _e('Show Some Love', 'ultimate-coming-soon-page') ?></span></h3>
                				<div class="inside">
                					<div class="like-widget">
                					<p><?php _e('Like this plugin? Show your support by:', 'ultimate-coming-soon-page')?></p>
                					<ul>
                                        <li>&raquo; <a href="https://www.seedprod.com/submit-site/"><?php _e('Submit your site to the Showcase', 'ultimate-coming-soon-page') ?></a></li>
                					    <li>&raquo; <a target="_blank" href="http://wordpress.org/extend/plugins/ultimate-coming-soon-page/"><?php _e('Rating It', 'ultimate-coming-soon-page') ?></a></li>
                					    <li>&raquo; <a target="_blank" href="<?php echo "http://twitter.com/share?url={$this->plugin_seedprod_url}&text=Check out this awesome WordPress Plugin I'm using, 'Ultimate Coming Soon Page' by SeedProd {$this->plugin_short_url}"; ?>"><?php _e('Tweet It', 'ultimate-coming-soon-page') ?></a></li>
                					    
                					    
                					   
                					</ul>
                					</div>
                				</div>
                            </div>
                            <?php } ?>
                            <div class="postbox rss-postbox">
                                <div class="handlediv" title="Click to toggle"><br /></div>
                				<h3 class="hndle"><span><?php _e('SeedProd Blog', 'ultimate-coming-soon-page') ?></span></h3>
                				<div class="inside">
                					<div class="rss-widget">
                					<?php
                					wp_widget_rss_output(array(
                					   'url' => 'http://seedprod.com/feed/',
                					   'title' => 'SeedProd Blog',
                					   'items' => 3,
                					   'show_summary' => 0,
                					   'show_author' => 0,
                					   'show_date' => 1,
                					));
                					?>
            					    <ul>
                					    <li>&raquo; <a href="http://seedprod.com/subscribe/"><?php _e('Subscribe by Email', 'ultimate-coming-soon-page') ?></a></li>
                				    </ul>
                					</div>
                				</div>
                            </div>
                            
                        </div>
                    </div>-->
                    <div id="post-body">
                        <div id="post-body-content" >
                            <div class="meta-box-sortables ui-sortable">
                                <form action="options.php" method="post">
                                <?php
                                foreach ($this->options as $v) {
                                    if(isset($v['menu_slug'])){
                                        if($v['menu_slug'] == $page){
                                            switch ($v['type']) {
                                                case 'setting':
                            				        settings_fields($v['id']);
                            				        break;
                            				    case 'section':
                            				        echo '<div class="postbox seedprod-postbox"><div class="handlediv" title="Click to toggle"><br /></div>';
                                            		$this->seedprod_do_settings_sections($v['id']);
                                        		    echo '</div>';
                                        		    break;
                        		    
                            		        }
                    		            }
            		                }
                                }
                                ?>
                        		
                        	    </form>
                            </div>
                        </div>
                    </div>
                </div>
        	</div>	
        	<?php
        }

        /**
         * Create the settings options, sections and fields via the WordPress Settings API
         *
         * This function is attached to the admin_init action hook.
         *
         * @since 0.1
         */
        function set_settings(){
            foreach ($this->options as $k) {
                switch ($k['type']) {
                    case 'setting':
                        if(empty($k['validate_function'])){
                	        $k['validate_function'] = array(&$this,'validate_machine');
                	    }
                    	register_setting(
                    		$k['id'],
                    		$k['id'],
                    		$k['validate_function']
                    	);
                    	break;
                	case 'section':
                	    if(empty($k['desc_callback'])){
                	        $k['desc_callback'] = array(&$this,'section_dummy_desc');
                	    }else{
                	        $k['desc_callback'] = array(&$this, $k['desc_callback']);
                	    }
                    	add_settings_section(
                    		$k['id'],
                    		$k['label'],
                    		$k['desc_callback'],
                    		$k['id']
                    	);
                    	break;
                	default:
                    	if(empty($k['callback'])){
                	        $k['callback'] = array(&$this,'field_machine');
                	    }
                    	add_settings_field(
                    		$k['id'],
                    		$k['label'],
                    		$k['callback'],
                    		$k['section_id'],
                    		$k['section_id'],
                    		array('id' => $k['id'], 
                    		'desc' => (isset($k['desc']) ? $k['desc'] : ''),
                    		'setting_id' => $k['setting_id'], 
                    		'class' => (isset($k['class']) ? $k['class'] : ''), 
                    		'type' => $k['type'],
                    		'default_value' => (isset($k['default_value']) ? $k['default_value'] : ''),
                    		'option_values' => (isset($k['option_values']) ? $k['option_values'] : ''))
                    	);
                	    
        	    }
            }
        }

        /**
         * Create a field based on the field type passed in.
         *
         * @since 0.1
         */
        function field_machine($args) {
            extract($args);
        	$options = get_option( $setting_id );
        	switch($type){
        	    case 'textbox':
        	        echo "<input id='$id' class='".(empty($class) ? 'regular-text' : $class)."' name='{$setting_id}[$id]' type='text' value='".esc_attr(empty($options[$id]) ? $default_value : $options[$id])."' />
        	        <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
                case 'image':
        	        echo "<input id='$id' class='".(empty($class) ? 'regular-text' : $class)."' name='{$setting_id}[$id]' type='text' value='".(empty($options[$id]) ? $default_value : $options[$id])."' />
        	        <input id='{$id}_upload_image_button' class='button-secondary upload-button' type='button' value='". __('Media Image Library', 'ultimate-coming-soon-page')."' />
        	        <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>
        	        ";
        	        break;
        	    case 'select':
            	    echo "<select id='$id' class='".(empty($class) ? '' : $class)."' name='{$setting_id}[$id]'>";
            	    foreach($option_values as $k=>$v){
            	        if(preg_match("/optgroupend/i",$k)){
            	            echo "</optgroup>";
            	        }else{
            	            if(preg_match("/optgroup/i",$k)){
                	            echo "<optgroup label='$v'>";
                	        }else{

                	            if(preg_match("/empty/i",$k) && empty($default_value)){             
                	                echo "<option value=''>$v</option>";
                	            }else{
            	                    echo "<option value='$k' ".((preg_match("/empty/i",$options[$id] || isset($options[$id]) === false) ? $default_value : $options[$id]) == $k ? 'selected' : '').">$v</option>";
        	                    }
        	                }
        	            }

            	    }
            	    echo "</select>
                    <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
                    break;
        	    case 'textarea':
                    echo "<textarea id='$id' class='".(empty($class) ? '' : $class)."' name='{$setting_id}[$id]'>".(empty($options[$id]) ? $default_value : $options[$id])."</textarea>
        	        <br><small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;

                case 'wpeditor':
                    $content   = $options[ $id ];
                    $editor_id = $id;
                    $args      = array(
                         'textarea_name' => "{$setting_id}[$id]" 
                    ); 

                    wp_editor( $content, $editor_id, $args );

                    break;
        	    case 'radio':
        	        foreach($option_values as $k=>$v){
        	            echo "<input type='radio' name='{$setting_id}[$id]' value='$k'".((empty($options[$id]) ? $default_value : $options[$id]) == $k ? 'checked' : '')."  /> $v<br/>";
                    }
        	        echo "<small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
        	    case 'checkbox':
        	        $count = 0;
        	        foreach($option_values as $k=>$v){
        	            echo "<input type='checkbox' name='{$setting_id}[$id][]' value='$k'".(in_array($k,(empty($options[$id]) ? (empty($default_value) ? array(): $default_value) : $options[$id])) ? 'checked' : '')."  /> $v<br/>";
                        $count++;
                    }
        	        echo "<small class='description'>".(empty($desc) ? '' : $desc)."</small>";
        	        break;
        	    case 'color':
        	        echo "
            	        <input id='$id' type='text' name='{$setting_id}[$id]' value='".(empty($options[$id]) ? $default_value : $options[$id])."' style='background-color:".(empty($options[$id]) ? $default_value : $options[$id]).";' />
                        <input type='button' class='pickcolor button-secondary' value='Select Color'>
                        <div id='colorpicker' style='z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;'></div>
                        <br />
                        <small class='description'>".(empty($desc) ? '' : $desc)."</small>
                        ";
        	        break;
        	}
	
        }

        /**
         * Validates user input before we save it via the Options API. If error add_setting_error
         *
         * @since 0.1
         * @param array $input Contains all the values submited to the POST.
         * @return array $input Contains sanitized values.
         * @todo Figure out best way to validate values.
         */
        function validate_machine($input) {
            $error = false;
            foreach ($this->options as $k) {
                switch($k['type']){
                    case 'setting':
                        break;
                    case 'section':
                        break;
                    default:
                        // Validate a pattern
                        if(isset($pattern) && $pattern){
                    	    if(!preg_match( $pattern, $input[$k['id']])) {
                    	        $error = true;
                        		add_settings_error(
                        			$k['id'],
                        			'seedprod_error',
                        			$k['error_msg'],
                        			'error'
                        		);
                        		unset($input[$k['id']]);
                        	}		
                        }
                        // Sanitize 
                	    if($k['type'] == 'image'){
                	        $input[$k['id']] = esc_url_raw($input[$k['id']]);
                	    }
        	    }
            }
            if(!$error){
 				global $wp_settings_errors;
				$display = true;
				if(!empty($wp_settings_errors)){
					foreach($wp_settings_errors as $k=>$v){
						if($v['code'] == 'seedprod_settings_updated')
							$display = false;
					}
				}
				if($display)
		        	add_settings_error('general', 'seedprod_settings_updated', sprintf(__("Settings saved.  <a href='%s/?cs_preview=true'>Preview &raquo;</a>", 'ultimate-coming-soon-page'),home_url()), 'updated');
            }
        	return $input;
        }

        /**
         * Dummy function to be called by all sections from the Settings API. Define a custom function in the config.
         *
         * @since 0.1
         * @return string Empty
         */
        function section_dummy_desc() {
        	echo '';
        }
        
        /**
         * Returns Font Families
         *
         * @since 0.1
         * @return string or array
         */
        function font_families($family=null) {
            $fonts = array();
            $fonts['_arial'] = 'Helvetica, Arial, sans-serif';
            $fonts['_arial_black'] = 'Arial Black, Arial Black, Gadget, sans-serif';
            $fonts['_georgia'] = 'Georgia,serif';
            $fonts['_helvetica_neue'] = '"Helvetica Neue", Helvetica, Arial, sans-serif';
            $fonts['_impact'] = 'Charcoal,Impact,sans-serif';
            $fonts['_lucida'] = 'Lucida Grande,Lucida Sans Unicode, sans-serif';
            $fonts['_palatino'] = 'Palatino,Palatino Linotype, Book Antiqua, serif';
            $fonts['_tahoma'] = 'Geneva,Tahoma,sans-serif';
            $fonts['_times'] = 'Times,Times New Roman, serif';
            $fonts['_trebuchet'] = 'Trebuchet MS, sans-serif';
            $fonts['_verdana'] = 'Verdana, Geneva, sans-serif';
            if($family){
                $font_family=$fonts[$family];
                if(empty($font_family)){
                    $font_family = '"'. urldecode($family) . '",sans-serif' ;
                }
            }else{
                $font_family=$fonts;  
            }
        	return $font_family;
        }
        
        /**
         * Get list of fonts from google and web safe fonts.
         *
         * @since 0.1
         * @return array 
         */
         function font_field_list($show_google_fonts = true){
              $fonts = unserialize(get_transient('seedprod_fonts'));
              if($fonts === false){
                  if($show_google_fonts){
                      //$query = urlencode('select * from html where url="http://www.google.com/webfonts" and xpath=\'//div[@class="preview"]/span\'');
                      //$request = "http://query.yahooapis.com/v1/public/yql?q={$query}&format=json";
                      //$reponse = wp_remote_get($request);
                      //$result = json_decode($reponse['body']);
                      $result = array("ABeeZee","Abel","Abril Fatface","Aclonica","Acme","Actor","Adamina","Advent Pro","Aguafina Script","Akronim","Aladin","Aldrich","Alegreya","Alegreya SC","Alex Brush","Alfa Slab One","Alice","Alike","Alike Angular","Allan","Allerta","Allerta Stencil","Allura","Almendra","Almendra Display","Almendra SC","Amarante","Amaranth","Amatic SC","Amethysta","Anaheim","Andada","Andika","Annie Use Your Telescope","Anonymous Pro","Antic","Antic Didone","Antic Slab","Anton","Arapey","Arbutus","Arbutus Slab","Architects Daughter","Archivo Black","Archivo Narrow","Arimo","Arizonia","Armata","Artifika","Arvo","Asap","Asset","Astloch","Asul","Atomic Age","Aubrey","Audiowide","Autour One","Average","Average Sans","Averia Gruesa Libre","Averia Libre","Averia Sans Libre","Averia Serif Libre","Bad Script","Balthazar","Bangers","Basic","Baumans","Belgrano","Belleza","BenchNine","Bentham","Berkshire Swash","Bevan","Bigelow Rules","Bigshot One","Bilbo","Bilbo Swash Caps","Bitter","Black Ops One","Bonbon","Boogaloo","Bowlby One","Bowlby One SC","Brawler","Bree Serif","Bubblegum Sans","Bubbler One","Buda","Buenard","Butcherman","Butterfly Kids","Cabin","Cabin Condensed","Cabin Sketch","Caesar Dressing","Cagliostro","Calligraffitti","Cambo","Candal","Cantarell","Cantata One","Cantora One","Capriola","Cardo","Carme","Carrois Gothic","Carrois Gothic SC","Carter One","Caudex","Cedarville Cursive","Ceviche One","Changa One","Chango","Chau Philomene One","Chela One","Chelsea Market","Cherry Cream Soda","Cherry Swash","Chewy","Chicle","Chivo","Cinzel","Cinzel Decorative","Clicker Script","Coda","Coda Caption","Codystar","Combo","Comfortaa","Coming Soon","Concert One","Condiment","Contrail One","Convergence","Cookie","Copse","Corben","Courgette","Cousine","Coustard","Covered By Your Grace","Crafty Girls","Creepster","Crete Round","Crimson Text","Croissant One","Crushed","Cuprum","Cutive","Cutive Mono","Damion","Dancing Script","Dawning of a New Day","Days One","Delius","Delius Swash Caps","Delius Unicase","Della Respira","Denk One","Devonshire","Didact Gothic","Diplomata","Diplomata SC","Domine","Donegal One","Doppio One","Dorsa","Dosis","Dr Sugiyama","Droid Sans","Droid Sans Mono","Droid Serif","Duru Sans","Dynalight","EB Garamond","Eagle Lake","Eater","Economica","Electrolize","Elsie","Elsie Swash Caps","Emblema One","Emilys Candy","Engagement","Englebert","Enriqueta","Erica One","Esteban","Euphoria Script","Ewert","Exo","Expletus Sans","Fanwood Text","Fascinate","Fascinate Inline","Faster One","Federant","Federo","Felipa","Fenix","Finger Paint","Fjalla One","Fjord One","Flamenco","Flavors","Fondamento","Fontdiner Swanky","Forum","Francois One","Freckle Face","Fredericka the Great","Fredoka One","Fresca","Frijole","Fruktur","Fugaz One","Gabriela","Gafata","Galdeano","Galindo","Gentium Basic","Gentium Book Basic","Geo","Geostar","Geostar Fill","Germania One","Gilda Display","Give You Glory","Glass Antiqua","Glegoo","Gloria Hallelujah","Goblin One","Gochi Hand","Gorditas","Goudy Bookletter 1911","Graduate","Grand Hotel","Gravitas One","Great Vibes","Griffy","Gruppo","Gudea","Habibi","Hammersmith One","Hanalei","Hanalei Fill","Handlee","Happy Monkey","Headland One","Henny Penny","Herr Von Muellerhoff","Holtwood One SC","Homemade Apple","Homenaje","IM Fell DW Pica","IM Fell DW Pica SC","IM Fell Double Pica","IM Fell Double Pica SC","IM Fell English","IM Fell English SC","IM Fell French Canon","IM Fell French Canon SC","IM Fell Great Primer","IM Fell Great Primer SC","Iceberg","Iceland","Imprima","Inconsolata","Inder","Indie Flower","Inika","Irish Grover","Istok Web","Italiana","Italianno","Jacques Francois","Jacques Francois Shadow","Jim Nightshade","Jockey One","Jolly Lodger","Josefin Sans","Josefin Slab","Joti One","Judson","Julee","Julius Sans One","Junge","Jura","Just Another Hand","Just Me Again Down Here","Kameron","Karla","Kaushan Script","Kavoon","Keania One","Kelly Slab","Kenia","Kite One","Knewave","Kotta One","Kranky","Kreon","Kristi","Krona One","La Belle Aurore","Lancelot","Lato","League Script","Leckerli One","Ledger","Lekton","Lemon","Libre Baskerville","Life Savers","Lilita One","Limelight","Linden Hill","Lobster","Lobster Two","Londrina Outline","Londrina Shadow","Londrina Sketch","Londrina Solid","Lora","Love Ya Like A Sister","Loved by the King","Lovers Quarrel","Luckiest Guy","Lusitana","Lustria","Macondo","Macondo Swash Caps","Magra","Maiden Orange","Mako","Marcellus","Marcellus SC","Marck Script","Margarine","Marko One","Marmelad","Marvel","Mate","Mate SC","Maven Pro","McLaren","Meddon","MedievalSharp","Medula One","Megrim","Meie Script","Merienda","Merienda One","Merriweather","Merriweather Sans","Metal Mania","Metamorphous","Metrophobic","Michroma","Milonga","Miltonian","Miltonian Tattoo","Miniver","Miss Fajardose","Modern Antiqua","Molengo","Molle","Monda","Monofett","Monoton","Monsieur La Doulaise","Montaga","Montez","Montserrat","Montserrat Alternates","Montserrat Subrayada","Mountains of Christmas","Mouse Memoirs","Mr Bedfort","Mr Dafoe","Mr De Haviland","Mrs Saint Delafield","Mrs Sheppards","Muli","Mystery Quest","Neucha","Neuton","New Rocker","News Cycle","Niconne","Nixie One","Nobile","Norican","Nosifer","Nothing You Could Do","Noticia Text","Nova Cut","Nova Flat","Nova Mono","Nova Oval","Nova Round","Nova Script","Nova Slim","Nova Square","Numans","Nunito","Offside","Old Standard TT","Oldenburg","Oleo Script","Oleo Script Swash Caps","Open Sans","Open Sans Condensed","Oranienbaum","Orbitron","Oregano","Orienta","Original Surfer","Oswald","Over the Rainbow","Overlock","Overlock SC","Ovo","Oxygen","Oxygen Mono","PT Mono","PT Sans","PT Sans Caption","PT Sans Narrow","PT Serif","PT Serif Caption","Pacifico","Paprika","Parisienne","Passero One","Passion One","Patrick Hand","Patrick Hand SC","Patua One","Paytone One","Peralta","Permanent Marker","Petit Formal Script","Petrona","Philosopher","Piedra","Pinyon Script","Pirata One","Plaster","Play","Playball","Playfair Display","Playfair Display SC","Podkova","Poiret One","Poller One","Poly","Pompiere","Pontano Sans","Port Lligat Sans","Port Lligat Slab","Prata","Press Start 2P","Princess Sofia","Prociono","Prosto One","Puritan","Purple Purse","Quando","Quantico","Quattrocento","Quattrocento Sans","Questrial","Quicksand","Quintessential","Qwigley","Racing Sans One","Radley","Raleway","Raleway Dots","Rambla","Rammetto One","Ranchers","Rancho","Rationale","Redressed","Reenie Beanie","Revalia","Ribeye","Ribeye Marrow","Righteous","Risque","Roboto","Roboto Condensed","Rochester","Rock Salt","Rokkitt","Romanesco","Ropa Sans","Rosario","Rosarivo","Rouge Script","Ruda","Rufina","Ruge Boogie","Ruluko","Rum Raisin","Ruslan Display","Russo One","Ruthie","Rye","Sacramento","Sail","Salsa","Sanchez","Sancreek","Sansita One","Sarina","Satisfy","Scada","Schoolbell","Seaweed Script","Sevillana","Seymour One","Shadows Into Light","Shadows Into Light Two","Shanti","Share","Share Tech","Share Tech Mono","Shojumaru","Short Stack","Sigmar One","Signika","Signika Negative","Simonetta","Sintony","Sirin Stencil","Six Caps","Skranji","Slackey","Smokum","Smythe","Sniglet","Snippet","Snowburst One","Sofadi One","Sofia","Sonsie One","Sorts Mill Goudy","Source Code Pro","Source Sans Pro","Special Elite","Spicy Rice","Spinnaker","Spirax","Squada One","Stalemate","Stalinist One","Stardos Stencil","Stint Ultra Condensed","Stint Ultra Expanded","Stoke","Strait","Sue Ellen Francisco","Sunshiney","Supermercado One","Swanky and Moo Moo","Syncopate","Tangerine","Tauri","Telex","Tenor Sans","Text Me One","The Girl Next Door","Tienne","Tinos","Titan One","Titillium Web","Trade Winds","Trocchi","Trochut","Trykker","Tulpen One","Ubuntu","Ubuntu Condensed","Ubuntu Mono","Ultra","Uncial Antiqua","Underdog","Unica One","UnifrakturCook","UnifrakturMaguntia","Unkempt","Unlock","Unna","VT323","Vampiro One","Varela","Varela Round","Vast Shadow","Vibur","Vidaloka","Viga","Voces","Volkhov","Vollkorn","Voltaire","Waiting for the Sunrise","Wallpoet","Walter Turncoat","Warnes","Wellfleet","Wendy One","Wire One","Yanone Kaffeesatz","Yellowtail","Yeseva One","Yesteryear","Zeyada");
                      foreach($result as $v){
                         $google_fonts[urlencode($v)] = $v;
                      }
                      asort($google_fonts);
                      $pre2["optgroup_2"] = "Google Fonts";
                      $post2["optgroupend_2"] = "";
                 }
                 $post1["optgroupend_1"] = "";
                 $system_fonts['_arial'] = 'Arial';
                 $system_fonts['_arial_black'] = 'Arial Black';
                 $system_fonts['_georgia'] = 'Georgia';
                 $system_fonts['_helvetica_neue'] = 'Helvetica Neue';
                 $system_fonts['_impact'] = 'Impact';
                 $system_fonts['_lucida'] = 'Lucida Grande';
                 $system_fonts['_palatino'] = 'Palatino';
                 $system_fonts['_tahoma'] = 'Tahoma';
                 $system_fonts['_times'] = 'Times New Roman';
                 $system_fonts['_trebuchet'] = 'Trebuchet';
                 $system_fonts['_verdana'] = 'Verdana';
                 $pre0["empty_0"] = "Select a Font";
                 $pre1["optgroup_1"] = "System Fonts";
                 $pre2["optgroup_2"] = "Google Fonts";
                 $fonts =  $pre0 + $pre1 + $system_fonts+ $post1+ $pre2 + $google_fonts + $post2;
                 if(!empty($google_fonts)){
                     set_transient('seedprod_fonts',serialize( $fonts ),86400);
                }
             }
             return $fonts;
         }
         
         /**
          * SeedProd version of WP's do_settings_sections
          *
          * @since 0.1
          */
         function seedprod_do_settings_sections($page) {
             global $wp_settings_sections, $wp_settings_fields;

             if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
                 return;

             foreach ( (array) $wp_settings_sections[$page] as $section ) {
                 echo "<h3 class='hndle'>{$section['title']}</h3>\n";
                 echo '<div class="inside">';
                 call_user_func($section['callback'], $section);
                 if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
                     continue;
                 echo '<table class="form-table">';
                 do_settings_fields($page, $section['id']);
                 echo '</table>';
                 echo '<p>';
                 echo "<input name=\"Submit\" type=\"submit\" value=\"". __('Save Changes', 'ultimate-coming-soon-page') ."\" class=\"button-primary\"/>";
                 echo '</p>';
                 echo '</div>';
             }
         }

    }
}
?>