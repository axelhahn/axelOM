<?php
/*
                       __    __    _______  _______ 
.---.-..--.--..-----.|  |  |__|  |       ||   |   |
|  _  ||_   _||  -__||  |   __   |   -   ||       |
|___._||__.__||_____||__|  |__|  |_______||__|_|__|
                      \\\_____ axels OBJECT MANAGER


*/

$iTimerStart=microtime(true);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

const APP_NAME='axel :: OM';
const APP_VERSION='0.0.36';
const DELIM_TITLE='<span></span>';

require_once('../classes/render-adminlte4.class.php');
require_once('classes/adminmeta.class.php');
require_once('classes/adminacl.class.php');
require_once('../classes/appmeta.class.php');
require_once('../classes/queryparam.class.php');
require_once('../classes/icon.class.php');


$sPage=queryparam::get('page', '/^[a-z]*$/');
$sObject=queryparam::get('object', '/^[a-z\_]*$/');

$sTabApp=queryparam::get('app', '/^[a-z\-0-9]*$/');
if(!file_exists('config/settings.php')){
    $sPage='install';
} else {
    $aSettings=include('config/settings.php');
}
$sUiLang=$aSettings['lang'] ?? "en-en";
$sUiLang=queryparam::get('lang', '/^[a-z\-]*$/')
    ?: ($aSettings['lang'] ?? "en-en");

if(!$sTabApp && !$sPage){
    // $sTabApp=array_key_first($aObjects);
    header('location: ?page=home');
    return true;
}


$adminmetainfos=new adminmetainfos();
$appmetainfos=new appmetainfos();
$acl=new adminacl();

$renderAdminLTE=new renderadminlte();
$aReplace=include("./config/page_replacements.php");

// $aProjects=include("./config/objects.php");


// $aTopnav=$adminmetainfos->getTopNavArray();
$aTopnav=[];
$aTopnav[]=['label'=> '='];
$aTopnav[]=['label'=> '{{nav.apps}}:'];
$sAppRootDir=$adminmetainfos->getAppRootDir();

$aSidebarNav=[];
$aTopnavRight=[];
$aObjects=[];

$bAppFound=false;
foreach(array_keys($adminmetainfos->getApps(1)) as $sApp){
    $appmeta=new appmetainfos($sAppRootDir.'/'.$sApp);
    if ($acl->canView($sApp)){
        if($sApp==$sTabApp){
            // tab is active
            $bAppFound=true;
            $aSidebarNav[]=['href'=>'?page=home', 'label'=>'{{back}}', 'icon'=>icon::getclass('back')];

            $aSidebarNav[]=['href'=>'?app='.$sApp.'&page=home',          'label'=>'{{nav.home}} :: ' . $appmeta->getAppname(), 'icon'=>$appmeta->getAppicon() ? $appmeta->getAppicon() : icon::getclass('home')];
            if(count($appmeta->getObjects())){
                $aSidebarNav[]=['href'=>'#', 'label'=>'-'];
            }
            foreach ($appmeta->getObjects() as $sMyObject=>$aMyObject){
                $aSidebarNav[]=[
                    'href'=>'?app='.$sTabApp.'&page=object&object='.$sMyObject, 
                    'label'=>$appmeta->getObjectLabel($sMyObject), 
                    'icon'=> $appmeta->getObjectIcon($sMyObject),
                    'title'=> $appmeta->getObjectHint($sMyObject),
                ];
            }
            if(count($appmeta->getObjects()) && $acl->canEdit($sTabApp)){
                $aSidebarNav[]=['href'=>'#', 'label'=>'-'];
                $aSidebarNav[]=['href'=>'?app='.$sTabApp.'&page=object&object=pdo_db_attachments',          'label'=>'{{files}}', 'icon'=>icon::getclass('file')];
            }
            if ($acl->isAppAdmin($sApp) ){
                $aSidebarNav[]=['href'=>'#', 'label'=>'-'];
                $aSidebarNav[]=['href'=>'?app='.$sTabApp.'&page=tools',  'label'=>'{{tools}}',      'icon'=>icon::getclass('tools')];
                $aSidebarNav[]=['href'=>'?app='.$sTabApp.'&page=config', 'label'=>'{{nav.config}}', 'icon'=> icon::getclass('config')];
        
            }

            $aTopnav[]=['href'=>'?app='.$sApp.'&page=home', 'label'=>$appmeta->getAppname(), 'icon'=>$appmeta->getAppicon() ?? icon::getclass('app'), 'class'=>'active' ];
        } else {
            $aTopnav[]=['href'=>'?app='.$sApp.'&page=home', 'label'=>$appmeta->getAppname(), 'icon'=>$appmeta->getAppicon() ?? icon::getclass('app')];
        }
    }
}

