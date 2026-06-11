#!/bin/php
<?php
/*
*
*                        __    __    _______  _______ 
* .---.-..--.--..-----.|  |  |__|  |       ||   |   |
* |  _  ||_   _||  -__||  |   __   |   -   ||       |
* |___._||__.__||_____||__|  |__|  |_______||__|_|__|
*                       \\\_____ axels OBJECT MANAGER
* 
* CLI tool :: BACKUP DB :: Backup databases of all apps
* 
*/

require_once __DIR__ . '/../vendor/php-abstract-dbo/src/pdo-db.class.php';
require_once __DIR__ . '/../admin/classes/adminmeta.class.php';
require_once __DIR__ . '/../classes/appmeta.class.php';

$sVersion="0.1";
$adminmetainfos=new adminmetainfos();
$sAppRootDir=$adminmetainfos->getAppRootDir();

$sUsage="This tool backups the database of all apps.
You should run it in a daily cronjob.

USAGE: 
  php backup-db.php [options]

OPTIONS:
  -h          Show this help message
  --keep=N    Keep only the last N backup files for each app (default: 0, keep all)

EXAMPLES:
  php backup-db.php            # start a database backup for each app
  php backup-db.php --keep=5   # Keep only the last 5 backup files for each app

";

// ---------------------------------------------------------------------- 
// INIT
// ---------------------------------------------------------------------- 

if($argc>1) {
  parse_str(implode('&',array_slice($argv, 1)), $_GET);
}

$iMaxBackups=(int) ($_GET['--keep']??0);

// ---------------------------------------------------------------------- 
// MAIN
// ---------------------------------------------------------------------- 


echo "\n"
    ."---===###|  axelOM :: Backup DB :: v$sVersion  |###===---\n"
    ."\n"
    ;

// print_r($_GET);

if(isset($_GET['-h'])){
    echo "$sUsage";
    exit(0);
}

// ---------------------------------------------------------------------- 
// start backups
// ---------------------------------------------------------------------- 

echo "   Found " . count($adminmetainfos->getApps()) . " apps\n";

foreach(array_keys($adminmetainfos->getApps()) as $sAppname){

    echo "\n--- $sAppname \n";
    $appmeta=new appmetainfos($sAppRootDir.'/'.$sAppname);
    
    $oDB = new axelhahn\pdo_db([
        'db'=>$appmeta->getDbsettings(),
        // 'showdebug'=>true,
        'showerrors' => true,
    ]);

    if (!$oDB || !$oDB->driver()) {
        echo "\n";
        echo "ERROR: Unable to initialize database.\n";
        echo $oDB->lasterror();
        exit(1);
    }

    $sBackupdir=realpath($appmeta->getAppdir())."/data";
    if(!is_dir($sBackupdir)){
        echo "Creating backup dir '$sBackupdir'...\n";
        mkdir($sBackupdir);
    }
    
    $sBackupBase="$sBackupdir/$sAppname-";
    $sBackupfile=$sBackupBase.(string) $oDB->driver()."-".date('U').".jsonlines";
    $aTables=[];

    $iStartBackup=microtime(true);
    if($oDB->dump($sBackupfile, $aTables)){
        $aOptions=$oDB->dumpAnalyzer($sBackupfile);
        if($aOptions['counters']['rows']==0){
            echo "ERROR: $sAppname no datasets were written during backup.";
            unlink($sBackupfile);
        } else {
            $iTimeBackup=microtime(true)-$iStartBackup;
            echo "OK: $sAppname - backup was written in "
                .(round(($iTimeBackup)*10000)/10)." ms - "
                .number_format(filesize($sBackupfile)/$iTimeBackup, false, false, "'")." B/ sec"
                ." - $sBackupfile"
                ."\n"
                // . print_r($aOptions, true)
                ;
        }
    } else{
        exit(1);
    }

    // if(file_exists($sBackupfile)){
    //     echo "WIP: deleting backup file '$sBackupfile'...\n";
    //     unlink($sBackupfile);
    // }

    $aBackupFiles=[];
    foreach(glob($sBackupBase."*.jsonlines") as $sFile){
        $aBackupFiles[filemtime($sFile)]=$sFile;
    }


    if(count($aBackupFiles)>0 && $iMaxBackups>0){
        krsort($aBackupFiles);
        echo "Cleanup - Found " . count($aBackupFiles) . " backup files for $sAppname - Keeping last $iMaxBackups backups\n";
        foreach(array_slice($aBackupFiles, $iMaxBackups) as $sFile){
            echo "Deleting backup file '$sFile'...\n";
            unlink($sFile);
        }
    }
}

echo "\nDone.\n\n";

// ---------------------------------------------------------------------- 
