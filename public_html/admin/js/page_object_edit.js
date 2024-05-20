/*
                       __    __    _______  _______ 
.---.-..--.--..-----.|  |  |__|  |       ||   |   |
|  _  ||_   _||  -__||  |   __   |   -   ||       |
|___._||__.__||_____||__|  |__|  |_______||__|_|__|
                      \\\_____ axels OBJECT MANAGER

----------------------------------------------------------------------

javascript functions for object editor

----------------------------------------------------------------------
*/


// ----------------------------------------------------------------------

// track if a form was changed:
// https://stackoverflow.com/questions/959670/generic-way-to-detect-if-html-form-is-edited

$.fn.extend({
    trackChanges: function() {
      $(":input",this).change(function() {
         $(this.form).data("changed", true);
         disableRelations();
      });
      $(":input",this).keypress(function() {
        $(this.form).data("changed", true);
        disableRelations();
     });

    }
    ,
    isChanged: function() { 
      return this.data("changed"); 
    }
   });



/**
 * Disable relation handling if the object was changed.
 */
function disableRelations(){
  var _opacity=0.4;
  $('#relations').css('opacity', _opacity);
  $('#relations button').attr('disabled', 'disabled');

  $('#frmAttach').css('opacity', _opacity);
  $('#frmAttach button').attr('disabled', 'disabled');
  $('#frmAttach .dropzone').hide();
  
  $('#msgOtherChanges').css('display', 'block');
  return true;
}
   
// ----------------------------------------------------------------------
// MAIN

// track editor form for an object
window.setTimeout('$("#frmEditObject").trackChanges();', 100);

// track changes in summernote editors
$('.summernote').on('summernote.change', function(we, contents, $editable) {
  disableRelations();
});


// ----------------------------------------------------------------------
