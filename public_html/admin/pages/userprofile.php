<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * ADMIN HOME :: Overview for existing apps
 * 
 */

$sContextbar='';
$TITLE=icon::get('user') . $acl->getUserDisplayname();

$sPerms='';

function yesno($bValue){
    return $bValue 
        ? '<span class="btn btn-success">'.icon::get('yes') .'</span>'
        : '<span class="btn btn-default">'.icon::get('no').'</span>'
        ;
}

foreach ($adminmetainfos->getApps(1) as $sApp => $aAppData){
    $appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);
    if ($acl->canView($sApp)){
        $sPerms.='<tr>'
            .'<td>'.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</td>'
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
    . $renderAdminLTE->getCallout([
        'type' => 'info',
        'text' => icon::get('user') . '{{userprofile.banner}}',
        ]).'<br><br>'

    .$renderAdminLTE->getCard(array (
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
    ))

;


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
