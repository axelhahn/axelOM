<?php
/**
 * ----------------------------------------------------------------------
 * 
 * Generator for HTML Tags
 * 
 * see getTag() ... 
 *    it generates html code for tags 
 *    - with and without label
 *    - with and without closing tag
 * 
 * see _setAttributes($aAttributes) ... 
 *    attribute will be inserted with given key-value array
 *    BUT with special attribute keys:
 *    - label - will be used as label
 *    - icon  - will be added as <i class="[icon value]"></i> to the label
 * 
 * 
 * @author Axel Hahn
 * @link TODO
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * 
 * ----------------------------------------------------------------------
 * 2024-05-18        <axel>  add variable types
 * 2025-11-05        <axel>  remove attribute on NULL value; optimze spacing between attributes
 */
class htmlelements {

    /**
     * set of defined icon prefixes
     * @var array 
     */
    protected array $_aIcons=[];
    
    /**
     * label text of current html tag
     * @var string
     */
    protected string $_sLabel = '';

    /**
     * list of html attributes for current tag
     * @var array
     */
    protected array $_aAttributes = [];
    

    // ----------------------------------------------------------------------
    // CONSTRUCTOR
    // ----------------------------------------------------------------------
    
    public function __construct() {
        // return true;
    }

    // ----------------------------------------------------------------------
    // 
    // PRIVATE FUNCTIONS 
    // 
    // ----------------------------------------------------------------------
    
    
    /**
     * generate html attibutes with all internal attributes key -> values
     * @return string
     */
    protected function _addAttributes() :string {
        $sReturn = '';
        foreach ($this->_aAttributes as $sAttr => $sValue) {
            if(is_array($sValue)){
                echo "ERROR: an html tag was defined with array in attribute [$sAttr]:<br><pre>".print_r($this->_aAttributes, 1)."</pre>";
            }
            $sReturn .= ($sValue!==null) 
                ? (($sReturn ? ' ' : '' ) . "$sAttr=\"$sValue\"") 
                : '';
            
        }
        return $sReturn ? " $sReturn" : "";
    }
    
    
    /**
     * internal helper: fetch all attributes from key-value hash; 
     * Specialties here:
     * - label will be extracted from key 'label' 
     * - and optional existing key 'icon' will be added at beginning of a label
     * 
     * @param array $aAttributes
     * @return boolean
     */
    protected function _setAttributes(array $aAttributes) :bool {
        $this->_sLabel='';
        if(isset($aAttributes['icon']) && $aAttributes['icon']){
            $this->_sLabel.=$this->getIcon($aAttributes['icon']);
            unset($aAttributes['icon']);
        }
        if(isset($aAttributes['label']) && $aAttributes['label']){
            $this->_sLabel .= $aAttributes['label'];
            unset($aAttributes['label']);
        }
        $this->_aAttributes=$aAttributes;
        return true;
    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS
    // HTML GENERIC
    // 
    // ----------------------------------------------------------------------
    
    /**
     * generic function to get html code for a single tag 
     * 
     * @param string   $sTag          tag name
     * @param array    $aAttributes   array with attributes (optional including 'icon' and 'label')
     * @param boolean  $bCloseTag     optional: set false if tag has no closing tag (= ending with "/>")
     * @return string
     */
    public function getTag(string $sTag, array $aAttributes, bool $bCloseTag=true) :string {
        $sTpl = $bCloseTag ? "<$sTag%s>%s</$sTag>" : "<$sTag %s/>%s";
        $this->_setAttributes($aAttributes);
        return sprintf($sTpl, $this->_addAttributes(), $this->_sLabel);
    }
    
    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS
    // SIMPLE HTML ELEMENTS
    // 
    // ----------------------------------------------------------------------

    /**
     * helper detect prefix of a string add prefix of a framework
     * i.e. value "fa-close" detects font awesome and adds "fa " as prefix
     * 
     * @param string $sIconclass
     * @return string
     */
    public function getIcon(string $sIconclass='') :string{
        if(!$sIconclass){
            return '';
        }
        $sPrefix='';
        foreach ($this->_aIcons as $sPrefix =>$add) {
            if (strpos($sIconclass, $sPrefix)===0){
                $sPrefix=$add;
                continue;
            }
        }
        return '<i class="'.$sPrefix.$sIconclass.'"></i> ';
    }
   

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS
    // HTML COMPONENTS
    // 
    // ----------------------------------------------------------------------

    /**
     * get html code for an input field
     * 
     * @param array $aAttributes  attributes of the select tag
     * @return string
     */
    public function getFormInput(array $aAttributes) :string {
        $sTpl = '<input %s/>';
        $this->_setAttributes($aAttributes);
        return sprintf($sTpl, $this->_addAttributes());
    }
    /**
     * get html code for an option field in a select drop down
     * 
     * @param array $aAttributes  attributes of the option tag
     * @return string
     */
    public function getFormOption(array $aAttributes) :string {
        $sTpl = '<option %s>%s</option>';
        $this->_setAttributes($aAttributes);
        return sprintf($sTpl, $this->_addAttributes(), $this->_sLabel);
    }
    /**
     * get html code for a select drop down
     * 
     * @param array $aAttributes  attributes of the select tag
     * @param array $aOptions     array for all option fields
     * @return bool|string
     */
    public function getFormSelect(array $aAttributes, array $aOptions=[]) :bool|string {
        if(!count($aOptions)){
            return false;
        }
        $sOptions='';
        foreach($aOptions as $aOptionAttributes){
            $sOptions.=$this->getTag('option', $aOptionAttributes);
        }
        $aAttributes['label']=$sOptions;
        return $this->getTag('select', $aAttributes);
    }

    /**
     * get html code for a table
     * 
     * @param array $aHead             array with table header columns
     * @param array $aBody             array with table body rows and cols
     * @param array $aTableAttributes  optional: attributes of the table tag
     * @return string
     */
    public function getTable(array $aHead, array $aBody, array $aTableAttributes=[]) :string {
        $sTdata='';
        $sThead='';
        $sTpl = '<table %s>'
                . '<thead><tr>%s</tr></thead>'
                . '<tbody>%s</tbody>'
                . '</table>';
        
        foreach($aHead as $sTh){
            $sThead.='<th>'.$sTh.'</th>';
        }
        foreach($aBody as $aTr){
            $sTdata.='<tr>';
            foreach($aTr as $sTd){
                $sTdata.='<td>'.$sTd.'</td>';
            }
            $sTdata.='</tr>';
        }
        $this->_setAttributes($aTableAttributes);
        return sprintf($sTpl, 
                $this->_addAttributes(), 
                $sThead,
                $sTdata
                );
    }
}
