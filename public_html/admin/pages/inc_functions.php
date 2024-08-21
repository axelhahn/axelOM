<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * this is an include file for different page scripts
 * 
 */


global $aTopMsg; $aTopMsg=[];

/**
 * add a message to the top of the page
 * @param string $sType the type of message
 * @param string $sMsg the message
 * @return void
 */
function addMsg($sType, $sMsg){
    global $aTopMsg;
    $aTopMsg[]=[$sType, $sMsg];
}

/**
 * render the messages to the top of the page
 * @return string
 */
function renderMsg(){
    global $renderAdminLTE;
    global $aTopMsg;
    $sReturn='';

    foreach($aTopMsg as $aMsg){
        $sType='danger'; 
        switch($aMsg[0]){
            case 'ok':    $sType='success'; break;
            case 'warn':  $sType='warning'; break;
            case 'error': $sType='danger'; break;
            case 'info':  $sType='info'; break;
        }

        $sReturn.=$renderAdminLTE->getCallout([
            'type' => $sType,
            // 'title' => 'No or an invalid object name was given',
            'dismissible' => 0,
            'text' => $aMsg[1],
        ]);
    }
    return $sReturn;
}

/**
 * show an inline error message
 * @param string $sTitle the title of the error message
 * @param string $sMessage the message to show
 * @return string
 */
function renderError($sTitle='ERROR (untitled)', $sMessage=''){
    global $renderAdminLTE;
    return $renderAdminLTE->getAlert([
        'type' => 'danger',
        'title' => $sTitle,
        'dismissible' => 0,
        'text' => $sMessage,
    ]);
}


/** 
 * guess the class name from an object name
 * @param string $sObjname the object name
 * @return string
 */
function getClassnameFromObjname($sObjname) {
    return 'axelhahn\\' . str_replace('-', '', $sObjname);
}



/**
 * initialize an object from givem object name.
 * I finds the class file and initializes it.
 * The function returns a string with error message or an object on success.
 * 
 * @param axelhahn\pdo_db   $oDB      database conection pobject
 * @param string            $sObject  name of object, like 'user' or 'product'
 * @return mixed object|string
 */
function initClass($oDB, $sObject){
    global $sAppRootDir, $sTabApp;
    if (!$sObject) {
        return renderError(
            '{{msgerr.no_or_invalid_object_name}}',
            '{{msgerr.no_or_invalid_object_name2}}',
        );
    }
    
    $sMyFile=str_replace('_', '-', $sObject).'.class.php';
    $sClassfile='';
    foreach([
        $sAppRootDir.'/'.$sTabApp.'/classes',
        __DIR__ . '/../../classes',
        __DIR__ . '/../../vendor/php-abstract-dbo/src',
    ] as $mydir){
        if (file_exists($mydir.'/'.$sMyFile)) {
            $sClassfile=$mydir.'/'.$sMyFile;
            break;
        }
    }
    if (!$sClassfile) {
        return renderError(
            '{{msgerr.class_file_missing}}',
            '{{msgerr.class_file_missing2}}: [' . $sObject.'.class.php' .']',
        );
    }

    // ---------- let's init


    require_once __DIR__ . '/../../vendor/php-abstract-dbo/src/pdo-db.class.php';
    require_once $sClassfile;

    $sClassname = getClassnameFromObjname($sObject);

    if (!$o = new $sClassname($oDB)) {
        return renderError(
            '{{msgerr.class_not_initialized}} [' . $sClassname . ']',
            '{{msgerr.class_not_initialize2d}}: [' . $sClassfile . ']',
        );
    }
    return $o;
}

/**
 * Create a CodeMirror editor with php syntax highlighting.
 * This function is idempotent. It will only include the necessary
 * CSS and JS files once, no matter how often it is called.
 * The editor ID is automatically generated and will be unique
 * for each call to this function.
 * 
 * @param array $aOptions array with options. Keys are:
 *                        - content {string} The initial content of the editor.
 *                        - file    {string} The initial content of the editor loaded from given file
 *                        - mime    {string} mode for syntax highlighting; default: "text/x-php"
 *                        - theme   {string} Theme to use. See public_html/vendor/codemirror/6.65.7/theme/
 * @return string The HTML code for the editor.
 */
function editorInit(array $aOptions = []): string
{
    static $iIdCounter;
    if(!isset($iIdCounter)){
        $iIdCounter=0;
    }
    $sCmBase="../vendor/codemirror/6.65.7";

    $iIdCounter++;
    $sIdBase="editor-$iIdCounter";

    // ========== handle options
    //
    // --- theme: 
    // $sTheme="material-palenight";
    // $sTheme="nord";
    $sTheme=$aOptions['theme'] ?? "neo";

    $sMode=$aOptions['mode'] ?? "php";
    $sMime=$aOptions['mime'] ?? "text/x-php";

    $sContent=$aOptions['content'] ?? '';
    if(isset($aOptions['file'])){
        if (! $sContent=file_get_contents($aOptions['file'])){
            $sContent='ERROR: '.$aOptions['file'].' not loaded/ not found.';
        }
    }

    $sReturn="";

    if($iIdCounter==1){
        $sReturn.='
        <link rel="stylesheet" href="'.$sCmBase.'/codemirror.css">
        <link rel="stylesheet" href="'.$sCmBase.'/theme/'.$sTheme.'.min.css">

        <script src="'.$sCmBase.'/codemirror.js"></script>

        <script src="'.$sCmBase.'/mode/clike/clike.min.js"></script>
        ';
    }

    $sReturn.='
    <script src="'.$sCmBase.'/mode/'.$sMode.'/'.$sMode.'.min.js"></script>
    <textarea id="'.$sIdBase.'">'.$sContent.'</textarea>

    <script>
      window.onload = function() {
        var myTextarea = document.getElementById("'.$sIdBase.'");
        editor = CodeMirror.fromTextArea(myTextarea, {
          theme: "'.$sTheme.'",
          lineNumbers: true,
          matchBrackets: true,
          mode: "'.$sMime.'"
        });
      };
    </script>
    ';
    return $sReturn;

}