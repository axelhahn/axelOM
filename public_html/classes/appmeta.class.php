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
 * APPMETA CLASS
 * handle metadata of a single application
 * 
 * @author Axel Hahn
 * @link https://github.com/axelhahn/axelOM/
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * 
 * ----------------------------------------------------------------------
 * 2024-nn-mm  v0.x  <axel>  initial release
 * 2024-05-18        <axel>  add variable types
 * ======================================================================
 */
class appmetainfos {

    /**
     * full path of application directory
     * @var string
     */
    protected string $_sAppBasedir = '';

    /**
     * application name (foldername)
     */
    protected string $_sApp = '';

    /**
     * application config
     * @var array
     */
    protected array $_aConfig = [];

    /**
     * Last error message
     * @var string
     */
    protected string $_error='';

    /**
     * List of required directories in the application folder
     * @var array
     */
    protected array $_aDirs = [
        'classes',
        'config',
        'files'
    ];

    /**
     * List of allowed relations between objects
     * @var array
     */
    protected array $_aRelations = [];



    // --------------------------------------------------------------

    /**
     * Constructor
     * @param mixed $aAppdir  full path of application directory or app name
     * @return boolean
     */
    public function __construct(string $sAppdir='')
    {
        if($sAppdir){
            $this->set($sAppdir);
        }
        // return true;
    }

    // --------------------------------------------------------------
    // SETTER
    // --------------------------------------------------------------

    /**
     * Set application directory; if the directory is valid, the method 
     * returns true and sets the internal variable $_sAppBasedir
     * 
     * @param string $sAppdir  full path of application directory
     * @return boolean
     */
    public function set(string $sAppdir='') :bool {
        $this->_sApp = '';
        $this->_aConfig = [];
        if($sAppdir && is_dir($sAppdir) && $this->getConfigfile($sAppdir)>""){
            $this->_sAppBasedir = dirname($sAppdir);
            $this->_sApp = basename($sAppdir);
            $this->_aConfig = include($this->getConfigfile());
            return true;
        } else {
            $this->_error=__METHOD__ .' ERROR: '.$sAppdir.' is not a valid app directory';
            return false;
        }
    }

    /**
     * Set application basedir - the parent directory of al apps
     * @param  string  $sBasedir  full path of application directory
     * @return bool
     */
    public function setAppBasedir(string $sBasedir) :bool {
        $this->_sAppBasedir = '';
        $this->_sApp = '';
        $this->_aConfig = [];
        if (is_dir($sBasedir)){
            $this->_sAppBasedir = dirname($sBasedir);
            return true;
        }
        return false;
    }

    // --------------------------------------------------------------
    // CREATE
    // --------------------------------------------------------------

