<?php

class Vum_form {

    var $fieldList, $navList, $topLvlTabs = 0, $formTitle;

    function __construct( $title = '' ) {
        $this->formTitle = $title;
    }

    function setPluginURL( $url = '' ) {
        $this->pluginURL = $url;
    }

    function addTextbox( $id, $title, $desc, $default = '' ) {
        $this->fieldList[ $id ] = $this->_array( 'textbox', $title, $id, $desc, array( ), $default );
        return $id;
    }

    function addYesNo( $id, $title, $desc, $default = '1' ) {
        $this->addRadioGroup( $id, $title, $desc, array( 1 => 'Yes', 0 => 'No' ), $default );
        return $id;
    }

    function addDropdown( $id, $title, $desc, $options = array( ), $default = false ) {
        $this->fieldList[ $id ] = $this->_array( 'dropdown', $title, $id, $desc, $options, $default );
    }

    function addTextarea( $id, $title, $desc, $default = '' ) {
        $this->fieldList[ $id ] = $this->_array( 'textarea', $title, $id, $desc, array( ), $default );
    }

    function addRadioGroup( $id, $title, $desc, $options = array( ), $default = false ) {
        $this->fieldList[ $id ] = $this->_array( 'radio', $title, $id, $desc, $options, $default );
    }

    function localVideos() {
        $this->fieldList[ 'local' ] = '';
    }

    function addHeading( $title, $tag = 'h2' ) {
        $this->fieldList[ ] = $this->_array( 'title', $title, $tag );
    }

    function html( $html ) {
        $this->fieldList[ ] = $this->_array( 'html', $html );
    }

    function addClass( $fieldId, $class ) {
        $this->fieldList[ $fieldId ] [ 'classes' ] [ ] = $class;
    }

    function openTab( $title = '' ) {
        $this->navList[ $this->topLvlTabs ] = $this->_array( 'sectionOpen', $title, 'tabs-' . $this->topLvlTabs );
        $this->fieldList[ ] = $this->_array( 'sectionOpen', $title, 'tabs-' . $this->topLvlTabs );
        $this->topLvlTabs++;
    }

    function closeTab() {
        $this->closeSection();
    }

    function openSection( $title = '', $id = '' ) {
        // Add item to array 
        $this->fieldList[ ] = $this->_array( 'sectionOpen', $title, $id );

        // return the ID of this item.
        $keys = array_keys( $this->fieldList );
        return end( $keys );
    }

    function closeSection() {
        $this->fieldList[ ] = $this->_array( 'sectionClose' );
    }

    function fields() {
        $return = array( );

        foreach ( $this->fieldList as $key => $val ):

            // Fields with an integer mean they're a heading, div etc. Not an input. 
            if ( !is_int( $key ) )
                $return[ $key ] = $val;

        endforeach;

        return $return;
    }

    function display() {
        Vum_form_html::openForm();

        Vum_form_html::title( array( 'title' => '<img style="vertical-align: -7px;margin-right:7px" src="' . $this->pluginURL . '/images/vum-logo-32.png">' . $this->formTitle, 'id' => 'h2' ) );

        Vum_form_html::tabNav( $this->navList );

        //var_dump($this->fieldList);die;

        foreach ( $this->fieldList as $key => $item ):
            Vum_form_html::$item[ 'type' ]( $item );
        endforeach;

        Vum_form_html::closeForm();
    }

    function getVal( $id ) {
        return stripslashes( get_option( 'wpm_o_' . $id, 'na' ) );
    }

    function _array( $type = '', $title = '', $id = 0, $desc = '', $options = array( ), $default = false ) {
        $ar = array(
            'type' => $type,
            'title' => $title,
            'id' => $id,
            'desc' => $desc,
            'value' => ( $this->getVal( $id ) == 'na' ? $default : $this->getVal( $id ) ),
            'options' => $options,
            'dbName' => 'wpm_o_' . $id,
            'classes' => array( )
        );
        return $ar;
    }

}

