<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * TOOLS :: Backup + Resore
 * 
 */

require_once('inc_functions.php');

$sContextbar='';
$sBackuppath=dirname(dirname(__DIR__)).'/apps/'.$sTabApp.'/data/';

// $sBaseAppUrl = '?page=tools&app='.$sTabApp;
// $sBaseUrl    = '?page=tools&app='.$sTabApp;

if($sTabApp){

    if(!$acl->isAppAdmin($sTabApp)){
        $BODY='';
        return false;
    }
    
    // ----------------------------------------------------------------------
    // Actions
    // ----------------------------------------------------------------------
    $sAction=queryparam::post('action', '/^[a-z]*$/');

    $sOutAction='';

    include('inc_set_db.php'); // set $oDB

    if($sAction){
        // $sOutAction.='ACTION = '.$sAction.'<br>';
        $sFile=queryparam::post('file', '/^[a-z\.0-9\-]*$/');
        $sBackupfile=$sBackuppath.'/'.$sFile;

        switch ($sAction) {
            case 'backup':
                $sBackupfile=$sBackuppath.'/'.$sTabApp.'-'.$oDB->driver().'-'.date('U').'.json';
                $aDumpdata=$oDB->dump();
                if(isset($aDumpdata)){

                    if (file_put_contents($sBackupfile, json_encode($aDumpdata, JSON_PRETTY_PRINT))){
                        addMsg('ok', '{{msgok.tools_backup_created}}');
                        $sOutAction.='{{msgok.tools_backup_created}}<br>';
                    } else {
                        addMsg('error', '{{msgerr.tools_backup_file_not_written}}');
                    }
                } else {
                    addMsg('error', '{{msgerr.tools_backup_no_data}}');
                }
                break;
            case 'restore':
                    $sBackupfile=$sBackuppath.'/'.$sFile;
                    $oDB->setDebug(1); 
                    
                    if ($oDB->import($sBackupfile)){
                        addMsg('ok', '{{msgok.tools_backup_restored}}');
                    } else {
                        addMsg('error', '{{msgerr.tools_restore_failed}}');
                    }
                    $oDB->setDebug(0);
                    break;
            case 'delete':
                $sBackupfile=$sBackuppath.'/'.$sFile;
                if (file_exists($sBackupfile)){
                    if (unlink($sBackupfile)){
                        addMsg('ok', '{{msgok.tools_file_deleted}}');
                    } else {
                        addMsg('error', '{{msgerr.tools_file_not_deleted}}');
                    }
                } else {
                    addMsg('error', '{{msgerr.tools_file_not_deleted_not_found}}');
                }
                break;
            case 'view':


                $sBackupfile=$sBackuppath.'/'.$sFile;
                /*

                // does not work:
                header("Content-Type: text/json");
                header('Content-Disposition: attachment; filename="'.$sFile.'"');
                readfile($sBackupfile);
                die();
                */

                $sOutAction.= $renderAdminLTE->getButton([
                    'type' => 'dark',
                    'text' => icon::get('back') . '{{back}}',
                    'onclick' => 'location.reload();',
                ]).'<br><hr>'
                    . "<pre>".htmlentities(file_get_contents($sBackupfile))."</pre>";
                break;
            default:
                addMsg('error', '{{msgerr.action_not_implemented}}: ['.$sAction.']');
        }
    }

    // ----------------------------------------------------------------------
    // APP HOME :: Show backups of an app
    // ----------------------------------------------------------------------

    $TITLE='<strong>'.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</strong> :: {{tools}}' ;
    
    $s=''
        // 
        . ($appmeta->getApphint() && $appmeta->getApphint() 
            ? $renderAdminLTE->getCallout(array (
                'type' => 'info',
                'text' => $appmeta->getApphint().' -> <strong>{{tools}}</strong>',
              )).'<br>'
            : '')

    ;

    $sTable = '';
    foreach (glob($sBackuppath."/*json") as $file) {
        $sTable .= '<tr>'
        . '<td>' . basename($file) . '</td>'
        . '<td>' . date('Y-m-d H:i:s', filemtime($file)) . '</td>'
        . '<td align="right">' . number_format( filesize($file), false, false,"'") . '</td>'
        . '<td align="right">' 
                . $renderAdminLTE->getButton([
                    'type' => 'dark',
                    'text' => icon::get('view') . '{{view}}',
                    'title' => '{{view}}: '.$file,
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'view\', \'file\': \''.basename($file).'\'});',
                ])
                .' '
                . $renderAdminLTE->getButton([
                    'type' => 'dark',
                    'text' => icon::get('restore') . '{{restore}}',
                    'title' => '{{restore}}: '.$file,
                    'onclick' => 'if(confirm(\'{{confirm_restore}}\n\n'.$file.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'restore\', \'file\': \''.basename($file).'\'});',
                ])
                .' '
                .$renderAdminLTE->getButton([
                    'type' => 'danger',
                    'text' => icon::get('delete') . '{{delete}}',
                    'title' => '{{delete}}: '.basename($file),
                    'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$file.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'delete\', \'file\': \''.basename($file).'\'});',
                ])

            . '</td>' . "\n";

        $sTable .= '</tr>' . "\n";
    }

    $sTable = $sTable 
        ? '<table class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
            <tr><th>{{backup.file}}</th><th>{{backup.timestamp}}</th><th>{{backup.size}}</th><th>{{tdactions}}</th></tr>
        </thead>
        <tbody>' . "\n" 
        . $sTable 
        . '</tbody></table>' . "\n\n" 
        : '';

    $s .= ''
        . ( $sOutAction ? '<pre>'.$sOutAction.'</pre>' : '')
        .''
        .$renderAdminLTE->getCard([
        'type' => 'info',
        'variant' => 'outline',
        // 'tb-remove' => 1,
        // 'tb-collapse' => 1,
        'title' => '{{tools.manage_backups}}',
        'tools' => '',
        'text' => ''
            // backup button:
            . $renderAdminLTE->getButton([
                    'type' => 'success',
                    'text' => icon::get('backup').'{{backup}}',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'backup\'})',
                ]) . '<br><br>'
                
            . $sTable
        // 'footer' => '&copy; Axel',
    ]);


    $sContextbar = $renderAdminLTE->getCallout([
        'type' => 'primary',
        'title' => icon::get('more').'{{more}}',
        'text' => '<h3>TODO</h3>'
            . $renderAdminLTE->getButton([
                'type' => 'success',
                'text' => icon::get('optimize').'{{optimize}}',
                'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'optimize\'})',
            ]) . '<br><br>'


    ]);
}


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg().$s, 
        ($sContextbar ? 10 : 12)
        )
    . ($sContextbar 
        ? $renderAdminLTE->addCol($sContextbar,2)
        : ''
    )
);
