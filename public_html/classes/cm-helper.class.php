<?php
/**
 * ----------------------------------------------------------------------
 * 
 * HELPER CLASS for syntax highlighting with codemiror
 * 
 * @author Axel Hahn
 * @link TODO
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * 
 * ----------------------------------------------------------------------
 * 2025-11-16  v0.1  <axel>  initial version
 */
/**
 * 
 * HELPER CLASS for syntax highlighting with codemiror
 * 
 */

class cmhelper {

    /**
     * @var string base url for codemirror files
     */
    protected string $_sCmbaseUrl='';

    /**
     * @var array memory for loaded modes
     */
    protected array $_aModes2Load=[];

    /**
     * @var array memory for loaded themes
     */
    protected array $_aThemes2Load=[];

    /**
     * @var string html tags (css, js) für html head section
     */
    protected string $_sHtmlHead='';

    /**
     * @var string javascript code to iniialize editors
     */
    protected string $_sJS='';

    /**
     * Mapping array for codemirror modes
     * 
     * WORK IN PROGRESS 
     * These are just a few of the supported languages!
     * @see https://codemirror.net/5/mode/index.html
     * 
     * Per language/ file type we define
     * - load {array}  list of javascript files to load
     * - mode {string} mode value when initalizing editor object
     * 
     * @var array
     */
    public array $aSHLangs=[];

    // ----------------------------------------------------------------------
    // 
    // ----------------------------------------------------------------------

    /**
     * __construct
     */
    public function __construct(){
        $aCfg=@include 'cm-helper.config.php';
        $this->aSHLangs=@include 'cm-helper.lang.php';
        $this->setBase($aCfg['baseurl']??false);
    }

    // ----------------------------------------------------------------------
    //  SETTER
    // ----------------------------------------------------------------------

    /**
     * Set a base url for codemirror resources.
     * Its value cannot be verified - it is just used for html head
     * Check the browser dev tools -> network if the resource can be loaded.
     * 
     * @param string $sNewbase  new ur
     * @return void
     */
    public function setBase(string $sNewbase=''): void{
        $this->_sCmbaseUrl=$sNewbase?:$this->_sCmbaseUrl;
        $this->_sHtmlHead.='
            <!-- codemirror -->
            <link rel="stylesheet" href="'.$this->_sCmbaseUrl.'/codemirror.min.css">
            <link rel="stylesheet" href="'.$this->_sCmbaseUrl.'/theme/neo.min.css">
            <script src="'.$this->_sCmbaseUrl.'/codemirror.min.js"></script>
            <script src="'.$this->_sCmbaseUrl.'/addon/selection/selection-pointer.min.js"></script>
            <script src="'.$this->_sCmbaseUrl.'/addon/edit/matchbrackets.min.js"></script>

            <link rel="stylesheet" href="'.$this->_sCmbaseUrl.'/addon/lint/lint.css">
            <script src="'.$this->_sCmbaseUrl.'/addon/lint/lint.css"></script>            
        ';
        $this->_sJS.='';
    }

    // ----------------------------------------------------------------------
    // ADD EDITOR
    // ----------------------------------------------------------------------

    /**
     * Add an editor with its own syntax highlighting for a textarea.
     * 
     * @param string $sCssClass   css classname named "highlight-<lang>"
     * @param string $sFormid     id of the textarea
     * @param array $aMoreOtions  array of additional options. known subkey are
     *                            - mime  string  set a mime if mode does not
     *                                            fit the mode param for 
     *                                            codemirror
     *                            - theme  string thene name
     * @return bool
     */
    public function addEditor(string $sCssClass, string $sFormid='', array $aMoreOtions=[]):bool{

        static $iCmCounter;

        if(!($iCmCounter??false)){
            $iCmCounter=0;
        }
        $iCmCounter++;

        $sMode=str_replace('highlight-', '', $sCssClass);

        if(!($this->_aModes2Load[$sMode]??false)){
            $this->_aModes2Load[$sMode]=true;
            if($this->aSHLangs[$sMode]??false){
                foreach($this->aSHLangs[$sMode]['load'] as $sJsfile){
                    $this->_sHtmlHead.="<script src=\"$this->_sCmbaseUrl/mode/$sJsfile/$sJsfile.js\"></script>";
                }
            }

            if($sMode=='htmlmixed'){
                $this->_sJS.='
                        <script>
                            var mixedMode = {
                                name: "htmlmixed",
                                scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
                                            mode: null},
                                            {matches: /(text|application)\/(x-)?vb(a|script)/i,
                                            mode: "vbscript"}]
                            };
                        </script>                
                ';

            }
        }

        $sTheme='';
        if($aMoreOtions['theme']??false){
            $sTheme=$aMoreOtions['theme'];
            if(!($this->_aThemes2Load[$sTheme]??false)){
                $this->_aThemes2Load[$sTheme]=true;
                $this->_sHtmlHead.="<link rel=\"stylesheet\" href=\"$this->_sCmbaseUrl/theme/$sTheme.min.css\">";
            }
        }

        $this->_sJS.='
            <script>
                window.onload = function() {
                    CodeMorrorEditor'.$iCmCounter.' = CodeMirror.fromTextArea(
                        document.getElementById("'.$sFormid.'"), {
                            '.($sTheme ? "theme: \"$sTheme\"," : '').'
                            lineNumbers: true,
                            matchBrackets: true,
                            selectionPointer: true,
                            styleActiveLine: true,
                            indentUnit: 4,
                            indentWithTabs: true,
                            mode: "'.($this->aSHLangs[$sMode]['mode'] ?? $sMode).'"
                    });
                };
            </script>            
        ';

        return true;
    }

    // ----------------------------------------------------------------------
    // GETTER
    // ----------------------------------------------------------------------

    /**
     * Get html code for html head lines
     * @return string
     */
    public function getHtmlHead(): string{
        return $this->_sHtmlHead;
    }

    /**
     * Get html code for javascript code for bottom of html page
     * @return string
     */
    public function getJs(): string{
        return $this->_sJS;
    }

    /**
     * Get a list of supported languages
     * @return array
     */
    public function getLanguages(){
        return array_keys($this->aSHLangs);
    }
}