Class Vum_form_html
{
    static function openForm($method='post')
    { ?>
    <div class="wrap">
    <form id="wpm_form" method="<?php echo $method;?>" action="<?php echo admin_url( 'admin.php?page=vum-options' ); ?>">
    <?php wp_nonce_field('vum_nonce','vum_save'); ?>
    <div id="tabs">
  <?php }

    static function closeForm()
    {
        echo '<p class="submit"><input type="submit" name="submit-button" class="button-primary" value="Save Changes" /></p>';
        echo '<input type="hidden" name="return" id="return" value="" />';
        echo '<p> <a href="admin.php?page=vum-reset" onclick="return confirm(\'Are you sure you want to reset the plugin?\')">Reset the plugin</a> <em> please note you will lose all your settings, but this will not effect your master profile. </em> </p>';
        echo '</div>'; // Closes #tabs
        echo '</form>';
        echo '</div>'; // Close #wrap
    }
    static function tabNav($atts=array())
    {   ?>
      	<ul>
            <?php foreach($atts as $id=>$navItem): ?>
		<li><a href="#tabs-<?php echo $id;?>"><?php echo $navItem['title'];?></a></li>
            <?php endforeach; ?>
	</ul>
        <br />
    <?php }
    static function textbox($atts)
    {
        extract($atts); ?>
            <div class="wpm_input wpm_text<?php self::applyClasses($classes);?>" id="<?php echo $id; ?>">
                <label for="<?php echo $id; ?>"><?php echo $title; ?></label>
                <div class="wpm_form_item <?php echo $id; ?>">
                    <input name="<?php echo $id; ?>" type="text" value="<?php echo $value; ?>" /><br />
                    <small><?php echo $desc; ?></small>
                </div>
            </div>
    <?php }
    static function textarea($atts)
    {
        extract($atts); ?>
            <div class="wpm_input wpm_text">
                <label for="<?php echo $id; ?>"><?php echo $title; ?></label>
                <div class="wpm_form_item wpm_form_textarea_container">
                    <textarea name="<?php echo $id; ?>"><?php echo $value; ?></textarea><br />
                    <small><?php echo $desc; ?></small>
                </div>
            </div>
    <?php }
     static function dropdown($atts)
    {
        extract($atts); ?>
            <div class="wpm_input wpm_text">
                <label for="<?php echo $id; ?>"><?php echo $title; ?></label>
                <div class="wpm_form_item">
                    <select name="<?php echo $id; ?>">

                            <?php foreach($options as $k=>$v): ?>
                            <option value="<?php echo $k;?>"<?php
                            if($k == $value){echo' selected="selected"';}
                            ?>class=""><?php echo $v;?></option>
                            <?php endforeach; ?>
                    </select>
                    <?php if ($id == 'num_local') { ?>
                    <img id="wpm-waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
                    <?php } ?>
                    <small><?php echo $desc; ?></small>
                </div>
            </div>
    <?php }
     static function radio($atts)
    {
        extract($atts); ?>
            <div class="wpm_input wpm_text" id="wpm_o_<?php echo $id; ?>">
                
                <label for="<?php echo $id; ?>"><?php echo $title; ?></label>
                <div class="wpm_form_item wpm_form_radio_container">
                    <?php foreach($options as $k=>$v): ?>

                    <label><input class="<?php self::applyClasses($classes);?>" type="radio" name="<?php echo $id; ?>" value="<?php echo $k;?>" <?php
                    if($k == $value){echo' checked="checked"';}
                    ?> />
                    <?php echo $v; ?>
                    </label>
                   
                    <?php endforeach; ?>
					<br />
                    <small><?php echo $desc; ?></small>
                </div>
            </div>
    <?php }

    static function title($atts)
    {
        extract($atts); ?>
        <<?php echo $id;?>><?php echo $title;?></<?php echo $id;?>>
    <?php
    }

    static function sectionOpen($atts)
    {
        extract($atts); ?>
        <div class="wpm_section<?php self::applyClasses($classes);?>" <?php echo 'id="'.$id.'"';  ?>>
        <?php
    }

    static function applyClasses($classArray=array())
    {
        foreach($classArray as $class):
            echo ' ' . $class ;
        endforeach;
    }

    static function html($atts)
    {
        echo $atts['title'];
    }

    static function sectionClose($atts)
    {
        echo '</div>';
    }
}
