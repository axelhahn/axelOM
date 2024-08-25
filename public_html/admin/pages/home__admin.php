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

$s=''
    . $renderAdminLTE->addRow(
        $renderAdminLTE->addCol(
            $renderAdminLTE->getInfobox(array (
                'type' => '',
                'shadow' => '',
                'icon' => icon::getclass('apps'),
                'iconbg' => '',
                'text' => '{{home.apps}}',
                'number' => (count($adminmetainfos->getApps()) ?? 0 )
                    . ($acl->isAdmin()
                        ? '<span style="float: right">'.$renderAdminLTE->getButton([
                            'type' => 'success',
                            'text' => icon::get('new').'{{new}} :: TODO',
                            'onclick' => '',
                        ]).'</span>'
                        : ''
                    )
                    ,
                // 'progressvalue' => 70,
                // 'progresstext' => '70% Increase in 30 Days',
                ))
            , 4
        )

    )
    .'<br>'
;

$sBoxes='';
foreach ($adminmetainfos->getApps(1) as $sApp => $aAppData){
    if ($acl->canView($sApp)){

        $appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);
        $iCount=count($appmeta->getObjects());
        $sItems=$renderAdminLTE->getBadge([
            'type'=>$iCount ? 'success' : 'secondary',
            'title'=>'{{home.count}}: '.$iCount,
            'text'=>$iCount ? $iCount : 'NA',
        ]);;

        $sBoxContent=''
            .($appmeta->getApphint() && $appmeta->getApphint() ? $appmeta->getApphint().'<br>' : '')
            .'{{home.objects}}: '.$sItems.'<br>';
        $sBoxes.=$renderAdminLTE->addCol(
            $renderAdminLTE->getCard(array (
                'type' => '',
                'title' => '<a href="?app='.$sApp.'&page=home">' .icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</a>',
                'text' => $sBoxContent,
                // 'variant' => '',
            )),
            3
        );
    }
}
$s.=$renderAdminLTE->addRow($sBoxes);


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        ($sContextbar ? 10 : 12)
        )
    . ($sContextbar 
        ? $renderAdminLTE->addCol($sContextbar,2)
        : ''
    )
);
