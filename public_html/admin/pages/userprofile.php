<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * USER PROFILE :: settings and permissions
 * 
 */

$sContextbar='';
$TITLE=icon::get('user') . $acl->getUserDisplayname();
$BANNER='{{userprofile.banner}}';

$sPerms='';

require_once  __DIR__.'/../classes/usersettings.class.php';
require_once  'inc_functions.php';

// ------------------------------------------------------------------------
// functions
// ------------------------------------------------------------------------

function yesno($bValue){
    return $bValue 
        ? '<span class="btn btn-success">'.icon::get('yes') .'</span>'
        : '<span class="btn btn-default">'.icon::get('no').'</span>'
        ;
}

function getLanguages():array{
    $aLanguages=[];
    foreach (glob(__DIR__.'/../lang/*.php') as $sFile){
        $aLanguages[]=basename($sFile,'.php');
    }
    return $aLanguages;
}

function getThemes():array{
    $aThemes=[];
    foreach (glob(__DIR__.'/../themes/*') as $sTheme){
        if(is_dir($sTheme) && preg_match('/^[a-z0-9]+$/', basename($sTheme))){
            $aThemes[]=basename($sTheme);    
        }
    }
    return $aThemes;
}

function getUserconfig(){
    global $acl;
    $renderAdminLTE=new renderadminlte();

    $sReturn='';
    $aCfg = new usersettings($acl->getUser());
    
    // $sReturn.=print_r(getThemes(), true);
    $sSelectLang='';
    $sSelectLang.='<option value="" '.(!$aCfg->getConfig()['lang'] ? 'selected' : '').'>{{userprofile.system_default}}</option>';
    foreach(getLanguages() as $sLang){
        $sSelectLang.='<option value="'.$sLang.'" '.($sLang==$aCfg->getConfig()['lang'] ? 'selected' : '').'>'.$sLang.'</option>';
    }

    $sSelectTheme='';
    $sSelectTheme.='<option value="" '.(!$aCfg->getConfig()['theme'] ? 'selected' : '').'>{{userprofile.system_default}}</option>';
    foreach(getThemes() as $sTheme){
        $sSelectTheme.='<option value="'.$sTheme.'" '.($sTheme==$aCfg->getConfig()['theme'] ? 'selected' : '').'>'.$sTheme.'</option>';
    }

    $sReturn.='<form action="" method="post">
    <input type="hidden" name="action" value="save">

    <div class="row">
        <label class="col-sm-2 col-form-label">{{userprofile.language}}:</label> 
        <div class="col-sm-9">
            <div class="form-group">
                <select name="lang" class="form-control">
                    '.$sSelectLang.'
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-2 col-form-label">{{userprofile.theme}}:</label> 
        <div class="col-sm-9">
            <div class="form-group">
                <select name="theme" class="form-control">
                    '.$sSelectTheme.'
                </select>
            </div>
        </div>
    </div>
    <hr>


    '.
    $renderAdminLTE->getButton([
        'type' => 'primary',
        'text' => icon::get('save') . '{{save}}',
    ])
    .'

    </form>';

    return $sReturn;
}

// ------------------------------------------------------------------------
// main
// ------------------------------------------------------------------------

if(($_POST['action']??'')==='save'){
    $oCfg = new usersettings($acl->getUser());
    unset($_POST['action']);
    $oCfg->setValues($_POST);

    if ($oCfg->save()) {
        addMsg('ok', '{{msgok.file_was_saved}}');
    } else {
        addMsg('error', '{{msgerr.file_was_not_saved}}');
    }    
    $BODY=renderMsg() . "<script>window.setTimeout(function(){window.location.href='?page=userprofile'; window.location.reload();}, 3000);</script>";
    return true;
    // header('Location: ?page=userprofile');
}

foreach ($adminmetainfos->getApps(1) as $sApp => $aAppData){
    $appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);
    if ($acl->canView($sApp)){
        $sPerms.='<tr>'
            .'<td><a href="?app='.$sApp.'&page=home"><strong>'.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</a></strong><br>'.$appmeta->getApphint().'</td>'
            .'<td>'.yesno($acl->canView($sApp)).'</td>'
            .'<td>'.yesno($acl->canEdit($sApp)).'</td>'
            .'<td>'.yesno($acl->isAppAdmin($sApp)).'</td>'
            .'</tr>'
        ;

    }
}

$sPerms=$sPerms 
    ? '<table class="table"><thead><tr>
    <th>{{app}}</th><th>{{perms.view}}</th><th>{{perms.edit}}</th><th>{{perms.admin}}</th>
    </tz></thead><tbody>'.$sPerms.'</tbody></table>'
    : icon::get('close').' {{perms.none}}'
    ;

$s=''
    .$renderAdminLTE->getCard([
        'type' => '',
        'title' => '',
        'text' => ''
            .'<table class="table"><tbody>

                <tr>
                    <td>{{userprofile.userid}}</td>
                    <td><strong>'.$acl->getUser().'</strong></td>
                </tr>
                <tr>
                    <td>{{userprofile.name}}</td>
                    <td>'.$acl->getUserDisplayname().'</td>
                </tr>

                <!-- hook -->
                {{CONTENT_HOOK_1}}

                <!-- user specific config -->
                <tr>
                    <td>'.icon::get('config').' {{userprofile.config}}</td>
                    <td>'.getUserconfig().'<br><br></td>
                </tr>


                <tr>
                    <td>'.icon::get('users').' {{userprofile.groups}}</td>
                    <td><ul><li>'.implode('</li><li>',$acl->getGroups()).'</li></td>
                </tr>
                <tr>
                    <td>'.icon::get('yes') .' {{userprofile.permissions}}</td>
                    <td>'.$sPerms.'</td>
                </tr>
            
            </tbody></table>'
            .'<br>'
            . $renderAdminLTE->getButton([
                'type' => 'primary',
                'text' => icon::get('back') . '{{back}}',
                'onclick' => 'history.back();',
                ])
            ,
        // 'variant' => '',
    ])

;


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
