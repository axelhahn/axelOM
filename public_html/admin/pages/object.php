<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * OBJECTS :: list items and edit single items
 * 
 */

if(!$acl->canView($sTabApp)){
    include('error403.php');
    return false;
}

require_once('inc_functions.php');

$sHomeAppUrl = '?app='.$sTabApp.'&page=home';
$sBaseAppUrl = '?app='.$sTabApp.'&page=object';
$sBaseUrl    = '?app='.$sTabApp.'&page=object&object=' . $sObject;

$aObjdata=$appmeta->getObject($sObject);
$sObjLabel= ''
    . (isset($aObjdata['icon']) ? icon::get($aObjdata['icon']) : '')
    . ($aObjdata['label'] ?? '');

if($sObject=="pdo_db_attachments"){
    $sObjLabel=icon::get('file') . ' {{files}}';
}

$TITLE='<strong>'
    .'<a href="'.$sHomeAppUrl.'">'
    .icon::get($appmeta->getAppicon()) . $appmeta->getAppname()
    .'</a></strong> ' .DELIM_TITLE
    . ''.($sObjLabel ? '  ' . $sObjLabel : '') .'';

$BANNER=$appmeta->getApphint() . ' -> <strong>' . $appmeta->getObjectHint($sObject).'</strong>';

// addMsg('ok', 'OK: I am a test');

/**
 * get html code for a data list that must exist in app configuration
 * in apps/<appname>/config/objects.php
 * 
 * @param  string  $sLabel  data list key
 * @return string
 */
function getDatalist(string $sLabel): string
{
    global $appmeta;
    $sReturn='';
    $aLists=$appmeta->getConfig('datalists');
    if(isset($aLists[$sLabel]) && is_array($aLists[$sLabel])){
        $sReturn.='<datalist id="'.$sLabel.'">';
        foreach($aLists[$sLabel] as $aItem){
            $sReturn.='<option value="'.$aItem[0].'" label="'.$aItem[1].'"></option>';
        }
        $sReturn.='</datalist>';
        $sReturn='<div class="row mb-3">'
            .'<label class="col-sm-2 col-form-label"></label>'
            . '<div class="col-sm-10">'.$sReturn.'</div>'
        .'</div>'
        ;
    }
    return $sReturn;
}

// ---------- let's init

include('inc_set_db.php'); // set $oDB

$o=initClass( $oDB, $sObject );
if(is_string($o)) {
    // strng = error occured
    $BODY = $o;
    return true;
}

// ---------- handle POST

$aAttributes = $o->getAttributes(true);

$sMainContent = '';

// $sMainContent.='<pre>_POST = '.print_r($_POST, 1).'</pre>';

