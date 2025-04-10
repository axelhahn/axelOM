<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * ADMIN USERS :: Overview of all users and their permissions
 * 
 */
include "inc_functions.php";

if(!$acl->isGlobalAdmin()){
    include('error403.php');
    return false;
}

$sContextbar='';
$TITLE=icon::get('users') . '{{users.title}}';
$BANNER='{{users.banner}}';

$sPerms='';
$s='';

function yesno($bValue){
    return $bValue 
        ? '<span class="btn btn-success">'.icon::get('yes') .'</span>'
        : '<span class="btn btn-default">'.icon::get('no').'</span>'
        ;
}


if (!isset($aSettings['acl'])){
    $s.=$renderAdminLTE->getCard([
        'type' => '',
        'title' => '',
        'text' => '{{users.noacl}}'
        ,
        // 'variant' => '',
    ]);
} else{

    $aUsers=[];

    $aGroups=$aSettings['acl']['groups'] ?? [];
    // print_r($aGroups); die();

    if (isset($aGroups['global']['admin']) && is_array($aGroups['global']['admin'])) {
        $sGlobalAdmins='<div class="text-success"><strong>'.icon::get('adminuser') .implode('<br>'.icon::get('user'),$aGroups['global']['admin']).'</strong></div><br>';
    } else {
        $sGlobalAdmins='-';
    }

    foreach ($adminmetainfos->getApps(1) as $sApp => $aAppData){
        $appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);

        if(isset($aGroups[$sApp])){
            $aAppusers=[];
            $sTable='';

            // read all users from all groups, uniq and sorted
            foreach($aGroups[$sApp] as $sGroup => $aMembers){
                foreach ($aMembers as $sMember){
                    $aAppusers[$sMember]=1;
                }
            }
            $aAppusers=array_keys($aAppusers);
            sort($aAppusers);

            // render table data with users and their permissions
            // echo '<pre>';print_r($aAppusers); die();
            foreach($aAppusers as $sAppuser){
                $aclapp=new adminacl();
                $aclapp->setUser($sAppuser);
                $sTable.='<tr>'
                .'  <td>'.icon::get('user') .'<strong>'. $sAppuser.'</strong><br>'.implode(', ', $aclapp->getGroups()).'<br></td>'
                .'  <td>'.yesno($aclapp->canView($sApp)).'</td>'
                .'  <td>'.yesno($aclapp->canEdit($sApp)).'</td>'
                .'  <td>'.yesno($aclapp->isAppAdmin($sApp)).'</td>'
                .'</tr>'."\n\n"
            ;

            }
            $sPerms.=($sTable
                    ? '<tr><th><h5 class="text-primary">'.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</h5></th><th>{{perms.view}}</th><th>{{perms.edit}}</th><th>{{perms.admin}}</th></tr>'
                        .''.$sTable.''
                    : '<tr><td>'.icon::get('close').' {{perms.none}}</td></tr>'
                )
                ;
        }
    }


    $s.=''

        .$renderAdminLTE->getCard([
            'type' => '',
            'title' => '',
            'text' => ''
            .'{{users.info}}<br><br>'
            .'<table class="table"><tbody>

            <tr>
                <td>{{users.global_admins}}</td>
                <td>'.$sGlobalAdmins.'</td>
            </tr>
 
            <tr>
                <td>{{users.app_permissions}}</td>
                <td><table class="table">'.$sPerms.'</table></td>
            </tr>

            </tbody></table>'
            
            /*
            . '<form id="frmEditObject" class="form-horizontal" action="?" method="POST">'
            .' <textarea cols="40" rows="30" style="width: 100%">'.file_get_contents(__DIR__.'/../config/settings.php').'</textarea>'
            . '</form>'
            */

            ,
            // 'variant' => '',
        ])

    ;

}


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
