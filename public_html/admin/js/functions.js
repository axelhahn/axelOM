/*
                       __    __    _______  _______ 
.---.-..--.--..-----.|  |  |__|  |       ||   |   |
|  _  ||_   _||  -__||  |   __   |   -   ||       |
|___._||__.__||_____||__|  |__|  |_______||__|_|__|
                      \\\_____ axels OBJECT MANAGER

----------------------------------------------------------------------

javascript functions for all pages

----------------------------------------------------------------------
*/

AOM_LEFT_SIDEBAR="";

/**
 * get query parameters from url as object
 * 
 * @example:
 *   var _GET=getQueryParams();
 *   console.log(_GET['app']);
 * 
 * @returns {object}
 */
function getQueryParams() {
    var qs = document.location.search.split('+').join(' ');
    var params = {},
            tokens,
            re = /[?&]?([^=]+)=([^&]*)/g;
    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }
    return params;
}

/**
 * Show given content in the overlay window.
 * @param {string} sHtmlcode   output to show in the overlay; empty text hides the overlay
 * @return bool
 */
function overlayDisplay(sHtmlcode){
  $('#overlay-text').html(sHtmlcode);
  if(sHtmlcode){
    overlayShow();
  } else {
    overlayHide();
  }
  return true;
}

/**
 * bring overlay window to front
 */
function overlayShow(){
  $('#overlay').show();
}

/**
 * hide overlay window
 */
function overlayHide(){
  $('#overlay').hide();
}

function dosearch(){
  var _GET=getQueryParams();
  var q=$("#searchtop").val();
  localStorage.setItem(_GET['app'] + "-search", q);

  if(q){
    httprequest("GET", "?app="+_GET['app']+"&page=search&q="+q, {}, "overlay-text");
    overlayShow();
  } else {
    overlayHide();
  }
}

/**
 * Make an http request with given method, url, form data.
 * The response can be shown in a given dom id or in the full browser window.
 * 
 * TODO: beautify
 * - Example POST method implementation:
 *   https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
 * - Parse Javascript fetch in PHP
 *   https://stackoverflow.com/questions/35091757/parse-javascript-fetch-in-php
 * 
 * @param {string}  method  http method; eg. GET, POST, PUT, ...
 * @param {string}  url     url to request
 * @param {json}    data    request body as key -> value in a JSON
 * @param {string}  idOut   optional: id of output element in DOM; default: write response in browser
 * @return void
 */
async function httprequest(method="GET", url = "", data = {}, idOut = null) {

    // console.log("httprequest("+method+", "+url+", "+data+", "+idOut+")");

    if (method == "POST" || method == "PUT") {
        var fd = new FormData();
        for (var key in data) {
            fd.append(key, data[key]);
        }
    } else {
        var fd = null;
    }

    // Default options are marked with *
    const response = await fetch(url, {
        method: method, // *GET, POST, PUT, DELETE, etc.
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        credentials: "same-origin", // include, *same-origin, omit
        headers: {
            // "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
            // 'Content-Type': 'multipart/form-data',
        },
        redirect: "follow", // manual, *follow, error
        referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: fd, // body data type must match "Content-Type" header
    });
    // return response.json(); // parses JSON response into native JavaScript objects

    var responsebody = await response.text();
    if (idOut) {
        document.getElementById(idOut).innerHTML = responsebody;
    } else {
        document.open();
        document.write(responsebody);
        document.close();
    }

}


// // save left menu status
// function saveLeftMenu(){
//   localStorage.setItem("bodyClass", document.body.className);
// }

// // it is flickering on page reload
// function restoreLeftMenu(){
//   var s=localStorage.getItem("bodyClass", document.body.className);
//   if (s) {
//     document.body.className = s;
//   }
// }


// ----------------------------------------------------------------------
// MAIN
// ----------------------------------------------------------------------

$(document).ready(function () {

    // var oQuery = getQueryParams();
    $('.dataTable').DataTable({
      "lengthMenu": [[25, 100, -1], [25, 100, "..."]],
      stateSave: true,
    });

    $('.summernote').summernote({
      minHeight: 100,             // set minimum height of editor
      maxHeight: 500,             // set maximum height of editor
      toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        // ['insert', ['link', 'picture', 'video']],
        ['insert', ['link']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ]
    });

    // hide overlay when clicking on the background outside the overlay window
    $('#overlay').click(function () {
      $(this).hide();
    });

    // do not close on click inside modal window
    $( '#overlay-text' ).on( 'click', function( event ) {
      event.stopPropagation();
    });

    var _GET=getQueryParams();
    $('#searchtop').val(localStorage.getItem("search") ? localStorage.getItem(_GET['app'] + "-search") : "");

    // search field on top right
    $('#searchtop').click(function () {
      dosearch();
    });
    $('#searchtop').keyup(function () {
      dosearch();
    });
    $('#searchtop').keypress(function () {
      dosearch();
    });

    // // save left menu status
    // $('.nav-link[data-widget="pushmenu"]').click(function () {
    //   window.setTimeout("saveLeftMenu()", 1000);
    // });

    // restoreLeftMenu();


  });
