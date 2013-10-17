// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) {
      arguments.callee = arguments.callee.caller;
      console.log( Array.prototype.slice.call(arguments) );
  }
};
// make it safe to use console.log always
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

jQuery(document).ready(function($){
    // Uploader
    var uploadID = ''; /*setup the var*/

    jQuery('.upload-button').click(function() {
        uploadID = jQuery(this).prev('input'); /*grab the specific input*/
        formfield = jQuery('.upload').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

 window.send_to_editor = function(h) {
        if(uploadID != ''){
        imgurl = jQuery('img',h).attr('src');
        uploadID.val(imgurl); /*assign the value to the input*/
        tb_remove();
        uploadID = '';
        }else{
          var ed, mce = typeof(tinymce) != 'undefined', qt = typeof(QTags) != 'undefined';

          if ( !wpActiveEditor ) {
            if ( mce && tinymce.activeEditor ) {
              ed = tinymce.activeEditor;
              wpActiveEditor = ed.id;
            } else if ( !qt ) {
              return false;
            }
          } else if ( mce ) {
            if ( tinymce.activeEditor && (tinymce.activeEditor.id == 'mce_fullscreen' || tinymce.activeEditor.id == 'wp_mce_fullscreen') )
              ed = tinymce.activeEditor;
            else
              ed = tinymce.get(wpActiveEditor);
          }

          if ( ed && !ed.isHidden() ) {
            // restore caret position on IE
            if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
              ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

            if ( h.indexOf('[caption') === 0 ) {
              if ( ed.plugins.wpeditimage )
                h = ed.plugins.wpeditimage._do_shcode(h);
            } else if ( h.indexOf('[gallery') === 0 ) {
              if ( ed.plugins.wpgallery )
                h = ed.plugins.wpgallery._do_gallery(h);
            } else if ( h.indexOf('[embed') === 0 ) {
              if ( ed.plugins.wordpress )
                h = ed.plugins.wordpress._setEmbed(h);
            }

            ed.execCommand('mceInsertContent', false, h);
          } else if ( qt ) {
            QTags.insertContent(h);
          } else {
            document.getElementById(wpActiveEditor).value += h;
          }

          try{tb_remove();}catch(e){};
        }
    };
    
    // Color Picker
    $('.pickcolor').click( function(e) {
		colorPicker = jQuery(this).next('div');
		input = jQuery(this).prev('input');
		$(colorPicker).farbtastic(input);
		colorPicker.show();
		e.preventDefault();
		$(document).mousedown( function() {
    		$(colorPicker).hide();
    	});
	});
});

