<?php
/**
 * ======================================================================
 * 
 * This is a php class to get safe values from $_GET|$_POST|$_SESSION
 * - grid layout
 * - navigation
 * - widgets and forms
 * 
 * @example <code>
 * // load class
 * require_once('../classes/icon.class.php');
 * 
 * // get html code for an icon with its keyword
 * echo icon::get('back');
 * </code>
 * ----------------------------------------------------------------------
 * 2024-05-18  <axel-hahn.de>  add variable types
 * 2025-04-10  <axel-hahn.de>  fix static method
 * ======================================================================
 */

global $_aIconClassData;
$_aIconClassData=include_once 'icon.class_include.php';

class icon{
    
    /**
     * get icon in i tag by a given css class name, eg fontawesome
     * 
     * @param string  $s   id or class of icon
     * @return string
     */
    public static function get(string $s) :string {
        global $_aIconClassData;
        return '<i class="'
            .($_aIconClassData[$s] ?? $s)
            .'"></i>&nbsp;'
            ;
    }
    /**
     * get classname of an icon
     * 
     * @param string  $s   id or class of icon
     * @return string
     */
    public static function getclass(string $s) :string {
        global $_aIconClassData;
        return $_aIconClassData[$s] ?? '';
    }

}