if (isset($_POST['action'])) {
    $aItemData = [];
    if($_POST['action']=='new' || $_POST['action']=='edit') {
        foreach (array_keys($aAttributes) as $sAttr) {
            $aItemData[$sAttr] = $_POST[$sAttr];
        }
    
    }

    if ($acl->canEdit($sTabApp)){

        // --- special action for attachments
        if($sObject=="pdo_db_attachments"){
            $sMyBaseDir=$appmeta->getConfig('attachments')['basedir']??false;
            if(!$sMyBaseDir || !is_dir($sMyBaseDir)){
                addMsg('error', '{{object.attachment-fix-app-config-in-attachments-key}}');
            } else {
                $o->setUploadDir($sMyBaseDir);
            }
        }
        // --- 

        switch ($_POST['action']) {
            case 'new':
                if ($o->setItem($aItemData)) {
                    if (!$o->save()) {
                        addMsg('error', '{{msgerr.item_not_created}}');
                    } else {
                        $sBtnEdit=' ... '.$renderAdminLTE->getButton([
                            'type' => 'dark',
                            'text' => icon::get('edit') . '{{edit}}',
                            'title' => '{{edit}}: '.$o->getLabel($aItemData),
                            'onclick' => 'location.href=\'' . $sBaseUrl.'&id='.$o->id() . '\'',
                        ]);
                        addMsg('ok', '{{msgok.item_was_created}}: ' . $o->id() . ': ' . $o->getLabel() . ' '.$sBtnEdit);
                    }
                } else {
                    addMsg('error', '{{msgerr.wrong_itemdata}}');
                }
                break;;

            case 'edit':
                $aItemData['id'] = queryparam::post('id', false, 'int');
                $o->read($aItemData['id']);
                $sBtnEdit=!($_GET['id']??false)
                    ? ' ... '.$renderAdminLTE->getButton([
                        'type' => 'dark',
                        'text' => icon::get('edit') . '{{edit}}',
                        'title' => '{{edit}}: '.$o->getLabel($aItemData),
                        'onclick' => 'location.href=\'' . $sBaseUrl.'&id='.$aItemData['id'] . '\'',
                    ])
                    : ''
                    ;
                if ($o->setItem($aItemData)) {
                    if($o->hasChange()){
                        if (!$o->save()) {
                            addMsg('error', '{{msgerr.item_not_updated}}: ' . $o->getLabel() . ' '.$sBtnEdit);
                        } else {
                            addMsg('ok', '{{msgok.item_was_updated}}: ' . $o->getLabel() . ' '.$sBtnEdit);
                        }
                    } else {
                        addMsg('ok', '{{msgok.item_unchanged}}: ' . $o->getLabel() . ' '.$sBtnEdit);
                    }
                } else {
                    addMsg('error', '{{msgerr.wrong_itemdata}}');
                }
                break;;

            case 'delete':
                $aItemData['id'] = queryparam::post('id', false, 'int');
                if ($o->delete($aItemData['id'])) {
                    addMsg('ok', '{{msgok.item_was_deleted}}: ' . $aItemData['id'] . ' ');
                } else {
                    addMsg('error', '{{msgerr.item_not_deleted}}: ' . $aItemData['id'] . ' ');
                }
                break;;

            case 'relCreate':
                $o->read(queryparam::get('id', false, 'int'));
                $sTargetObj=queryparam::post('relobject');

                // list of multiple targets
                $aTargetIdList=queryparam::post('relidlist', false, 'array');

                if($sTargetObj && $aTargetIdList){
                    foreach($aTargetIdList  as $sTargetId){
                        if ($o->relCreate($sTargetObj, $sTargetId)){
                            addMsg('ok', '{{msgok.relation_was_created}} ' . $sTargetObj . ':'.$sTargetId. '');
                        } else {
                            addMsg('error', '{{msgerr.relation_was_not_created}} [' . $sTargetObj . ':'.$sTargetId. ']');
                        }
                    }
                } else {
                    addMsg('error', '{{msgerr.wrong_relationdata}} [' . $sTargetObj . ':'.$sTargetId. ']');
                }
                break;;

            case 'relDelete':
                // $sMainContent .= 'POST = ' . htmlentities(print_r($_POST, 1)).'</pre>';
                if($o->read(queryparam::get('id', false, 'int'), true)){
                    $sTargetObj=queryparam::post('relobject');
                    $sRelKey=queryparam::post('relkey');
                    if(!$o->relDelete($sRelKey)){
                        addMsg('error', '{{msgerr.relation_was_not_deleted}} [' . $sRelKey .'] '.print_r($oDB->lastquery(), 1).':-(');
                    } else {
                        addMsg('ok', '{{msgok.relation_was_deleted}} [' . $sRelKey .'].');
                    }
                }
                

                // $oRelobj=initClass( $oDB, $aRelation['table'] );
                // if(is_string($oRelobj)){
                //     $BODY='Error: '.$oRelobj;
                //     return true;
                // }
                break;;
            case 'attach':
                $out='';
                // TODO: abstract the file path in config
                $sUploadpath=dirname(dirname(__DIR__)).'/apps/'.$sTabApp.'/files/';
                if(!is_dir($sUploadpath)){
                    header('http/1.0 401 Access denied');
                    die('{{msgerr.fileupload_no_dir}} ['.$sUploadpath.']');
                }
                $aItemData['id'] = queryparam::post('id', false, 'int');
                if(!$o->read($aItemData['id'])){
                    header('503 Service Unavailable', true, 503);
                    die('{{msgerr.fileupload_wrong_id}} ' . $aItemData['id'] .' ');
                }

                // print_r($_FILES['file'] );
                require_once __DIR__ . '/../../vendor/php-abstract-dbo/src/pdo-db-attachments.class.php';
                $oFile=new axelhahn\pdo_db_attachments($oDB);

                // $sMyBaseUrl=$appmeta->getConfig('attachments')['baseurl']??false;
                $sMyBaseDir=$appmeta->getConfig('attachments')['basedir']??false;

                if(!$sMyBaseDir || !is_dir($sMyBaseDir)){
                    addMsg('error', '{{object.attachment-fix-app-config-in-attachments-key}}');
                } else {
                    $oFile->setUploadDir($sMyBaseDir);
    
                    foreach($_FILES as $aFile){
                        if ($sTargetId = $oFile->uploadFile($aFile)){
                            $sTargetObj='pdo_db_attachments';
                            $out.= "{{msgok.fileupload_object_created}} #".$sTargetId. PHP_EOL;
                            if ($o->relCreate($sTargetObj, $sTargetId)){
                                $out.= '{{msgok.relation_was_created}} ' . $sTargetObj . ':'.$sTargetId. ':-)'. PHP_EOL;
                            } else {
                                header('503 Service Unavailable', true, 503);
                                $out.= '{{msgerr.relation_was_not_created}} [' . $sTargetObj . ':'.$sTargetId. ']: '. $oDB->error() . PHP_EOL;
                                die($out);
                            }
                        } else {
                            header('503 Service Unavailable', true, 503);
                            $out.= '{{msgerr.relation_was_not_created}} upload failed: '. $oDB->error() .print_r($aFile, 1). PHP_EOL;
                            die($out);
                        }
                    }
                }

                die("{{msgok.fileupload_and_relation}}");
                // break;;

            default:
                header('http/1.0 400 Bad Request');
                addMsg('error', 'OOPS: POST action [' . htmlentities($_POST['action']) . '] is not handled (yet). :-/');
        }
    } else {
        addMsg('error', '{{msgerr.missing_permissions}}');
    }
}

