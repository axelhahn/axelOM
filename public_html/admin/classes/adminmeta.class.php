<?php
/**
 * ______________________________________________________________________
 * 
 * 
 *                             __    __    _______  _______ 
 *       .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 *       |  _  ||_   _||  -__||  |   __   |   -   ||       |
 *       |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                             \\\_____ axels OBJECT MANAGER
 * 
 * ______________________________________________________________________
 *
 * ADMINMETA
 * 
 * Handle a list of applications before initializing an app.
 * 
 * @author Axel Hahn
 * @link https://github.com/axelhahn/axelOM/
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0

 * ----------------------------------------------------------------------
 * 2024-05-18        <axel>  add variable types
 * ======================================================================
 */

class adminmetainfos {

    /**
     * Root directory of all apps
     * @var string
     */
    protected $_sAppsRootDir = false;

    /**
     * Array of all apps
     * @var array
     */
    protected $_aAppObjects = [];

    // --------------------------------------------------------------
    // METHODS
    // --------------------------------------------------------------

    /**
     * Constructor
     * 
     * @param string $sAppsRootDir    root directory of all apps to set
     * @return boolean
     */
    public function __construct($sAppsRootDir=false) {
        $this->_sAppsRootDir = ($sAppsRootDir && is_dir($sAppsRootDir))
            ? $sAppsRootDir
            : __DIR__."/../../apps"
        ;
        return true;
    }


    /**
     * Get to root directory for all apps
     * 
     * @return string
     */
    public function getAppRootDir(){
        return $this->_sAppsRootDir;
    }

    /**
     * Get all available apps by scanning the approot dir and searching for a
     * a file named [appname]/config/objects.php
     * 
     * @param bool $bScanConfigs
     * @return array
     */
    public function getApps($bScanConfigs=false) {
        $aReturn=[];
        foreach(glob($this->_sAppsRootDir."/*") as $sFileEntry){
            if (is_dir($sFileEntry) && file_exists($sFileEntry.'/config/objects.php')){
                $sAppName=basename($sFileEntry);
                $aReturn[$sAppName]=$bScanConfigs ? include($sFileEntry.'/config/objects.php') : 1;
            };
        }
    
        $this->_aAppObjects=$aReturn;
        return $aReturn;
    }

}