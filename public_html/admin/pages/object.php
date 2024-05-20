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

require_once('inc_functions.php');

$sBaseAppUrl = '?app='.$sTabApp.'&page=object';
$sBaseUrl    = '?app='.$sTabApp.'&page=object&object=' . $sObject;

$aObjdata=$appmeta->getObject($sObject);
$sObjLabel= ''
    . (isset($aObjdata['icon']) ? icon::get($aObjdata['icon']) : '')
    . ($aObjdata['label'] ?? '');

$TITLE=''.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().' ' 
    . '<strong>'.($sObjLabel ? '  ' . $sObjLabel : '') .'</strong> ';

// addMsg('ok', 'OK: I am a test');

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

$sMainContent = ''
. ($appmeta->getApphint() && $appmeta->getApphint() 
    ? $renderAdminLTE->getCallout(array (
        'type' => 'info',
        'text' => $appmeta->getApphint() . ' -> <strong>' . $appmeta->getObjectHint($sObject).'</strong>',
    )).'<br>'
    : '');

// $sMainContent.='<pre>_POST = '.print_r($_POST, 1).'</pre>';

if (isset($_POST['action'])) {
    $aItemData = [];
    if($_POST['action']=='new' || $_POST['action']=='edit') {
        foreach (array_keys($aAttributes) as $sAttr) {
            $aItemData[$sAttr] = $_POST[$sAttr];
        }
    }
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
                    addMsg('ok', '{{msgok.item_was_created}}: ' . $o->id() . ' '.$sBtnEdit);
                }
            } else {
                addMsg('error', '{{msgerr.wrong_itemdata}}');
            }
            break;;

        case 'edit':
            $aItemData['id'] = queryparam::post('id', false, 'int');
            $o->read($aItemData['id']);
            $sBtnEdit=' ... '.$renderAdminLTE->getButton([
                'type' => 'dark',
                'text' => icon::get('edit') . '{{edit}}',
                'title' => '{{edit}}: '.$o->getLabel($aItemData),
                'onclick' => 'location.href=\'' . $sBaseUrl.'&id='.$aItemData['id'] . '\'',
            ]);
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
            $sTargetId=queryparam::post('relid', false, 'int');
            if($sTargetId && $sTargetObj){
                if ($o->relCreate($sTargetObj, $sTargetId)){
                    addMsg('ok', '{{msgok.relation_was_created}} ' . $sTargetObj . ':'.$sTargetId. '');
                } else {
                    addMsg('error', '{{msgerr.relation_was_not_created}} [' . $sTargetObj . ':'.$sTargetId. ']');
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
            foreach($_FILES as $aFile){
                $target_file=$sUploadpath . basename($aFile["name"]);
                if($aFile['error']==0){
                    if (move_uploaded_file($aFile["tmp_name"], $target_file)) {
                        $out.= "{{msgok.fileupload_stored_as}} ".$target_file. PHP_EOL;
                        $oFile->new();
                        $oFile->setItem([
                            'filename'=>$aFile["name"],
                            'mime'=>$aFile["type"],
                            'description'=>'',
                            'size'=>$aFile["size"],
                            'width'=>NULL,
                            'height'=>NULL,
                        ]);
                        if($oFile->save()){
                            $sTargetId=$oFile->id();
                            $sTargetObj='pdo_db_attachments';
                            $out.= "{{msgok.fileupload_object_created}} #".$sTargetId. PHP_EOL;
                            if ($o->relCreate($sTargetObj, $sTargetId)){
                                $out.= '{{msgok.relation_was_created}} ' . $sTargetObj . ':'.$sTargetId. ':-)'. PHP_EOL;
                            } else {
                                header('503 Service Unavailable', true, 503);
                                $out.= '{{msgerr.relation_was_not_created}} [' . $sTargetObj . ':'.$sTargetId. '] :-('. PHP_EOL;
                                die($out);
                            }
                        } else {
                            header('503 Service Unavailable', true, 503);
                            $out.= '{{msgerr.fileupload_not_added_in_database}}:-('. PHP_EOL;
                            die($out);
                        }
            
                    } else {
                        header('503 Service Unavailable', true, 503);
                        die('{{msgerr.fileupload_move_failed}}: ' . $aFile["error"]);
                    }
                }
            }
            die("{{msgok.fileupload_and_relation}}");
            // break;;

        default:
            header('http/1.0 400 Bad Request');
            addMsg('error', 'OOPS: POST action [' . htmlentities($_POST['action']) . '] is not handled (yet). :-/');
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
    $o->read($iId, true);
    $TITLE = '<a href="'.$sBaseUrl.'">'.$TITLE.'</a> :: '.$o->getLabel();

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

        
        $sTable = '' . "\n";
        $sTable .= '<table class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
            <tr>';
        foreach($aBasicAttributes as $sField){
            $sTable .= $sField=='id' ? '' : '<th>'.$sField.'</th>';
        }
        $sTable .= '<th>{{tdactions}}</th></tr>
            </thead>
            <tbody>' . "\n";

        foreach ($aItems as $aRow) {

            $sTable .= '<tr>';
            foreach($aBasicAttributes as $sField){
                if($sField=='id'){
                    continue;
                }
                $sText=$aRow[$sField];
                if($sField=='label'){
                    $sText='<a href="'.$sBaseUrl . '&id=' . $aRow['id'] . '"><strong>'.$aRow[$sField].'</strong></a>';
                }
                $sTable .= '<td>'.$sText.'</td>';
            }
            $sTable .= '<td align="right">'
                . ''
                . ($bDbTableOk 
                    ? $renderAdminLTE->getButton([
                        'type' => 'dark',
                        'text' => icon::get('edit') . '{{edit}}',
                        'title' => '{{edit}}: '.$o->getLabel($aRow),
                        'onclick' => 'location.href=\'' . $sBaseUrl . '&id=' . $aRow['id'] . '\'',
                    ])
                    : '')
                .' '
                .$renderAdminLTE->getButton([
                    'type' => 'danger',
                    'text' => icon::get('delete') . '{{delete}}',
                    'title' => '{{delete}}: '.$o->getLabel($aRow),
                    'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$o->getLabel($aRow).'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'delete\', \'id\': \''.$aRow['id'].'\'});',
                ])

                . '</td>' . "\n";

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
                . ($bDbTableOk
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
                        'type' => 'primary',
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
            'type' => 'primary',
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
                'text' => '<h3>{{object.tablecheck_ok}}</h3><hr>'
                    .$renderAdminLTE->getButton([
                        'type' => '',
                        'text' => icon::get('config').'{{config}}',
                        'onclick' => 'location.href=\'' . $sObjEditUrl . '\';',
                    ])
            
            ])
            :$renderAdminLTE->getAlert([
                'type' => 'danger',
                'title' => '{{error}}',
                'text' => icon::get('database').'{{object.tablecheck}}<br><hr>'
                    // .'{{object.tablecheck_update_required}}'
                    .$renderAdminLTE->getButton([
                        'type' => 'dark',
                        'text' => icon::get('config').'{{config}}',
                        'onclick' => 'location.href=\'' . $sObjEditUrl . '\';',
                    ])
            
            ])
        )
        ;
        // $sMainContent.=$sTable;
    }
}


