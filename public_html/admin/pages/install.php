<?php
/**
 * 
 * INSTALLER
 * 
 * It is opened if the main config file does not exist.
 * It asks for the backend language and generates the config file in step 2.
 * 
 */

if(file_exists('config/settings.php')) {
    header('Location: ?page=home');
}

$TITLE='{{install.title}}';
$BANNER='{{install.banner}}';

$iStep=(int)($_GET['step']??1);
$isDev=(int)($_GET['dev']??0);

$BODY="";
switch($iStep) {

    // select language
    case 1:
        $BODY="";
        foreach(glob(__DIR__.'/../lang/*.php') as $sLangfile) {
            $sLang=substr($sLangfile, strrpos($sLangfile, '/')+1, -4);
            $sClass=$sLang==$sUiLang ? 'btn-primary' : '';
            $BODY.="<a href=\"?page=install&step=1&lang=$sLang\" class=\"btn $sClass\">$sLang</a><br>";
        }
        $BODY=$renderAdminLTE->getCard([
            'type' => '',
            'title' => '{{install.select-lang}}',
            'text' => $BODY,
        ]);
        $BODY.="<br><a href=\"?page=install&step=2&lang=$sUiLang\" class=\"btn btn-primary\">{{continue}}</a>";
        break;

    // generate config
    case 2:
        if(file_exists('config/settings.php.dist')) {
            if(!file_exists('config/settings.php')) {
                $sCfg=file_get_contents('config/settings.php.dist');
                $sCfg=str_replace("'lang' => 'en-en',", "'lang' => '$sUiLang',", $sCfg);
                file_put_contents('config/settings.php', $sCfg);
            }
        }
        header('Location: ?page=home');
        break;
}
