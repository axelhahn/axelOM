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
 * ADMINACL
 * 
 * Add ACL permission to apps and objects
 * 
 * @author Axel Hahn
 * @link https://github.com/axelhahn/axelOM/
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0

 * ----------------------------------------------------------------------
 * 2024-06-12        <axel>  first lines
 * ======================================================================
 */

class adminacl
{

    /**
     * current userid
     * @var string
     */
    protected string $_sUser = '';

    /**
     * Human readable Username
     * @var string
     */
    protected string $_sUserDisplayname = '';
    /**
     * Detected groups
     * @var array
     */
    protected array $_aGroups = [];

    /**
     * configuration data from config/settings.php
     * @var array
     */
    protected array $_aConfig = [];

    /**
     * optional: current application to shorten acl check functions
     * @var string
     */
    protected string $_sApp = '';

    // --------------------------------------------------------------
    // METHODS
    // --------------------------------------------------------------

    /**
     * Constructor
     * 
     * @param string $sAppsRootDir    root directory of all apps to set
     * @return boolean
     */
    public function __construct()
    {

        // read config
        $aCfg = include (__DIR__ . '/../config/settings.php');
        $this->_aConfig = $aCfg['acl'] ?? [];

        $this->_detectUser();
        // echo "detected user: " . $this->getUser(); echo "<pre><hr>config:"; print_r($this->_aConfig); 
        // echo 'Groups:'; print_r($this->_aGroups);
        // die();
    }


    /**
     * Detect a user from $_SERVER environment. Which field to read is in key acl->userfield
     * and then set its groups
     * @return bool
     */
    protected function _detectUser(): bool
    {
        $this->_sUser = 'nobody';
        $this->_aGroups = ['@anonymous'];
        $this->_sUserDisplayname = '';

        // print_r($_SERVER);
        if (!count($this->_aConfig)) {
            $this->_sUser = 'superuser';
            $this->_aGroups = ['admin'];
            return true;
        }

        if (isset($this->_aConfig['userfield']) && isset($_SERVER[$this->_aConfig['userfield']])) {
            $this->_sUser = $_SERVER[$this->_aConfig['userfield']];
            if (isset($this->_aConfig['displayname']) && is_array($this->_aConfig['displayname'])) {
                foreach ($this->_aConfig['displayname'] as $sKey) {
                    $this->_sUserDisplayname .= $this->_sUserDisplayname ? ' ' : '';
                    $this->_sUserDisplayname .= isset($_SERVER[$sKey]) ? $_SERVER[$sKey] : '';
                }
            }
            $this->_detectGroups();
            return true;
        }

        return false;
    }

    /**
     * Detect groups based on user and config
     * @return bool
     */
    protected function _detectGroups(): bool
    {

        $this->_aGroups = ['@authenticated'];
        if (isset($this->_aConfig['groups']) && count($this->_aConfig['groups'])) {
            foreach ($this->_aConfig['groups'] as $sApp => $groups) {
                foreach ($groups as $sGroup => $members) {
                    if (array_search($this->_sUser, $members) !== false) {
                        if ($sApp !== 'global' && array_search($sApp, $this->_aGroups) === false) {
                            $this->_aGroups[] = $sApp;
                        }

                        $this->_aGroups[] = preg_replace('/^global_/', '', $sApp . '_' . $sGroup);

                    }
                }
            }
        }
        return true;
    }

    // ----------------------------------------------------------------------
    // SETTER
    // ----------------------------------------------------------------------

    /**
     * Simulate another login. This will set the given user.
     * This feature is usable for global admins only
     * @param string $sUser  new username
     * @return bool
     */
    function setUser(string $sUser): bool
    {
        if ($this->isAdmin()) {
            $this->_sUser = $sUser;
            $this->_detectGroups();
            return true;
        }
        return false;
    }

    /**
     * Simulate another login. This will set the given user.
     * This feature is usable for global admins only
     * @param string $sApp  appname to set
     * @return bool
     */
    public function setApp(string $sApp): bool
    {
        $this->_sApp = $sApp;
        return true;
    }

    // ----------------------------------------------------------------------
    // GET INFOS
    // ----------------------------------------------------------------------

    /**
     * return current userid
     * @return string
     */
    public function getUser(): string
    {
        return $this->_sUser;
    }

    /**
     * return human readable username
     * @return string
     */
    public function getUserDisplayname(): string
    {
        return $this->_sUserDisplayname ? $this->_sUserDisplayname : $this->_sUser;
    }

    /**
     * get a list of current groups
     * @return array
     */
    public function getGroups(): array
    {
        return $this->_aGroups;
    }

    // ----------------------------------------------------------------------
    // GET PERMISSIONS
    // ----------------------------------------------------------------------

    /**
     * check if the user is member of a given group - or global admin
     * @param string $sWantedGroup  name of group to check
     * @return bool
     */
    public function is(string $sWantedGroup): bool
    {
        return array_search('admin', $this->_aGroups) !== false || array_search($sWantedGroup, $this->_aGroups) !== false;
    }

    /**
     * return boolean if user is global admin
     * @return bool
     */
    public function isAdmin(): bool
    {
        return array_search('admin', $this->_aGroups) !== false;
    }

    /**
     * return boolean if user is global admin
     * @param string  $sAppname  appname to check
     * @return bool
     */
    public function isAppAdmin(string $sAppname = ''): bool
    {
        $sAppname = $sAppname ? $sAppname : $this->_sApp;
        return $this->isAdmin()
            || array_search($sAppname . '_admin', $this->_aGroups) !== false
        ;
    }

    public function canEdit(string $sAppname = ''): bool
    {
        $sAppname = $sAppname ? $sAppname : $this->_sApp;
        return $this->isAppAdmin($sAppname)
            || array_search($sAppname . '_manager', $this->_aGroups) !== false
        ;
    }

    public function canView(string $sAppname = ''): bool
    {
        $sAppname = $sAppname ? $sAppname : $this->_sApp;
        return $this->canEdit($sAppname)
            || array_search($sAppname, $this->_aGroups) !== false
        ;
    }
}
