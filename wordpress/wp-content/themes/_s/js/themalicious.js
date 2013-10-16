jQuery( document ).ready( function( $ ) {
    /*-------------------------------------------------------------------- 
   * JQuery Plugin: "EqualHeights" & "EqualWidths"
   * by:  Scott Jehl, Todd Parker, Maggie Costello Wachs (http://www.filamentgroup.com)
   *
   * Copyright (c) 2007 Filament Group
   * Licensed under GPL (http://www.opensource.org/licenses/gpl-license.php)
   *
   * Description: Compares the heights or widths of the top-level children of a provided element 
      and sets their min-height to the tallest height (or width to widest width). Sets in em units 
      by default if pxToEm() method is available.
   * Dependencies: jQuery library, pxToEm method  (article: http://www.filamentgroup.com/lab/retaining_scalable_interfaces_with_pixel_to_em_conversion/)                
   * Usage Example: $(element).equalHeights();
                      Optional: to set min-height in px, pass a true argument: $(element).equalHeights(true);
   * Version: 2.0, 07.24.2008
   * Changelog:
   *  08.02.2007 initial Version 1.0
   *  07.24.2008 v 2.0 - added support for widths
  --------------------------------------------------------------------*/

  $.fn.equalHeights = function(px) {
    $(this).each(function(){
      var currentTallest = 0;
      $(this).children().each(function(i){
        if ($(this).height() > currentTallest) { currentTallest = $(this).height(); }
      });
      if (!px && Number.prototype.pxToEm) currentTallest = currentTallest.pxToEm(); //use ems unless px is specified
      // for ie6, set height since min-height isn't supported
      // $.browser is deprecated
      // if ($.browser.msie && $.browser.version == 6.0) { $(this).children().css({'height': currentTallest}); }
      // $(this).children().css({'min-height': currentTallest}); 
    });
    return this;
  };

  $('#home-featured').equalHeights(true);


  // The search submit is covered by a Font Awesome icon so make sure clicking the icon trigger search submit
  $('.icon-search').click(function() {
    $('.search-submit').click(); 
  });

  //Change the active sidebar link when on a single custom post type of 'Story'  
  
  $('.single-cs_stories #menu-item-134').removeClass('current_page_parent');
  $('.single-cs_stories #menu-item-136').addClass('current_page_parent');

  // Add class to target Firefox on Windows only
  windows_firefox();
});

function windows_firefox () {
  if ( navigator.appVersion.indexOf("Win") != -1 ) {
    var OS = 'Windows';
  }
  if ( navigator.MozConnection) {
    var winBrowser = "Firefox";
    console.log(winBrowser);
  };
  if ( OS == "Windows" && winBrowser == "Firefox") {
    jQuery('html').addClass('windows-firefox');
  };
}

