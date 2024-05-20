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

global $_aIconClassData;
$_aIconClassData=include_once 'icon.class_include.php';

class icon{
    
    /**
     * get icon in i tag by a given css class name, eg fontawesome
     * 
     * @param string  $s   id or class of icon
     * @return string
     */
    static public function get(string $s) :string {
        global $_aIconClassData;
        return '<i class="'
            .(isset($_aIconClassData[$s]) ? $_aIconClassData[$s] : $s )
            .'"></i>&nbsp;'
            ;
    }
    /**
     * get classname of an icon
     * 
     * @param string  $s   id or class of icon
     * @return string
     */
    static public function getclass(string $s) :string {
        global $_aIconClassData;
        return $_aIconClassData[$s] ?? '';
    }

}