// die(__FILE__.':'.__LINE__);
$o=initClass( $oDB, $sObject );

// ---------- work with data

// GET
// page=object&object=objprogramminglanguages&id=2&newrel=objproducts
//      ^             ^                          ^        ^
//      |             |                          |        |
//      this page     object type                item id   add new relation
//                                               or "new"

// flags
// $bShowOverview=false;
$bShowEdit = false;
// $bShowRelations = false;
// $bShowEditRelations = false;


$sContextbar='';


if (queryparam::get('new')) {
    $bShowEdit = true;
}
$iId = queryparam::get('id', false, 'int');
if ($iId) {
    $bShowEdit = true;
    if(!$o->read($iId, true)){
        include('error404-wrong-objid.php');
        return true;
    }

    $TITLE='<strong>'
    .'<a href="'.$sHomeAppUrl.'">'
        .icon::get($appmeta->getAppicon()) . $appmeta->getAppname()
    .'</a></strong>'
    .DELIM_TITLE
    . '<a href="'.$sBaseUrl.'">'.($sObjLabel ? '  ' . $sObjLabel : '') .'</a>'
    .DELIM_TITLE
    .icon::get('item').$o->getLabel()
    ;

    // $TITLE = '<a href="'.$sBaseUrl.'">'.$TITLE.'</a> :: '.$o->getLabel();

}
$sRelobjectname=queryparam::get('newrel', '/[a-z\-]/');