// no app is active? --> get sidebar nav for general backend
if(!count($aSidebarNav)){
    $aSidebarNav[]=['href'=>'?page=home', 'label'=>'{{nav.home}}', 'icon'=>icon::getclass('home')];
    if($acl->isGlobalAdmin()){
        $aSidebarNav[]=[
            'href'=>'?page=adminusers', 
            'label'=>'{{nav.users}}', 
            'icon'=> icon::getclass('users'),
            'title'=> '',
        ];
        $aSidebarNav[]=[
            'href'=>'?page=adminconfig', 
            'label'=>'{{nav.config}}', 
            'icon'=> icon::getclass('config'),
            'title'=> '',
        ];
        /*
        $aSidebarNav[]=[
            'href'=>'?app=backend&page=adminconfig', 
            'label'=>'{{nav.config}}', 
            'icon'=> icon::getclass('config'),
            'title'=> '',
        ];
        */    
    }
    $aSidebarNav[]=['href'=>'?page=adminabout', 'label'=>'{{nav.about}}', 'icon'=>icon::getclass('about')];
}

// add search field
if($bAppFound){
    $aTopnavRight[]=['label'=>'<input class="form-control form-control-sidebar fix-nolabel" type="search" id="searchtop" placeholder="🔎 {{search.placeholder}}" aria-label="Search">',];
}
$aTopnavRight[]=[
    'href'    => ($sPage=='userprofile' ? '#' : '?page=userprofile'),
    'icon'    => icon::getclass('user'), 
    'label'   => $acl->getUserDisplayname(), 

    'class'   => ($sPage=='userprofile' ? 'active' : ''),
    'onclick' => ($sPage=='userprofile' ? 'history.back();' : ''),
    'title'   => ($sPage=='userprofile'  ? '{{back}}' : $acl->getUser()."\n- ".implode("\n- ",$acl->getGroups()) ), 
];

// highlight menu item
for($i=0; $i<count($aSidebarNav); $i++){
    if (
        $aSidebarNav[$i]['href']==$_SERVER['REQUEST_URI']
        || $aSidebarNav[$i]['href']=='?'.$_SERVER['QUERY_STRING']

        // activate nav item when editing an object
        || ($sTabApp && $sObject && preg_match('/\?app='.$sTabApp.'&page=object.*&object='.$sObject.'/', $aSidebarNav[$i]['href']) )
    ){
        $aSidebarNav[$i]['class']='active';
    }
}

$appmeta=new appmetainfos($sAppRootDir.'/'.$sTabApp);

// show page for missing app
if($sTabApp && !$bAppFound){
    $sPage="error404-wrong-app";
} else if($sTabApp && ($_GET['object']??false) && !($sObject??false) ){
    $sPage="error404-wrong-obj";
}

$aReplace['{{NAVI_TOP}}']=''
    . $renderAdminLTE->addWrapper(
    'nav', ['class'=>'app-header navbar navbar-expand bg-body'],
    $renderAdminLTE->addWrapper('div', [ 'class' => 'container-fluid' ],
        $renderAdminLTE->getTopNavigation($aTopnav, [], $aTopnavRight)
    )
    // add 2nd navbar if needed
    );

$aReplace['{{NAVI_LEFT}}']=''
    . $renderAdminLTE->addWrapper(
        'nav', ['class'=>'mt-2'],
        $renderAdminLTE->getSidebarNavigation($aSidebarNav)
    );

// ---------- include page content
$sIncfile='pages/'.$sPage.'.php';
if(!file_exists($sIncfile)){
    $sIncfile='pages/error404.php';
}
$BODY="WARNING: $sIncfile did not set \$BODY";
$TITLE="WARNING: $sIncfile did not set \$TITLE";
$BANNER="WARNING: $sIncfile did not set \$BANNER";
$JS_BODYEND="";

include($sIncfile);


// ---------- generate output
// die($appmeta->getAppname());
/*
$sBcSpacer='&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;';
$sBreadcrumb='<a href="?page=home">{{nav.home}}</a>'.$sBcSpacer
    .($sTabApp ? '<a href="?page=home&app='.$sTabApp.'">'.$appmeta->getAppname().'</a>'.$sBcSpacer : '')
    .($sObject ? '<a href="?page=object&app='.$sTabApp.'&object='.$sObject.'">'.$appmeta->getObjectLabel($sObject).'</a>'.$sBcSpacer : '')
    ;
*/

