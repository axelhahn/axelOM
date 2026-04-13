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
 * USERSETTINGS
 * 
 * Override a few ui system settings for a user
 * 
 * @author Axel Hahn
 * @link https://github.com/axelhahn/axelOM/
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0

 * ----------------------------------------------------------------------
 * 2026-04-xx        <axel>  initial release
 * ======================================================================
 */
class usersettings {

    /**
     * Current user
     * @var string
     */
    protected string $_sUser;

    /**
     * Configuration file for user specific settings
     * @var string
     */
    protected string $_sCfgFile;

    /**
     * User specific settings
     * @var array
     */
    protected array $_aValues = [];

    /**
     * Default settings of a user config
     * @var array
     */
    protected array $_aDefaultValues = [
        'lang'=>'',
        'iconset'=>'',
        'theme'=>'',
        'debug'=>false,
    ];

    protected bool $_bChanged = false;

    // -----------------------------------------------------------------------------
    // CONSTRUCTOR
    // -----------------------------------------------------------------------------
    
    /**
     * Constructor
     * @param string $sUser    user name to set
     */
    public function __construct(string $sUser='') {
        if($sUser) {
            $this->setUser($sUser);
        }
    }

    // -----------------------------------------------------------------------------
    // GETTERS
    // -----------------------------------------------------------------------------

    /**
     * Get a single config value
     * @param string $sKey
     * @return mixed
     */
    public function get(string $sKey):mixed {
        return $this->_aValues[$sKey]??null;
    }

    /**
     * Get array with all config entries
     * @return array
     */
    public function getConfig():array {
        return $this->_aValues;
    }

    /**
     * Get full path of user config file
     * @return string
     */
    public function getConfigfile(): string {
        return $this->_sCfgFile;
    }

    // -----------------------------------------------------------------------------
    // SETTERS
    // -----------------------------------------------------------------------------

    /**
     * Set a user and load its config if it exsists
     * @param string $sUser
     * @return bool
     */
    public function setUser(string $sUser): bool {
        $this->_sUser = $sUser;
        $this->_bChanged = false;
        $this->_aValues = $this->_aDefaultValues;
        $this->_sCfgFile = __DIR__ . '/../config/users/' . $sUser . '/settings.php';
        if (!is_dir(dirname($this->_sCfgFile))) {
            mkdir(dirname($this->_sCfgFile));
        }
        if (file_exists($this->_sCfgFile)) {
            $this->_aValues = (array) include($this->_sCfgFile);
        }
        return true;
    }

    /**
     * Set a single config value.
     * @param string $sKey
     * @param mixed $sValue
     * @throws Exception
     * @return void
     */
    public function setValue(string $sKey, $sValue) {
        if(! array_key_exists($sKey, $this->_aDefaultValues)) {
            throw new Exception(__METHOD__ . " - Error Key is invalid: '$sKey' - allowed are ".implode(', ', array_keys($this->_aDefaultValues)).".", 1);
        }
        if($this->_aValues[$sKey] !== $sValue) {
            $this->_aValues[$sKey] = $sValue;
            $this->_bChanged = true;
        }
    }

    /**
     * Set multiple config values.
     * @param array $aValues
     * @return void
     */
    public function setValues(array $aValues) {
        foreach($aValues as $sKey => $sValue) {
            $this->setValue((string) $sKey, $sValue);
        }
    }

    /**
     * PHP var_export() with short array syntax (square brackets) indented 2 spaces.
     *
     * NOTE: The only issue is when a string value has `=>\n[`, it will get converted to `=> [`
     * @link https://www.php.net/manual/en/function.var-export.php
     */
    protected function _varexport($expression, bool $return=FALSE) {
        $export = var_export($expression, TRUE);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ((bool)$return) return $export; else echo $export;
    }

    /**
     * Save user config file
     * @return bool
     */
    public function save() {
        if(!$this->_bChanged) {
            // SKIP saving
            return true;
        }
        if (file_put_contents($this->_sCfgFile, "<?php\n\ndeclare(strict_types=1);\n\nreturn " . $this->_varexport($this->_aValues, true) . ";")){
            $this->_bChanged = false;
            return true;
        } else {
            return false;
        };
    }

    /**
     * Delete user config file
     * @return bool
     */
    public function delete() {
        if(file_exists($this->_sCfgFile)) {
            return unlink($this->_sCfgFile);
        }
        return true;
    }
}