$iItems = $o->count();
if ($iItems == 0) {
    $bShowEdit = true;
    $sMainContent .= '<br>
    <h3>'.icon::get('new').'{{object.welcome}}</h3>
    <p>{{object.lets_create_first_item}}</p>
    <br>';
} else {

    if (!$bShowEdit) {

        // ---------- overview page with all entries (rows)

        $aBasicAttributes = $o->getBasicAttributes();
        $aItems = $o->search(['columns'=>$aBasicAttributes]);
        // print_r($aBasicAttributes);echo "<hr>"; print_r($aItems); die();

        $aCheck=$o->verifyColumns();
        $bDbTableOk=isset($aCheck['_result']['errors']) && $aCheck['_result']['errors']==0;

        
        $sTable = "\n";
        $sTable .= '<table class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
            <tr>';
        foreach($aBasicAttributes as $sField){
            $sLabel=$o->getFormtype($sField)['label']??$sField;
            $sTable .= $sField=='id' ? '' : '<th>'.$sLabel.'</th>';
        }
        $sTable .= "<th class=\"actions\">{{td-actions}}</th></tr>
            </thead>
            <tbody>\n";

        foreach ($aItems as $aRow) {

            $sTable .= '<tr>';
            $iCol=0;
            foreach($aBasicAttributes as $sField){
                if($sField=='id'){
                    continue;
                }
                $iCol++;
                $sText=$aRow[$sField];
                if($sField=='label' || $iCol==1){
                    $sText=$acl->canEdit($sTabApp)
                        ? '<a href="'.$sBaseUrl . '&id=' . $aRow['id'] . '"><strong>'.$aRow[$sField].'</strong></a>'
                        : '<strong>'.$aRow[$sField].'</strong>'
                        ;
                }
                $sTable .= '<td>'.$sText.'</td>';
            }
            $sTable .= '<td align="right"><nobr>'
                . ''

                /*

                   TODO: button to view item (maybe grphic with linked objects)

                .$renderAdminLTE->getButton([
                    'type' => 'dark',
                    'text' => icon::get('view') . '{{view}}',
                    'title' => '{{view}}: '.$o->getLabel($aRow),
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'view\', \'id\': \''.$aRow['id'].'\'});',
                ])
                . ' '
                 */
                . ($bDbTableOk && ($acl->canEdit($sTabApp))
                    ? $renderAdminLTE->getButton([
                        'class' => 'btn-outline-dark',
                        'text' => icon::get('edit') . '{{edit}}',
                        'title' => '{{edit}}: '.$o->getLabel($aRow),
                        'onclick' => 'location.href=\'' . $sBaseUrl . '&id=' . $aRow['id'] . '\'',
                    ])
                    : '')
                .' '
                . ($acl->canEdit($sTabApp)
                    ? $renderAdminLTE->getButton([
                        'class' => 'btn-outline-danger',
                        'text' => icon::get('delete') . '{{delete}}',
                        'title' => '{{delete}}: '.$o->getLabel($aRow),
                        'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$o->getLabel($aRow).'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'delete\', \'id\': \''.$aRow['id'].'\'});',
                    ])
                    : '')

                . '</nobr></td>' . "\n";

            $sTable .= '</tr>' . "\n";
        }
        $sTable .= '</tbody></table>' . "\n\n";

        $aObjdata['icon']??='object'; 
        $sMainContent .= $renderAdminLTE->getCard([
            'type' => 'info',
            'variant' => 'outline',
            // 'tb-remove' => 1,
            // 'tb-collapse' => 1,
            'title' => icon::get($aObjdata['icon']). '{{items}} :: <strong>' . $sObject . '</strong>',
            'tools' => '',
            'text' => ''
                . ($bDbTableOk && ($acl->canEdit($sTabApp))
                ? $renderAdminLTE->getButton([
                    'type' => 'success',
                    'text' => icon::get('new').'{{new}}',
                    'onclick' => 'location.href=\'' . $sBaseUrl . '&new=1\'',
                ]) . '<br><br>'
                : '')
                . $sTable,
            // 'footer' => '&copy; Axel',
        ]);
        
        $sObjEditUrl=str_replace('&page=object&', '&page=objectedit&', $sBaseUrl);
        if(!$bDbTableOk){
            addMsg(
                'error', 
                '{{object.tablecheck_update_required}}<br>'
                    // .'<ul><li>'.implode('</li><li>', $aCheck['_result']['messages']).'</li></ul>'
                    // .'<hr>'
                    .'{{object.tablecheck_edit_disabled}}<br>'
                    .$renderAdminLTE->getButton([
                        'type' => 'dark',
                        'text' => icon::get('config').'{{config}}',
                        'onclick' => 'location.href=\'' . $sObjEditUrl . '\';',
                    ]) . ' '
                    .$renderAdminLTE->getButton([
                        'type' => '',
                        'text' => icon::get('reload').'{{reload}}',
                        'onclick' => 'document.location.reload();',
                    ]),
            );
        }

        /*
        $sProperties='';
        foreach($aAttributes as $sKey=>$aProperty){
            $sProperties.='<strong>'.$sKey.'</Strong>: <em>'.$aProperty['create'].'</em><br>';
        }
        */

        $sContextbar .= $renderAdminLTE->getCallout([
            'type' => '',
            'title' => icon::get('items').'{{items}}',
            'text' => '<h3>' . $iItems . '</h3>',
        ])
        /*
        .$renderAdminLTE->getCallout(array(
            'type' => 'primary',
            'title' => icon::get('properties').'{{properties}}',
            'text' => $sProperties.'<hr>'
                .$renderAdminLTE->getButton([
                    'type' => '',
                    'text' => icon::get('code').'{{raw}}',
                    'onclick' => '$(\'#objattributes\').toggleClass(\'hidden\');',
                ]),
        ))
        */
        .($bDbTableOk
            ? $renderAdminLTE->getCallout([
                'type' => '',
                'title' => icon::get('database').'{{object.tablecheck}}',
                'text' => '<h3>{{object.tablecheck_ok}}</h3>'
                    . ($acl->isAppAdmin($sTabApp)
                        ? '<hr>'
                            .$renderAdminLTE->getButton([
                                'class' => 'btn-outline-dark',
                                'text' => icon::get('config').'{{config}}',
                                'onclick' => 'location.href=\'' . $sObjEditUrl . '\';',
                            ])
                        : ''
                    ),
            ])
            :$renderAdminLTE->getAlert([
                'type' => 'danger',
                'title' => '{{error}}',
                'text' => icon::get('database').'{{object.tablecheck}}'
                . ($acl->isAppAdmin($sTabApp)
                    ? '<hr>'
                        // .'{{object.tablecheck_update_required}}'
                        .$renderAdminLTE->getButton([
                            'type' => 'dark',
                            'text' => icon::get('config').'{{config}}',
                            'onclick' => 'location.href=\'' . $sObjEditUrl . '\';',
                        ])
                    : ''
                ),
            ])
        )
        ;
        // $sMainContent.=$sTable;
    }
}


