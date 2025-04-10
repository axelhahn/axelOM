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
 * included in home.php
 * 
 */

$sContextbar='';
$TITLE='{{home.welcome}}';
$BANNER='{{home.banner}}';

require_once('inc_functions.php');

if($_POST['action']??false=='appCreate'){
    if ($acl->isGlobalAdmin()){
        // 
        // echo "TODO: create App <pre>".print_r($_POST, true)."</pre>";
        $bOK=true;
        if(!$_POST['appname']??false){
            addMsg('error', '{{msgerr.missing_appname}}');
            $bOK=false;
        } 
        if(!$_POST['label']??false){
            addMsg('error', '{{msgerr.missing_label}}');
            $bOK=false;
        } 
        if($bOK) {
            $sApp=preg_replace('/[^a-z0-9]/', '', strtolower($_POST['appname']));
            $sLabel=$_POST['label']??$sApp;
            $appmeta=new appmetainfos();
            if ($appmeta->create(
                $sAppRootDir.'/'. $sApp, 
                [
                    'label' => $sLabel,
                    'icon' => $_POST['icon']??'',
                    'hint' => $_POST['hint'] ?? "TODO: $sLabel",
                ])){
                    addMsg('ok', '{{msgok.app_created}}');
                    header('Location: ?app='.$appmeta->getId().'&page=home');
                };
        }
    } else {
        addMsg('error', '{{msgerr.missing_permissions}}');
    }    
}

$sFormNewApp='<form method="POST" id="frmAddRel" action="">'
    . '<input type="hidden" name="action" value="appCreate">'

    . $renderAdminLTE->getCard([
        'title' => '{{newapp.title}}',
        'text' => 
            $renderAdminLTE->getFormInput([
                'type' => 'text',
                'name' => 'appname',
                'label' => '{{newapp.appname}}',
                'value' => $_POST['appname']??'',
                'required' => 'required',
                'placeholder' => '{{newapp.appname-placeholder}}',
            ])
            . $renderAdminLTE->getFormInput([
                'type' => 'text',
                'name' => 'icon',
                'label' => '{{newapp.icon}}',
                'value' => $_POST['icon']??'fa fa-puzzle-piece',
                'required' => 'required',
                // 'placeholder' => '{{newapp.appname}}',
            ])
            .$renderAdminLTE->getFormInput([
                'type' => 'text',
                'name' => 'label',
                'label' => '{{newapp.label}}',
                'value' => $_POST['label']??'',
                'required' => 'required',
                'placeholder' => '{{newapp.label-placeholder}}',
            ])
            .$renderAdminLTE->getFormInput([
                'type' => 'text',
                'name' => 'description',
                'label' => '{{newapp.description}}',
                'value' => $_POST['description']??'',
            ])
            ,
        'footer' => 
            $renderAdminLTE->getButton([
                'type' => 'secondary',
                'onclick' => 'overlayHide(); return false;',
                'text' => icon::get('abort') . '{{abort}}',
            ])
            .' '
            . $renderAdminLTE->getButton([
                'type' => 'primary',
                'text' => icon::get('save') . '{{save}}',
            ])

    ])
    

.'</form>'
;
$s=''
    . $renderAdminLTE->addRow(
        $renderAdminLTE->addCol(
            $renderAdminLTE->getInfobox([
                'type' => '',
                'shadow' => '',
                'icon' => icon::getclass('apps'),
                'iconbg' => '',
                'text' => '{{home.apps}}',
                'number' => (count($adminmetainfos->getApps()??[]))
                    ,
                // 'progressvalue' => 70,
                // 'progresstext' => '70% Increase in 30 Days',
                ])
            , 4
        )
        .$renderAdminLTE->addCol(
            $acl->isGlobalAdmin()
                        ? '<span style="float: right">'.$renderAdminLTE->getButton([
                            'type' => 'success',
                            'text' => icon::get('new').'{{new}}',
                            'onclick' => 'overlayDisplay(document.getElementById(\'frmNewApp\').innerHTML); return false;',
                        ]).'</span>'
                        : ''
                        ,1
        )


    )
    .'<br>
    <div id="frmNewApp" class="display-none">'
    .($acl->isGlobalAdmin()
        ? $renderAdminLTE->addRow(
            $renderAdminLTE->addCol(
                $sFormNewApp
            , 12
            )
        )
        : ''
    )
    .'</div>'
;

$sBoxes='';
foreach ($adminmetainfos->getApps(1) as $sApp => $aAppData){
    if ($acl->canView($sApp)){

        $appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);
        $iCount=count($appmeta->getObjects());
        $sItems=$renderAdminLTE->getBadge([
            'type'=>$iCount ? 'success' : 'secondary',
            'title'=>'{{home.count}}: '.$iCount,
            'text'=>$iCount ?: 'NA',
        ]);;

        $sBoxContent=''
            .($appmeta->getApphint() && $appmeta->getApphint() ? $appmeta->getApphint().'<br>' : '')
            .'{{home.objects}}: '.$sItems.'<br>';
        $sBoxes.=$renderAdminLTE->addCol(
            $renderAdminLTE->getCard([
                'type' => '',
                'title' => '<a href="?app='.$sApp.'&page=home">' .icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</a>',
                'text' => $sBoxContent,
                'class' => 'height10em'
                // 'variant' => '',
            ]),
            3
        );
    }
}
$s.=$renderAdminLTE->addRow($sBoxes);


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg() . $s, 
        $sContextbar ? 10 : 12
        )
    . ($sContextbar 
        ? $renderAdminLTE->addCol($sContextbar,2)
        : ''
    )
);
