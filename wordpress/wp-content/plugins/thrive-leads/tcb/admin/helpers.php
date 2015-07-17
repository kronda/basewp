<?php
/**
 * helper functions (taken from Thrive Themes)
 */
if (!function_exists('_thrive_get_font_family_array')) {

    function _thrive_get_font_family_array($font_name = null)
    {
        if ($font_name === false) {
            return false;
        }
        $font_name = str_replace(" ", "", trim($font_name));
        $fonts = array('AbrilFatface' => "font-family: 'Abril Fatface', cursive;",
            'Amatic SC' => "font-family: 'Amatic SC', cursive;",
            'Archivo Black' => "font-family: 'Archivo Black', sans-serif;",
            'Arbutus Slab' => "font-family: 'Arbutus Slab', serif;",
            'Archivo Narrow' => "font-family: 'Archivo Narrow', sans-serif;",
            'Arial' => "font-family: 'Arial';",
            'Arimo' => "font-family: 'Arimo', sans-serif;",
            'Arvo' => "font-family: 'Arvo', serif;",
            'Boogaloo' => "font-family: 'Boogaloo', cursive;",
            'Calligraffitti' => "font-family: 'Calligraffitti', cursive;",
            'CantataOne' => "font-family: 'Cantata One', serif;",
            'Cardo' => "font-family: 'Cardo', serif;",
            'Cutive' => "font-family: 'Cutive', serif;",
            'DaysOne' => "font-family: 'Days One', sans-serif;",
            'Dosis' => "font-family: 'Dosis', sans-serif;",
            'Droid Sans' => "font-family: 'Droid Sans', sans-serif;",
            'Droid Serif' => "font-family: 'Droid Serif', sans-serif;",
            'FjallaOne' => "font-family: 'Fjalla One', sans-serif;",
            'FrancoisOne' => "font-family: 'Francois One', sans-serif;",
            'Georgia' => "font-family: 'Georgia';",
            'GravitasOne' => "font-family: 'Gravitas One', cursive;",
            'Helvetica' => "font-family: 'Helvetica';",
            'JustAnotherHand' => "font-family: 'Just Another Hand', cursive;",
            'Josefin Sans' => "font-family: 'Josefin Sans', sans-serif;",
            'Josefin Slab' => "font-family: 'Josefin Slab', serif;",
            'Lobster' => "font-family: 'Lobster', cursive;",
            'Lato' => "font-family: 'Lato', sans-serif;",
            'Montserrat' => "font-family: 'Montserrat', sans-serif;",
            'NotoSans' => "font-family: 'Noto Sans', sans-serif;",
            'OleoScript' => "font-family: 'Oleo Script', cursive;",
            'Old Standard TT' => "font-family: 'Old Standard TT', serif;",
            'Open Sans' => "font-family: 'Open Sans', sans-serif;",
            'Oswald' => "font-family: 'Oswald', sans-serif;",
            'OpenSansCondensed' => "font-family: 'Open Sans Condensed', sans-serif;",
            'Oxygen' => "font-family: 'Oxygen', sans-serif;",
            'Pacifico' => "font-family: 'Pacifico', cursive;",
            'Playfair Display' => "font-family: 'Playfair Display', serif;",
            'Poiret One' => "font-family: 'Poiret One', cursive;",
            'PT Sans' => "font-family: 'PT Sans', sans-serif;",
            'PT Serif' => "font-family: 'PT Serif', sans-serif;",
            'Raleway' => "font-family: 'Raleway', sans-serif;",
            'Roboto' => "font-family: 'Roboto', sans-serif;",
            'Roboto Condensed' => "font-family: 'Roboto Condensed', sans-serif;",
            'Roboto Slab' => "font-family: 'Roboto Slab', serif;",
            'ShadowsIntoLightTwo' => "font-family: 'Shadows Into Light Two', cursive;",
            'Source Sans Pro' => "font-family: 'Source Sans Pro', sans-serif;",
            'Sorts Mill Gaudy' => "font-family: 'Sorts Mill Gaudy', cursive;",
            'SpecialElite' => "font-family: 'Special Elite', cursive;",
            'Tahoma' => "font-family: 'Tahoma';",
            'TimesNewRoman' => "font-family: 'Times New Roman';",
            'Ubuntu' => "font-family: 'Ubuntu', sans-serif;",
            'Ultra' => "font-family: 'Ultra', serif;",
            'VarelaRound' => "font-family: 'Varela Round', sans-serif;",
            'Verdana' => "font-family: 'Verdana';",
            'Vollkorn' => "font-family: 'Vollkorn', serif;",);

        if ($font_name) {
            if (isset($fonts[$font_name])) {
                return $fonts[$font_name];
            } else {
                return false;
            }
        }
        return $fonts;
    }
}