    /**
     * Create a new application by creating basic folders and a configuration 
     * file
     * 
     * @param string $sNewAppdir  full path of application directory or app name
     * @param array  $aConfig     config for the new application with subkeys
     *                            - label
     *                            - icon
     *                            - TODO: db (see PDO constructor parameters)
     *                            - TODO: objects
     * @return boolean
     */
    public function create(string $sNewAppdir, array $aConfig=[]) :bool {
        if($sNewAppdir &&!is_dir($sNewAppdir)){
            $sNewApp=basename($sNewAppdir);
            mkdir($sNewAppdir);
            foreach($this->_aDirs as $sDir){
                mkdir($sNewAppdir.'/'.$sDir);
            }

            file_put_contents("$sNewAppdir/config/objects.php", "<?php 
            return [
                'label' => '".($aConfig['label'] ?? $sNewApp)."',
                'icon' => '".($aConfig['icon'] ?? 'fa fa-puzzle-piece')."',
                'hint' => '".($aConfig['hint'] ?? '')."',
                'db' => [
                    // see https://www.php.net/manual/de/pdo.construct.php
                    /*

                    // default value is
                    'dsn' => 'sqlite:'.__DIR__.'/../../../protected/data/app_$sNewApp.sqlite3',


                    'dsn' => 'mysql:host=localhost;dbname=$sNewApp;charset=utf8',
                    'user' => '$sNewApp',
                    'password' => 'mypassword',
                    'options' => []
                    */
                ],
                'objects' => [
                    // 'objproducts'               => ['label' => 'Products',              'icon' => 'fa fa-puzzle-piece'],
                    // 'objprogramminglanguages'   => ['label' => 'Programming languages', 'icon' => 'fa fa-puzzle-piece'],
                ]

            ];
            ");

            $this->set($sNewAppdir);
            return true;
        } else {
            $this->_error=__METHOD__ .' ERROR: '.$sNewAppdir.' is not a valid app directory';
            return false;
        }
    }
    // --------------------------------------------------------------
    // GETTER for application infos
    // --------------------------------------------------------------

    /**
     * Get last error
     * @return string
     */
    public function error() :string {
        return $this->_error;
    }

    /**
     * Get full path of application config file
     * @param string $sAppdir  full path of application directory
     * @return string|bool full path of application config file or false if not found
     */
    public function getConfigfile(string $sAppdir='') :string|bool {
        $sMyDir=$sAppdir ? $sAppdir : $this->_sAppBasedir.'/'.$this->_sApp;
        return $sMyDir
            ? (
                file_exists($sMyDir.'/config/objects.php')
                    ? $sMyDir.'/config/objects.php'
                    : false
            )
            : false
        ;
    }

    /**
     * Get full config or a given entry
     * @param string $sKey  optional: config key; if not set, the whole config is returned
     * @return mixed
     */
    public function getConfig(string $sKey='') :mixed {
        if(!$sKey){
            return $this->_aConfig;
        }
        if(!isset($this->_aConfig[$sKey])){
            return false;
        }
        return $this->_aConfig[$sKey];
    }

    /**
     * Get current application id (foldername of application)
     * @return string
     */
    public function getId() :string {
        return $this->_sApp;
    }

    /**
     * Get full path of application directory.
     * @return string
     */
    public function getAppdir() :string {
        return $this->_sAppBasedir.'/'.$this->_sApp;
    }

    /**
     * Get config value for application icon
     * see config value 'icon'
     * @return string
     */
    public function getAppicon() :string {
        return $this->getConfig('icon');
    }

    /**
     * Get config value for application label
     * see config value 'label'
     * @return string
     */
    public function getAppname() :string {
        return $this->getConfig('label');
    }

    /**
     * Get config value for application hint
     * see config value 'hint'
     * @return string
     */
    public function getApphint() :string {
        return $this->getConfig('hint') ?: '{{msgerr.app_hint_missing}}';
    }

    // --------------------------------------------------------------
    // GETTER for application objects
    // --------------------------------------------------------------

    /**
     * Get a hash of a single object (database table) of the current app
     * by a given id (hint: read array_keys($this->getObjects()))
     * 
     * @see getObjects()
     * @param  string  id of the object
     * @return array
     */
    public function getObject(string $sObjId) :array {
        return isset($this->_aConfig['objects'][$sObjId])
            ? $this->_aConfig['objects'][$sObjId]
            : []
        ;
    }

    /**
     * Get a hash of all objects (database tables) of the current app 
     * see config value 'objects'
     * 
     * @return array
     */
    public function getObjects() :array {
        return $this->getConfig('objects')?:[];
    }

    /**
     * Get label of a single object (database table) of the current app
     * If the object is not configured, the given id is returned
     * 
     * @param string  $sObjId  id of the object
     * @return string
     */
    public function getObjectLabel(string $sObjId) :string {
        return isset($this->_aConfig['objects'][$sObjId]['label'])
            ? $this->_aConfig['objects'][$sObjId]['label']
            : $sObjId
        ;
    }

    /**
     * Get icon of a single object (database table) of the current app
     * If the object is not configured, then 'fa-solid fa-cube' is returned
     * 
     * @param string  $sObjId  id of the object
     * @return string
     */
    public function getObjectIcon(string $sObjId) :string {
        return isset($this->_aConfig['objects'][$sObjId]['icon'])
            ? $this->_aConfig['objects'][$sObjId]['icon']
            : 'fa-solid fa-cube'
        ;
    }

    /**
     * Get hint text for an object
     * 
     * @param string  $sObjId  id of the object
     * @return string
     */
    public function getObjectHint(string $sObjId) :string {
        return isset($this->_aConfig['objects'][$sObjId]['hint'])
            ? $this->_aConfig['objects'][$sObjId]['hint']
            : ''
        ;
    }

    /**
     * Get an array of all allowed relation between objects
     * see config value 'relations'
     * 
     * @return array
     */
    public function getAllowedRelations(string $sObject) :array {
        if($this->_aRelations[$sObject]??false){
            return $this->_aRelations[$sObject];
        }
        $aReturn=[];
        foreach($this->getConfig('relations')?:[] as $aRelation){
            if(array_search($sObject, $aRelation)!==false){
                $relObj=$aRelation[0]===$sObject?$aRelation[1]:$aRelation[0];
                $aReturn[]=$relObj;
            }
        }
        array_unique($aReturn);
        sort($aReturn);
        $this->_aRelations[$sObject]=$aReturn;
        return $aReturn;
    }

    /**
     * Return bool if 2 given objects can have a relation.
     * It returns true if no key 'relations' was set or a relation between 2 objects was defined.
     * 
     * @param string $sObject1
     * @param string $sObject2
     * @return bool
     */
    public function isRelationAllowed(string $sObject1, string $sObject2) :bool {
        if(!count($this->getConfig('relations')?:[])){
            return true;
        }
        $aAllowed=$this->getAllowedRelations($sObject1);
        return in_array($sObject2, $aAllowed);
    }

    // --------------------------------------------------------------
    // GETTER for application database
    // --------------------------------------------------------------

    /**
     * Get a hash of database settings of the current app.
     * If non is configured it returns a sqlite database file
     * 
     * @return array
     */
    public function getDbsettings() :array {
        return (isset($this->_aConfig['db']) && isset($this->_aConfig['db']['dsn']))
            ? $this->_aConfig['db']
            : ['dsn' => 'sqlite:'.__DIR__.'/../protected/data/app_'.$this->getId().'.sqlite3']
        ;
    }

}