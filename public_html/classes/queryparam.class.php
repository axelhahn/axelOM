<?php

/**
 * Wrapping class to access $_GET|$_POST|$_SESSION variables with validation 
 * of a value
 * 
 * @example
 * Ensure that https://example.com/?page=<VALUE> will be accepted only if 
 * <VALUE> matches /^[a-z]*$/
 * <code>queryparam::get('page', '/^[a-z]*$/');</code>
 * 
 * Ideas:
 * - add more type validation
 * - add regex param with chars to remove eg /[^a-z]/ to keep only lowercase letters
 * 
 * Axel <axel.hahn@unibe.ch>
 * 2024-08-29  Axel php8 only; added variable types; short array syntax
 */

class queryparam
{

    /**
     * Get value of a a given scope variable (e.g. $_GET|$_POST|$_SESSION) if it exists.
     * It will return NULLL if the value does not match an optional regex or type.
     * 
     * @param array   $aScope        array of scope variables
     * @param string  $sVarname      name of post or get variable (POST has priority)
     * @param string  $sRegexMatch   set a regex that must match
     * @param string  $sType         force type: false|int
     * @return mixed NULL|value
     */
    static public function getvar(array $aScope, string $sVarname, string $sRegexMatch = '', string $sType = ''): mixed
    {
        // check if it exist
        if (!isset($aScope[$sVarname])) {
            return NULL;
        }

        $return = $aScope[$sVarname];

        // verify regex
        if ($sRegexMatch && !preg_match($sRegexMatch, $return)) {
            return NULL;
        }

        // force given type
        switch ($sType) {
            case 'int':
                $return = (int) $return;
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
    static function get(string $sVarname, string $sRegexMatch = '', string $sType = ''): mixed
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
    static function getorpost(string $sVarname, string $sRegexMatch = '', string $sType = ''): mixed
    {
        // $this->logAdd(__METHOD__."($sVarname, $sRegexMatch, $sType) start");

        // check if it exist
        if (!isset($_POST[$sVarname]) && !isset($_GET[$sVarname])) {
            // $this->logAdd(__METHOD__."($sVarname) $sVarname does not exist");
            return false;
        }

        // set it to POST or GET variable
        $aScope = isset($_POST[$sVarname])
            ? $_POST
            : $_GET
        ;
        return self::getvar($aScope, $sVarname, $sRegexMatch, $sType);

    }
    /**
     * return value of a $_POST variable if it exists
     * 
     * @param string  $sVarname      name of post variable
     * @param string  $sRegexMatch   set a regex that must match
     * @param string  $sType         force type: false|int
     * @return mixed NULL|value
     */
    static function post(string $sVarname, string $sRegexMatch = '', string $sType = ''): mixed
    {
        return self::getvar($_POST, $sVarname, $sRegexMatch, $sType);
    }

}