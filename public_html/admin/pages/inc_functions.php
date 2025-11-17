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
    return $sReturn ? "$sReturn<br>" : '';
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
            '{{msgerr.class_not_initialized2}}: [' . $sClassfile . ']',
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
 *                        - theme   {string} Theme to use. See public_html/vendor/codemirror/6.65.7/theme/
 * @return string The HTML code for the editor.
 */
function editorInit(array $aOptions = []): string
{
    require_once __DIR__.'/../../classes/cm-helper.class.php';
    if(!($codemirror??false)){
        $codemirror=new cmhelper();
    }
    
    static $iIdCounter;
    $iIdCounter++;
    $sIdBase="editor-$iIdCounter";

    $sContent=$aOptions['content'] ?? '';
    if(isset($aOptions['file'])){
        if (! $sContent=file_get_contents($aOptions['file'])){
            $sContent='ERROR: '.$aOptions['file'].' not loaded/ not found.';
        }
    }

    unset($aOptions['content']);
    unset($aOptions['file']);
    $aOptions['theme']??="neo";
    $codemirror->addEditor(
        'php', 
        $sIdBase, 
        $aOptions
    );

    $sReturn='<textarea id="'.$sIdBase.'" class="highlight-php" name="content">'.$sContent.'</textarea>'
        .$codemirror->getHtmlHead()
        .$codemirror->getJs()
        ;
    return $sReturn;

}

/**
 * Create a AdminLTE Card with CodeMirror editor with php syntax highlighting.
 * @param array $aOptions array with options. Keys are:
 *                        - content  {string} The initial content of the editor.
 *                        - file     {string} The initial content of the editor loaded from given file
 *                        - mime     {string} mode for syntax highlighting; default: "text/x-php"
 *                        - readonly {bool}   flag: readonly or changes can be saved?
 *                        - theme    {string} Theme to use. See public_html/vendor/codemirror/6.65.7/theme/
 *                        - url      {string} POST url of the form
 * @return string The HTML code for the card with editor
 */
function editorInCard(array $aOptions = []): string
{
    global $oCM;
    $bReadonly=$aOptions['readonly'] ?? false;
    $renderAdminLTE=new renderadminlte();
    return ''
        .$renderAdminLTE->getCard([
        'type' => '',
        'title' => ($aOptions['title'] ?? '')
            .($bReadonly ? ' ({{readonly}})' : ''),
        'text' => ''
        
            // init form
            .'
            <form id="frmEditFile" action="'.$aOptions['url'].'" method="POST">

            <input type="hidden" name="action" value="save">    
            <input type="hidden" name="file" value="' . $aOptions['file'] . '">

            '
            . editorInit($aOptions)

            // end form        
        ,
        'footer' => $bReadonly 
            ? '' 
            : $renderAdminLTE->getButton([
                'type' => 'primary',
                'text' => icon::get('save') . '{{save}}',
            ])
            .'</form>',
        // 'variant' => '',
        ]);
}

function saveFile($sFile, $sContent){
    $renderAdminLTE=new renderadminlte();
    $sBackBtn='<hr>'.$renderAdminLTE->getButton([
        'type' => 'secondary',
        'text' => icon::get('back') . '{{back}}',
        'onclick' => 'history.back();',
    ]);

    $sCurrent=file_get_contents($sFile);
    
    if($sCurrent==$sContent){
        addMsg('info', '{{msginfo.filecontent_is_the_same}}');
    } else {

        file_put_contents("{$sFile}_new.php", $sContent);
        exec("php -l '{$sFile}_new.php' ", $aOut, $rc);
        unlink("{$sFile}_new.php");
        if ($rc==0){
            if (file_put_contents("$sFile", $sContent)){
                addMsg('ok', '{{msgok.file_was_saved}}');
            } else {
                addMsg('error', '{{msgerr.file_was_not_saved}}'.$sBackBtn);
            }    
        } else {
            addMsg('error', 
                '{{msgerr.syntax_error}}<br>'
                .'<pre>'.implode("\n",$aOut).'</pre>'
                .$sBackBtn
            );
        }
    }

}