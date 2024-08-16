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

function editorInit(string $sFile=''){
    $sRelPath='../vendor/textarea-code-editor';
    static $iIdCounter;
    if(!isset($iIdCounter)){
        $iIdCounter=0;
    }

    $iIdCounter++;
    $sIdBase="editor-$iIdCounter";


    $sReturn="
        <div class=\"editor-holder\">
		<textarea id=\"$sIdBase\" rows=\"1\" spellcheck=\"false\" class=\"editor allow-tabs\"><?php
echo \"hello\";</textarea>
		<pre><code id=\"$sIdBase-out\" class=\"syntax-highight html\"></code></pre>
        </div>
    ";

    if($iIdCounter==1){
        $sReturn="
            <!-- Demo CSS (No need to include it into your project) -->
            <link rel=\"stylesheet\" href=\"$sRelPath/css/style_ah.css\">
            <!--
            <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/foundation/6.8.1/css/foundation.min.css\" integrity=\"sha512-QuI0HElOtzmj6o/bXQ52phfzjVFRAhtxy2jTqsp85mdl1yvbSQW0Hf7TVCfvzFjDgTrZostqgM5+Wmb/NmqOLQ==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />
            -->

            $sReturn

            <!--
            <script src=\"https://raw.githubusercontent.com/emmetio/textarea/master/emmet.min.js\"></script>
            <script src=\"https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.10.0/highlight.min.js\" integrity=\"sha512-6yoqbrcLAHDWAdQmiRlHG4+m0g/CT/V9AGyxabG8j7Jk8j3r3K6due7oqpiRMZqcYe9WM2gPcaNNxnl2ux+3tA==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>
            -->
            <script src=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/highlight.min.js\"></script>
            <!-- Script JS -->
            <script src=\"$sRelPath/js/script_ah.js\"></script>

        ";
    }

    return $sReturn;

}