// ---------- add debug info


    if ($aSettings['debug']??false) {
        $sDEBUG='';
        $sDB_DEBUG='';
        $sDB_LOG='';
        if (isset($oDB) && !is_null($oDB)){

            // dump test
            // $oDB->setDebug(1); echo '<pre>DUMP: '; print_r($oDB->dump()); die();

            $iQueries=0;
            foreach($oDB->queries() as $aQuery){
                $iQueries++;
                $bIsError=isset($aQuery['error'])   && $aQuery['error'];
                $sDB_DEBUG.='<tr'.($bIsError ? ' class="error"' : '').'>
                <td>'.(isset($aQuery['time'])    && $aQuery['time']    ? htmlentities($aQuery['time'])    : '-').'</td>
                <td>'.(isset($aQuery['records']) && $aQuery['records'] ? htmlentities($aQuery['records']) : '-').'</td>
                <td>
                    <code>'.htmlentities($aQuery['sql']).'</code>
                    '.($bIsError 
                        ? '<br><br><strong>{{debug.error}}</strong><br>'
                            . htmlentities($aQuery['error']).'<br>'
                            .(isset($aQuery['data']) 
                                ? '<pre>data = '.htmlentities(print_r($aQuery['data'], 1)).'</pre>' 
                                : ''
                            )
                        : '').'</td>
                </tr>';
            }
            /*
            Array
            (
                [loglevel] => info
                [table] => objproducts
                [method] => axelhahn\pdo_db_base::read
                [message] => read id 1
            )
            */
            foreach($oDB->logs() as $aLogentry){
                $sDB_LOG.='<tr class="'.$aLogentry['loglevel'].'">
                    <td>'.$aLogentry['loglevel'].'</td>
                    <td>'.$aLogentry['table'].'</td>
                    <td>'.$aLogentry['method'].'</td>
                    <td>'.$aLogentry['message'].'</td>
                </tr>';
            }
    
    
        }

        $sDEBUG.=$sDB_LOG
            ? '<hr>
            '.icon::get('log').'{{debug.logs}}: <strong>'.count($oDB->logs()).'</strong>
            <table class="table table-striped">
            <tr>
                <th>{{debug.log_level}}</th>
                <th>{{debug.log_table}}</th>
                <th>{{debug.log_method}}</th>
                <th>{{debug.log_message}}</th>
            </tr>
            '.$sDB_LOG.'</table>'
            :''
            ;

        $sDEBUG.=$sDB_DEBUG ? '<hr>
        '.icon::get('database').'{{debug.queries}}: <strong>'.$iQueries.'</strong>
        <table class="table table-striped">
        <tr>
            <th>{{debug.time_ms}}</th>
            <th>{{debug.records}}</th>
            <th>{{debug.query}}</th>
        </tr>
        '.$sDB_DEBUG.'</table>'
        : ''
        ;


        // $sDB_DEBUG.='<pre>'.print_r($oDB->logs(), 1).'</pre>';


        $BODY.='<br><br><br><br>'.$renderAdminLTE->addRow(
            $renderAdminLTE->addCol(
                $renderAdminLTE->getCard([
                    'type'=>'secondary',
                    'title'=>icon::get('debug').'{{debug.title}}',
                    'text'=>''
                        .icon::get('clock').'<strong>'.(round((microtime(true)-$iTimerStart)*10000)/10).'</strong> ms - {{debug.processing_time}}<br>'
                        .icon::get('memory').'<strong>'.number_format(memory_get_peak_usage()/1024/1024, 2).'</strong> MB - {{debug.max_memory}}'
                        .'<hr>'
                        .'GET <code>'.htmlentities(json_encode($_GET,JSON_PRETTY_PRINT)).'</code><br>'
                        .'POST <code>'.htmlentities(json_encode($_POST,JSON_PRETTY_PRINT)).'</code>'
                        .$sDEBUG
                        ,
                ])
            ,
            12
            ))
            .'<br><br>'
            // .'<div style="clear: both"></div>' 
            ;    
    }


$aReplace['{{PAGE_BODY}}']=$BODY;
$aReplace['{{PAGE_HEADER_LEFT}}']='<h2 class="page-title">'.$TITLE.'</h2>';
// $aReplace['{{PAGE_HEADER_RIGHT}}']=$sBreadcrumb;
$aReplace['{{PAGE_BANNER}}']=$BANNER;
$aReplace['{{JS_BODY_END}}']=$JS_BODYEND ?: '';

// simple translate
foreach([
    __DIR__.'/lang/'.$sUiLang.'.php',
    __DIR__.'/../apps/'.$sTabApp.'/lang/'.$sUiLang.'.php',
] as $sLangfile){

    // $sLangfile=__DIR__.'/lang/'.$sUiLang.'.php';
    if(file_exists($sLangfile)){
        foreach(include($sLangfile) as $sKey=>$sTranlated){
            $aReplace['{{'.$sKey.'}}']=$sTranlated;
        };
    }
}

$sTemplate=file_get_contents('config/page.tpl.php');
echo $renderAdminLTE->render($sTemplate,$aReplace);
