<?php


class usersettings {

    protected string $_sUser;
    protected string $_sCfgFile;

    protected array $_aValues = [];

    protected array $_aDefaultValues = [
        'lang'=>'',
        'iconset'=>'',
        'theme'=>'',
        'debug'=>false,
    ];

    public function __construct(string $sUser='') {
        if($sUser) {
            $this->setUser($sUser);
        }
    }


    public function get(string $sKey):mixed {
        return $this->_aValues[$sKey]??null;
    }

    public function getConfig():array {
        return $this->_aValues;
    }

    public function getConfigfile(): string {
        return $this->_sCfgFile;
    }

    public function setUser(string $sUser): true {
        $this->_sUser = $sUser;
        $this->_aValues = $this->_aDefaultValues;
        $this->_sCfgFile = __DIR__ . '/../config/users/' . $sUser . '/settings.php';
        if (file_exists($this->_sCfgFile)) {
            $this->_aValues = (array) include($this->_sCfgFile);
        }
        return true;
    }

    public function setValue(string $sKey, $sValue) {
        if(! array_key_exists($sKey, $this->_aDefaultValues)) {
            throw new Exception(__METHOD__ . " - Error Key is invalid: '$sKey' - allowed are ".implode(', ', array_keys($this->_aDefaultValues)).".", 1);
        }
        if($this->_aValues[$sKey] !== $sValue) {
            $this->_aValues[$sKey] = $sValue;
            // $this->save();
        }
    }

    public function setValues(array $aValues) {
        foreach($aValues as $sKey => $sValue) {
            $this->setValue((string) $sKey, $sValue);
        }
    }

    public function save() {
        return file_put_contents($this->_sCfgFile, "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($this->_aValues, true) . ";");
    }

    public function delete() {
        if(file_exists($this->_sCfgFile)) {
            return unlink($this->_sCfgFile);
        }
        return true;
    }
}