if ($bShowEdit) {

    // ---------- show editor: first item / new item / edit id

    $aItem = $o->getItem();
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

            $aCfgForm=$o->getFormtype($sVarname);
            // $aCfgForm['label']=$sVarname;
            $aCfgForm['value']=$value;
            
            // DEBUG
            // $sForm.='<pre>aCfgForm = '.print_r($aCfgForm, 1).'</pre>';
            
            $sInput = '';
            if (isset($aCfgForm['tag'])) {
                switch ($aCfgForm['tag']) {
                    case 'input':
                        $sForm.=$renderAdminLTE->GetFormInput($aCfgForm);
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
        }

        /*
        if(preg_match('/^rel_/', $sVarname)){
            $sForm.="{{object.mapped_to_relation}} <strong>$sVarname</strong> -> ".print_r($value, 1)."<br>";
        }
        */

    }
    // $sForm.='<pre>relations: '.print_r($o->relRead(), 1).'</pre>';
    $sFooter .= ''
        . ($iItems
            ? $renderAdminLTE->getButton([
                'type' => 'secondary',
                'text' => icon::get('abort') . '{{abort}}',
                'onclick' => 'location.href=\'' . $sBaseUrl . '\'; return false;',
                ])
                . ' '
            : ''
        )
        . $renderAdminLTE->getButton([
            'type' => 'primary',
            'text' => icon::get('save') . '{{save}}',
        ])
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
        
        
        // $sContextbar.='<pre>'.print_r($aRelations, 1).'</pre>';
        $sFormAttach='<form action="'.$sBaseUrl.'" class="dropzone">'
            .'<input type="hidden" name="action" value="attach">'
            .'<input type="hidden" name="id" value="'.$o->id().'">'
            .'</form>'
            .'<br>'
            .$renderAdminLTE->getButton([
                'type'=>'',
                'text'=>'{{reload_page}}',
                'onclick'=>'location.reload()',
            ])
            ;
        $sContextbar.=''
            . $renderAdminLTE->getCallout([
                'type' => 'primary',
                'title' => icon::get('date').'{{last_changed}}',
                'text' => $aItem['timeupdated'] ? '{{last_updated}}:<br>'.$aItem['timeupdated']: '{{created}}:<br>'.$aItem['timecreated'],
            ])
            . $renderAdminLTE->getCallout([
                'type' => 'primary',
                'title' => icon::get('relation').'{{relations}}',
                'text' => '<h3>' . (isset($aRelations['_targets']) ? count($aRelations['_targets']) : 0) . '</h3>',
            ])
            . '<div id="frmAttach">'
            . $renderAdminLTE->getCallout([
                'type' => 'primary',
                'title' => icon::get('attachments').'{{attachments}}',
                'text' => '<h3>...</h3>'.$sFormAttach,
            ])
            . '</div>'
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
                $sContentRelations.=''
                    . $renderAdminLTE->getButton([
                        'type' => ($sObjname == $sObject ? 'secondary' : 'success'),
                        'text' => icon::get('new') . ($aObjdata['label'] ?? $sObjname),
                        'onclick' => 'location.href=\'' . $sBaseUrl . '&id='.$aItem['id'].'&newrel='.$sObjname.'#relations\'',
                    ]) .' '
                ;

            }
            $sContentRelations=$sContentRelations ? '{{new}} '.$sContentRelations . '<br><br>' : '';
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

                        . '<div class="form-group row">'

                            . '<label for="selectnewrel" class="col-sm-2 col-form-label">'
                                .icon::get($appmeta->getObjectIcon($sRelobjectname))
                                .$appmeta->getObjectLabel($sRelobjectname).'<br>'
                                // .'{{select_relation_item}}'
                            .'</label>'
                            . '<select id="selectnewrel" name="relid" class="selectpicker col-sm-10 form-control" size="1" data-live-search="true" title="--- {{select_relation_item}} ---">'
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

                $sTargetLabel=isset($aRelation['_target']['label']) 
                    ? $aRelation['_target']['label']
                    : (isset($aRelation['_target']['displayname']) 
                        ? $aRelation['_target']['displayname'] 
                        : ($sRelObjectname ?? '?')
                    )
                    ;
                $sBtnEdit=$renderAdminLTE->getButton([
                    'type' => 'dark',
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
                    'type' => 'danger',
                    'text' => icon::get('delete') . '{{delete}}',
                    'title' => 'Delete relation '.$o->getLabel().' -> '.$sTargetLabel,
                    'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$o->getLabel().' -> '.$sTargetLabel.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'relDelete\', \'relkey\': \''.$sRelKey.'\'});',
                ]);


                $sRelTable.= '<tr>'
                    .'<td>'.icon::get($appmeta->getObjectIcon($aRelation['table'])).' '.$sTargetLabel.'</td>'
                    .'<td>'.$aRelation['column'].'</td>'
                    .'<td>'.$sRelKey.'</td>'
                    
                    .'<td align="right">'.$sBtnEdit.' '.$sBtnDel.'</td>'
                    ;
            }
        }
        $sRelObjects.= $sRelTable 
            ? '<table class="table table-bordered table-striped dataTable dtr-inline">'
                . '<thead>'
                .'<tr>'
                    .'<th>Target</th>'
                    .'<th>column</th>'
                    .'<th>key</th>'
                    .'<th>actions</th>'
                .'</tr></thead>'
                .'<tbody>'.$sRelTable.'</tbody></table>' 
            : '';
        // $sMainContent.=$renderAdminLTE->addRow($sRelObjects);


        $sMainContent.= ''
            .'<br><br><div id="msgOtherChanges">'
                .'<br>'
                .$renderAdminLTE->getCallout(array (
                    'type' => 'danger',
                    'text' => '{{object.relations_locked}}',
                ))
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
