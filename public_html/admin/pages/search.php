<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * QUICK SEARCH
 * Find objects in current application by multiple keywords and AND condition
 * 
 * Result is shown in modal window
 * 
 */

 $sContent='';

$aReplace=[];
$sLangfile=__DIR__.'/../lang/'.$sUiLang.'.php';
if(file_exists($sLangfile)){
    foreach(include($sLangfile) as $sKey=>$sTranslated){
        $aReplace['{{'.$sKey.'}}']=$sTranslated;
    };
}

if(!$acl->canView($sTabApp)){
    $sContent.=str_replace(array_keys($aReplace), array_values($aReplace), '{{msgerr.missing_permissions}}');
    die($sContent);
}

$q=queryparam::get('q');
$sObjectUrl = '?app='.$sTabApp.'&page=object';

require_once('inc_functions.php');

include('inc_set_db.php'); // set $oDB

// ----------------------------------------------------------------------

if (count($appmeta->getObjects())){

    if($q && preg_match('/../', $q)){
        $iHits=0;

        // loop over all objects of the app and search in them
        foreach($appmeta->getObjects() as $sObj=>$aObjData){

            $o=initClass( $oDB, $sObj );
            if(is_object($o)){
                $iCount=$o->count();
                $sItems=$renderAdminLTE->getBadge([
                    'type'=>$iCount ? 'success' : 'secondary',
                    'title'=>'{{home.count}}: '.$iCount,
                    'text'=>' '.$iCount.' ',
                ]);

                $sWhere='';
                $iCounter=0;
                $aData=[];
                $sSearchFor='';
                foreach(explode(" ", $q) as $word){
                    $iCounter++;
                    $sIndex='word'.$iCounter;
                    $aData[$sIndex]='%'.$word.'%';
                    $sSearchFor.=($sSearchFor ? ' & ' : '') . '<code>&quot;'.htmlentities($word).'&quot;</code>';

                    $sWhere2='';
                    $sWhere.=($sWhere ? ' AND ' : '');
                    foreach($o->getAttributes() as $sColumn){
                        $sWhere2.=($sWhere2 ? ' OR ' : '')
                            .'`'.$sColumn.'` LIKE :'.$sIndex
                            ;
                    }
                    $sWhere.=' ( '.$sWhere2.' ) ';
                }
                $sSql='SELECT * FROM `'.$sObj.'` WHERE '.$sWhere;
                $aResult=$o->makeQuery($sSql, $aData);
                if($aResult && count($aResult)){
                    $sContent.= "<hr><h4>"
                    . '<a href="'.$sObjectUrl.'&object='.$sObj.'">'
                        .icon::get($appmeta->getObjectIcon($sObj))
                        .$appmeta->getObjectLabel($sObj)
                    .'</a></h4>'
                    .'<ol>'
                    ;
                    foreach($aResult as $aRow){
                        $iHits++;
                        $o->read($aRow['id']);
                        $sContent.= '<li><a href="'.$sObjectUrl.'&object='.$sObj.'&id='.$aRow['id'].'">'.$o->getLabel()."</a></li>";
                    }
                    $sContent.= '</ol>';
                }
                // print_r($oDB->queries());die();
                // $o->search();
            }
        }
        $sContent=$sContent 
        ? "<h3>✅ {{search.results_for}}   $sSearchFor</h3>"
            ."{{search.hits}}: <strong>$iHits</strong><br>"
            .($iHits > 25 ? '💡 {{search.hint_and_condition}}' : '')
            .$sContent
        : "<h3>❌ {{search.no_results_for}} $sSearchFor</h3>"
        ;

    } else {
            $sContent.='<h3>👉 {{search.type_ahead}}</h3>';
    }
} else {
    $sContent.='<h3>❌ {{search.no_objects}}</h3>{{search.no_objects_hint}}';
}



// ----------------------------------------------------------------------
// abort here - to prevent rendering a complete page.
// BUT we need the replacement of language texts


$sContent=str_replace(array_keys($aReplace), array_values($aReplace), $sContent);
die($sContent);

// ----------------------------------------------------------------------
