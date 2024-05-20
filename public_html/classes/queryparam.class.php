<?php
/**
 * ======================================================================
 * 
 * This is a php class to get safe values from $_GET|$_POST|$_SESSION
 * - grid layout
 * - navigation
 * - widgets and forms
 * 
 * ----------------------------------------------------------------------
 * 2024-05-18  <axel-hahn.de>  add variable types
 * ======================================================================
 */
class queryparam{

    /**
     * return value of a a given scope variable (e.g. $_GET|$_POST|$_SESSION) if it exists.
     * It will return NULLL if the value does not match an optional regex or type.
     * 
     * @param string  $sVarname      name of post or get variable (POST has priority)
     * @param string  $sRegexMatch   set a regex that must match
     * @param string  $sType         force type: false|int
     * @return mixed NULL|mixed
     */
    static public function getvar(array $aScope, string $sVarname, string $sRegexMatch='', string $sType='') :mixed {
        // check if it exist
        if(!isset($aScope[$sVarname])){
            return NULL;
        }
        
        $return = $aScope[$sVarname];
        
        // verify regex
        if ($sRegexMatch && !preg_match($sRegexMatch,$return)){
            return NULL;
        }
        
        // force given type
        switch ($sType){
            case 'int': 
                $return=(int)$return;
                break;
        }
        return $return;

    }

    /**
     * return value of a $_GET variable if it exists
     * 
     * @param string  $sVarname      name of get variable
     * @param string  $sRegexMatch   set a regex that must match
     * @param string  $sType         force type: false|int
     * @return mixed NULL|value
     */
    static public function get(string $sVarname, string $sRegexMatch='', string $sType='') :mixed
    {
        return self::getvar($_GET, $sVarname, $sRegexMatch, $sType);
    }

    /**
     * return value of a $_POST or $_GET variable if it exists
     * 
     * @param string  $sVarname      name of post or get variable (POST has priority)
     * @param string  $sRegexMatch   set a regex that must match
     * @param string  $sType         force type: false|int
     * @return mixed NULL|value
     */
    static public function getorpost(string $sVarname, string $sRegexMatch='', string $sType='') :mixed {
        // $this->logAdd(__METHOD__."($sVarname, $sRegexMatch, $sType) start");
        
        // check if it exist
        if(!isset($_POST[$sVarname]) && !isset($_GET[$sVarname])){
            // $this->logAdd(__METHOD__."($sVarname) $sVarname does not exist");
            return false;
        }
        
        // set it to POST or GET variable
        $aScope = isset($_POST[$sVarname]) && $_POST[$sVarname]
                ? $_POST[$sVarname] 
                : ((isset($_GET[$sVarname]) && $_GET[$sVarname])
                    ? $_GET[$sVarname] 
                    : false
                  )
            ;
        return self::getvar($aScope, $sVarname, $sRegexMatch, $sType);

    }
    /**
     * return value of a $_POST variable if it exists
     * 
     * @param string  $sVarname      name of post variable
     * @param string  $sRegexMatch   set a regex that must match
     * @param string  $sType         force type: false|int
     * @return mixed
     */
    static public function post(string $sVarname, string $sRegexMatch='', string $sType='') :mixed {
        return self::getvar($_POST, $sVarname, $sRegexMatch, $sType);
    }

}