if ($bShowEdit) {

    // ---------- show editor: first item / new item / edit id

    if(!$acl->canEdit($sTabApp)){
        addMsg('error', '{{msgerr.missing_permissions}}');
    } else {

        $aItem = $o->getItem();
        $aBasicAttributes = $o->getBasicAttributes();
        // echo ''; print_r($aItem);die();

        // DEBUG
        // $sMainContent.='<pre>'.print_r($aItem, 1).'</pre>';
        // $sMainContent.='<pre>'.print_r($o->getAttributes(), 1).'</pre>';

        $JS_BODYEND='<script type="text/javascript" src="js/page_object_edit.js"></script>';
        $sFooter = '';
        $sForm = ''
            . '<form id="frmEditObject" class="form-horizontal" action="' . $sBaseUrl . '" method="POST">'
            . '<input type="hidden" name="action" value="' . ($iId ? 'edit' : 'new') . '">'
            . ($iId ? '<input type="hidden" name="id" value="'.$iId.'">' : '')
            ;
        foreach ($aItem as $sVarname => $value) {
            $editvalueid = 'edit-$sVarname-' . md5($sVarname);
            $bIsEditable = array_search($sVarname, array_keys($aAttributes)) !== false;
            if($bIsEditable){

                if(!$aCfgForm=$o->getFormtype($sVarname)){
                    $sForm.= $renderAdminLTE->getAlert([
                        'type' => 'danger',
                        'title' => 'getFormtype('.$sVarname.')',
                        'dismissible' => 0,
                        'text' => 'ERROR: ' . $oDB->error(),
                    ]);
                    continue;
                }
                // $aCfgForm['label']=$sVarname;
                $aCfgForm['value']=$value;
                $aCfgForm['title']=($aCfgForm['title'] ?? '');
                $aCfgForm['title'].=($aCfgForm['title'] ? "\n" : '')."($sVarname)";
                
                // DEBUG
                // $sForm.='<pre>aCfgForm = '.print_r($aCfgForm, 1).'</pre>';
                
                $sHtmlPre=$aCfgForm['markup-pre']??'';
                $sHtmlPost=$aCfgForm['markup-post']??'';

                unset($aCfgForm['markup-pre']);
                unset($aCfgForm['markup-post']);

                if(array_search($sVarname, $aBasicAttributes)!==false){
                    $aCfgForm['overview'] = true;
                }
                $sInput = '';

                $sForm.=$sHtmlPre;
                // $sForm.='<pre>'.print_r($aCfgForm, 1).'</pre>';
                if (isset($aCfgForm['tag'])) {
                    switch ($aCfgForm['tag']) {
                        case 'input':
                            // $sForm.='<pre>'.print_r($aCfgForm, 1).'</pre>';
                            $sMore='';
                            if(isset($aCfgForm['datalist'])){
                                $sMore.=getDatalist($aCfgForm['datalist']);
                            }
                            $sForm.=$renderAdminLTE->getFormInput($aCfgForm) . $sMore;
                            break;
                        case 'select':
                            $sForm.= // '<pre>'.htmlentities($renderAdminLTE->getFormSelect($aCfgForm), 1).'</pre>'.
                                $renderAdminLTE->getFormSelect($aCfgForm);
                            break;;
                        case 'textarea':
                            $sForm.=// '<pre>'.htmlentities($renderAdminLTE->getFormTextarea($aCfgForm), 1).'</pre>'.
                                $renderAdminLTE->getFormTextarea($aCfgForm);
                            break;;
                        default:
                            $sForm.='UNHANDLED: form tag "'.$aCfgForm['tag'].'"<pre>UNHANDLED: '.htmlentities(print_r($aCfgForm, 1), 1).'</pre>';
                    }
                }
                $sForm.=$sHtmlPost;
            }

            /*
            if(preg_match('/^rel_/', $sVarname)){
                $sForm.="{{object.mapped_to_relation}} <strong>$sVarname</strong> -> ".print_r($value, 1)."<br>";
            }
            */

        }

        // hooks and callbacks
        if($iId && method_exists($o, 'hookActions')){
            $aHooks=$o->hookActions();
            if($aHooks['backend_preview'] && method_exists($o, $aHooks['backend_preview'])){

                if(strstr(get_class($o), 'axelhahn\pdo_db_attachments')){

                    // handle attachments
                    // echo '<pre>'.print_r($appmeta->getConfig('attachments'), 1).'</pre>';
                    $sMyBaseUrl=$appmeta->getConfig('attachments')['baseurl']??false;
                    $sMyBaseDir=$appmeta->getConfig('attachments')['basedir']??false;
                    if($sMyBaseUrl && $sMyBaseDir && is_dir($sMyBaseDir)){
                        $o->setUrlBase($sMyBaseUrl);
                        $o->setUploadDir($sMyBaseDir);
                        $sForm.= '<h3>{{object.attachment_preview}}</h3>'. $o->{$aHooks['backend_preview']}();
                    } else {
                        addMsg('error', '{{object.attachment-fix-app-config-in-attachments-key}}');
                    }
                } else {
                    // other objects
                    $sForm.= '<h3>{{object.object_preview}}</h3>'.$o->{$aHooks['backend_preview']}();
                }

            }
        }

        // $sForm.='<pre>relations: '.print_r($o->relRead(), 1).'</pre>';
        $sFooter .= ''
            . ($o->id()
                ? 
                    '<span style="float: right;">'
                    .$renderAdminLTE->getButton([
                        'class' => 'btn-danger',
                        'text' => icon::get('delete') . '{{delete}}',
                        'title' => '{{delete}}: '.$o->getLabel(),
                        'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$o->getLabel().'\n\n?\')) httprequest(\'POST\', \''.$sBaseUrl.'\' , {\'action\': \'delete\', \'id\': \''.$o->id().'\'}); return false',
                    ])
                    . '</span>'
                    : ''
                )
            . $renderAdminLTE->getButton([
                'type' => 'secondary',
                'text' => icon::get('abort') . '{{abort}}',
                'onclick' => 'location.href=\'' 
                    . ($o->id() ? $sBaseUrl : $sHomeAppUrl)
                    . '\'; return false;',
            ])
            .' '
            . $renderAdminLTE->getButton([
                'type' => 'primary',
                'text' => icon::get('save') . '{{save}}',
            ])
            .($iId ? 
                ' '
                . $renderAdminLTE->getButton([
                    'type' => 'primary',
                    'onclick' => '$(\'#frmEditObject\').attr(\'action\', \''.$sBaseUrl.'&id='.$o->id().'\'); $(\'#frmEditObject\').submit(); return false;',
                    'text' => icon::get('save') . '{{save_and_continue}}',
                ])
                : ''
              )
            . '</form>'
            ;

        $aObjdata['icon']??='object';
        $sMainContent .= $renderAdminLTE->getCard([
            'type' => ($iId ? 'primary' : 'success'),
            'variant' => 'outline',
            // 'tb-remove' => 1,
            // 'tb-collapse' => 1,
            'title' => ($iId 
                ? icon::get($aObjdata['icon']) . '{{edit}} <strong>' . $o->getLabel().'</strong>' 
                : icon::get('new') . '{{create_new_item}}') /* . ' :: ' . $sObject */,
            // 'tools' => '123',
            'text' => $sForm,
            'footer' => $sFooter,
        ]);

        if($iId){
            $aRelations=$o->relRead();

            // check relations
            $iRelErrors=0;
            foreach($aRelations['_targets']??[] as $sRelKey => $aRel){
                if(!$appmeta->isRelationAllowed($sObject, $aRel['table']) ){
                    $iRelErrors++;
                    addMsg('error', "{{msgerr.relation_not_allowed}}: $sObject -> <strong>$aRel[table]</strong>");   
                } else {
                    // addMsg('ok', "OK <strong>$aRel[table]</strong>");
                }
            }
            
            // $sMainContent.='<pre>'.print_r($aRelations, 1).'</pre>';

            // add attachment - for all objects but not the attachment object
            $sFormAttach=$sObject=="pdo_db_attachments" 
                ? ""
                : '<form action="'.$sBaseUrl.'" class="dropzone">'
                .'<input type="hidden" name="action" value="attach">'
                .'<input type="hidden" name="id" value="'.$o->id().'">'
                .'</form>'
                .'<br>'
                .$renderAdminLTE->getButton([
                    'class' => 'btn-outline-dark',                    
                    'text'=>'{{reload_page}}',
                    'onclick'=>'location.reload()',
                ])
                ;
            $sContextbar.=''
                . $renderAdminLTE->getCallout([
                    'type' => '',
                    'title' => icon::get('date').'{{last_changed}}',
                    'text' => $aItem['timeupdated'] ? '{{last_updated}}:<br>'.$aItem['timeupdated']: '{{created}}:<br>'.$aItem['timecreated'],
                ])
                . $renderAdminLTE->getCallout([
                    'type' => $iRelErrors ? 'danger' : '',
                    'title' => icon::get('relation').'{{relations}}'
                        .($iRelErrors 
                            ? ' '.$sItems=$renderAdminLTE->getBadge([
                                'type'=>'danger',
                                'text'=>$iRelErrors,
                            ])
                            : ''
                        )
                        ,
                    'text' => '<h3>' . (isset($aRelations['_targets']) ? count($aRelations['_targets']) : 0) . '</h3>',
                ])
                . ($sFormAttach
                    ? '<div id="frmAttach">'
                        . $renderAdminLTE->getCallout([
                            'type' => '',
                            'title' => icon::get('attachments').'{{attachments}}',
                            'text' => '<h3>...</h3>'.$sFormAttach,
                        ])
                        . '</div>'
                    : ''
                )
                ;
        };

        // ---------- relations of the selected item

        $sContentRelations='';

        if(isset($aItem['id']) && (int)$aItem['id']){


            if(!$sRelobjectname){

                // --- show Add buttons to existing object types

                // $sContentRelations.='<pre>'.print_r($aProjects, 1).'</pre>';

                // from index.php: $aObjects
                // foreach($aObjects as $sObjname){
                
                foreach($appmeta->getObjects() as $sObjname=>$aObjdata){
                    $bRelationAllowed=$appmeta->isRelationAllowed($sObject, $sObjname);
                    $sBtnClass= $bRelationAllowed
                        ? ($sObjname == $sObject ? 'secondary' : 'success')
                        : ''
                        ;

                    $sContentRelations.=''
                        . ($bRelationAllowed 
                            ?  $renderAdminLTE->getButton([
                                    'type' => ($sObjname == $sObject ? 'secondary' : 'success'),
                                    'text' => icon::get($appmeta->getObjectIcon($sObjname)) 
                                        . ($aObjdata['label'] ?? $sObjname),
                                    'onclick' => 'location.href=\'' . $sBaseUrl . '&id='.$aItem['id'].'&newrel='.$sObjname.'#relations\'',
                                ])
                            : $renderAdminLTE->getButton([
                                    'disabled' => 'disabled',
                                    'text' => icon::get($appmeta->getObjectIcon($sObjname)) 
                                        . ($aObjdata['label'] ?? $sObjname),
                                ])
                            ).' '
                    ;

                }
                $sContentRelations=$sContentRelations ? icon::get('new')  . '{{new}} ... '.$sContentRelations . '<br><br>' : '';
            } else {

                // --- form for adding a new relation of given object

                $sRelClass=getClassnameFromObjname($sRelobjectname);
                $oRel=initClass( $oDB, $sRelobjectname );
                $sUrlUp=$sBaseUrl.'&id='.$aItem['id'];
                $sFormRelations='';

                if(is_string($oRel)) {
                    // strng = error occured
                    $sFormRelations.=$oRel;
                } else {
                    // lookup all values
                    $aRelAtributes=$oRel->getBasicAttributes();
                    $aRelItems=$oRel->search(['columns'=>$aRelAtributes, 'order'=>$aRelAtributes]);

                    // relations of current object
                    $aRelationsOfType=$o->relRead(['table'=>$sRelobjectname])['_targets'] ?? [];
                    $aLinkedIds=[];
                    $aLinkedTableIds=[];

                    foreach($aRelationsOfType as $aRel){
                        // add id of linked relation that are between objects and not connected with a column
                        if(!$aRel['column']){
                            $aLinkedIds[]=$aRel['id'];
                        } else {
                            $aLinkedTableIds[]=$aRel['id'];                        
                        }
                    }

                    
                    if($aRelItems){
                        $sFormRelations.='<form method="POST" id="frmAddRel" action="'.$sUrlUp.'#relations">'
                            . '<input type="hidden" name="action" value="relCreate">'
                            . '<input type="hidden" name="relobject" value="'.$o->getTablename($sRelClass).'">'

                            . '<div class="row mb-3">'

                                . '<label for="selectnewrel" class="col-sm-2 col-form-label">'
                                    .icon::get($appmeta->getObjectIcon($sRelobjectname))
                                    .$appmeta->getObjectLabel($sRelobjectname).'<br>'
                                    // .'{{select_relation_item}}'
                                .'</label>'
                                . '<div class="col-sm-10">'
                                . '<select id="selectnewrel" name="relidlist[]" class="selectpicker form-control" size="1" multiple="multiple" data-live-search="true" title="--- {{select_relation_item}} ---">'
                                    ;
                                    if($aRelItems && count($aRelItems)){
                                        
                                        // $sFormRelations .= '<option value="">--- {{select_relation_item}} ---</option>';
                                        foreach($aRelItems as $aItem){
                                            // $sHintForOption='{{object.inactive_has_a_link_already}}';
                                            $sHintForOption='';
                                            $bEnabled=true;
                                            if ($aItem['id'] == $o->id() && $o->getTablename($sRelClass) == $o->getTable()) {
                                                $bEnabled=false;
                                                $sHintForOption.='{{object.inactive_current_item}} ';
                                            }
                                            if (array_search($aItem['id'], $aLinkedIds)!==false) {
                                                $bEnabled=false;
                                                $sHintForOption.='{{object.inactive_has_a_link_already}} ';
                                            }
                                            if (array_search($aItem['id'], $aLinkedTableIds)!==false) {
                                                $sHintForOption.='{{object.has_link_in_object}} ';
                                            }
                                            $sOptionLabel=$oRel->getLabel($aItem). ( $sHintForOption ? ' <<< '.$sHintForOption.'' : '');
                                            $sFormRelations .= $bEnabled
                                                ? '<option value="'.$aItem['id'].'">'.$sOptionLabel.'</option>'
                                                : '<option value=""        disabled>'.$sOptionLabel.'</option>'
                                                ;
                                        }
                                    } else {
                                        $sFormRelations .= '<option value="">--- {{select_no_data_set}} ---</option>';
                                    }
                            $sFormRelations.='</select>'
                            . '</div>'
                            . '</div>';
                    } else {
                        $sFormRelations.='{{no_items_found}}';
                    }
                }
                
                $sFooterRelations = ''
                    . $renderAdminLTE->getButton([
                        'type' => 'secondary',
                        'text' => icon::get('abort') . '{{abort}}',
                        'onclick' => 'location.href=\'' . $sUrlUp . '#relations\'; return false;',
                    ])
                    . ' '
                    . $renderAdminLTE->getButton([
                        'type' => 'primary',
                        'text' => icon::get('save') . '{{save}}',
                    ])
                    . '</form>';
        
                // $sFormRelations.='<pre>'.print_r($aRelItems, 1).'</pre>';

                $sContentRelations.= $renderAdminLTE->getCard([
                    'type' => 'success',
                    'variant' => 'outline',
                    // 'tb-remove' => 1,
                    // 'tb-collapse' => 1,
                    'title' => '{{object.add_relation_to}} <strong>'.$o->getLabel().'</strong>',
                    // 'tools' => '123',
                    'text' => $sFormRelations,
                    'footer' => $sFooterRelations,
                ])
                .'<br><br>';

            }

            // ---------- show existing relations
            $sRelObjects='';
            $sRelTable= '';
            // echo '<pre>'; print_r($aRelations); echo '</pre>'; die(__FILE__.':'.__LINE__);
            if(isset($aRelations['_targets'])){
                foreach($aRelations['_targets'] as $sRelKey=>$aRelation) {

                    $sTargetLabel=$aRelation['_target']['label']
                        ??($aRelation['_target']['displayname']
                            ??($aRelation['_target']['filename'] 
                                ??($sRelObjectname ?? '?') 
                            )
                        )
                        ;
                    $sBtnEdit=$renderAdminLTE->getButton([
                        'class' => 'btn-outline-dark',
                        'text' => icon::get('edit') . '{{edit}}',
                        'title' => '{{edit}} '.$sTargetLabel,
                        // 'onclick' => 'location.href=\'?page=object&app='.$sTabApp.'&object='.$aRelation['table'].'&id=' . $oRelobj->id() . '\'',
                        'onclick' => 'location.href=\''. $sBaseAppUrl . '&object='.$aRelation['table'].'&id=' . $aRelation['id'] . '\'',
                        
                    ]);
                    $sBtnDel=$aRelation['column']
                        ? $renderAdminLTE->getButton([
                            'disabled' => 'disabled',
                            'text' => icon::get('delete') . '{{delete}}',
                        ])
                        : $renderAdminLTE->getButton([
                        'class' => 'btn-outline-danger',
                        'text' => icon::get('delete') . '{{delete}}',
                        'title' => 'Delete relation '.$o->getLabel().' -> '.$sTargetLabel,
                        'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$o->getLabel().' -> '.$sTargetLabel.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'relDelete\', \'relkey\': \''.$sRelKey.'\'});',
                    ]);

                    

                    $sRelTable.= '<tr'
                            .($appmeta->isRelationAllowed($sObject, $aRelation['table']) ? '' : ' class="table-danger"')
                        .'>'
                        .'<td>'
                            .'<a href="'.$sBaseAppUrl . '&object='.$aRelation['table'].'&id=' . $aRelation['id'].'"><strong>'
                            .icon::get($appmeta->getObjectIcon($aRelation['table'])).' '.$sTargetLabel
                            .'</strong></a>'
                        .'</td>'
                        .'<td>'.$aRelation['column'].'</td>'
                        .'<td>'.$sRelKey.'</td>'
                        
                        .'<td align="right"><nobr>'.$sBtnEdit.' '.$sBtnDel.'</nobr></td>'
                        ;
                }
            }
            $sRelObjects.= $sRelTable 
                ? '<table class="table table-bordered table-striped dataTable dtr-inline">'
                    . '<thead>'
                    .'<tr>'
                        .'<th>{{td-rel-target}}</th>'
                        .'<th>{{td-rel-column}}</th>'
                        .'<th>{{td-rel-key}}</th>'
                        .'<th class="actions">{{td-actions}}</th>'
                    .'</tr></thead>'
                    .'<tbody>'.$sRelTable.'</tbody></table>' 
                : '';
            // $sMainContent.=$renderAdminLTE->addRow($sRelObjects);


            $sMainContent.= ''
                .'<br><br><div id="msgOtherChanges">'
                    .'<br>'
                    .$renderAdminLTE->getCallout([
                        'type' => 'danger',
                        'text' => '{{object.relations_locked}}',
                    ])
                .'</div>'
                .'<div id="relations">'
                .$renderAdminLTE->addRow(
                    $renderAdminLTE->addCol('', 1)
                    .$renderAdminLTE->addCol(
                        $renderAdminLTE->getCard([
                            'type' => 'dark',
                            'variant' => 'outline',
                            // 'tb-remove' => 1,
                            // 'tb-collapse' => 1,
                            'title' => icon::get('relation').'{{relations}}',
                            // 'tools' => '123',
                            'text' => $sContentRelations . $sRelObjects,
                            // 'footer' => $sFooterRelations,
                        ]), 11)
                )
                .'</div>'
                ;
        }
    }

}

$BODY = $renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg().$sMainContent,
        10
    )
    . $renderAdminLTE->addCol(
        $sContextbar,
        2
    )
);
