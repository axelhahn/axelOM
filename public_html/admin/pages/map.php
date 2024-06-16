<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * render a network map with dependencies
 * 
 */

$sContent='';


    /**
     * Get an array with group items of the checks
     * @return array
     */
    function _getVisualGroups()
    {
        $iGroup = 10000; // starting node id for groups
        $aReturn = [];
        $sBaseUrl = dirname($_SERVER['SCRIPT_NAME']) . '/images/icons/';
        foreach ([
            'cloud',
            'database',
            'deny',
            'disk',
            'file',
            'folder',
            'monitor',
            'network',
            'security',
            'service',
        ] as $sGroupname) {
            $aReturn[$sGroupname] = [
                'id' => $iGroup++,
                'label' => $this->_tr('group-' . $sGroupname),
                'image' => $sBaseUrl . $sGroupname . '.png',
            ];
        }

        return $aReturn;
    }


    /**
     * get image as data: string to embed 
     * @param  array  $aOptions  hash with option keys
     *                           - bgcolor  background of svg rect
     *                           - width    width of svg rect
     *                           - height   height of svg rect
     *                           - style    style of html div
     *                           - content  html code of div
     * @return string
     */
    function _getHtmlInSvg($aOptions)
    {
        $revert = ['%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'];
        $svg = '<svg xmlns="http://www.w3.org/2000/svg"'
            . (isset($aOptions['width'])   ? ' width="' . (int)$aOptions['width'] . '"'  : '')
            . (isset($aOptions['height'])  ? ' height="' . (int)$aOptions['height'] . '"' : '')
            . ' >'
            . '<rect x="0" y="0"'
            . (isset($aOptions['width'])   ? ' width="' . (int)$aOptions['width'] . '"'  : '')
            . (isset($aOptions['height'])  ? ' height="' . (int)$aOptions['height'] . '"' : '')
            . (isset($aOptions['bgcolor']) ? ' fill="' . $aOptions['bgcolor'] . '"'      : '')
            . ' stroke-width="20" stroke="#ffffff" >'
            . '</rect>'
            . '<foreignObject x="15" y="10" width="100%" height="100%">'
            . '<div xmlns="http://www.w3.org/1999/xhtml"'
            . (isset($aOptions['style']) ? ' style="' . $aOptions['style'] . '"' : '')
            . '>'
            . (isset($aOptions['content']) ? $aOptions['content'] : '')
            . "</div>"
            . '</foreignObject>'
            . '</svg>';
        // die($svg);
        // echo '<pre>'.htmlentities($svg).'</pre>'; 
        // die();
        return "data:image/svg+xml;charset=utf-8," . strtr(rawurlencode($svg), $revert);
    }

    /**
     * helper for _generateMonitorGraph: find node id of parent check
     * it returns fals if none was found
     * 
     * @return string|bool
     */
    function _findNodeId($sNeedle, $sKey, $aNodes)
    {
        foreach ($aNodes as $aNode) {
            if (isset($aNode[$sKey]) && $aNode[$sKey] === $sNeedle) {
                return $aNode['id'];
            }
        }
        return false;
    }
    /**
     * Get html code for visual view of all checks
     * @return string
     */
    function _generateMonitorGraph($sUrl)
    {
        $sReturn = '';

        // files with .png must exist in server/images/icons/
        $aParentsCfg = _getVisualGroups();
        $aNodes = [];
        $aEdges = [];
        $iCounter = 1;

        $aShapes = [
            RESULT_OK      => ['color' => '#55aa55', 'width' => 3],
            RESULT_UNKNOWN => ['color' => '#605ca8', 'width' => 3, 'shape' => 'ellipse'],
            RESULT_WARNING => ['color' => '#f39c12', 'width' => 6, 'shape' => 'dot'],
            RESULT_ERROR   => ['color' => '#ff3333', 'width' => 9, 'shape' => 'star'],
        ];

        foreach ($this->_data as $sAppId => $aEntries) {
            // echo '<pre>'.print_r($aEntries,1); die();
            if ($sUrl != $aEntries['result']['url']) {
                continue;
            }

            //
            // --- add application node
            //
            $aNodes[] = [
                'id' => 1,
                'title' => ''
                    . '<div class="result' . $aEntries['meta']["result"] . '">'
                    . '<img src="images/icons/check-' . $aEntries['meta']["result"] . '.png">'
                    . $this->_tr('Resulttype-' . $aEntries['meta']["result"])
                    . ' - '
                    . '<strong>' . $aEntries['meta']['website'] . '</strong><br>'

                    . '</div>',
                'label' => $aEntries['meta']['website'],
                'shape' => 'box',
                'widthConstraint' => ['maximum' => 300],
                'font' => ['size' => 18, 'color' => '#ffffff'],
                'color' => $aShapes[$aEntries['meta']['result']]['color'],

                // 'margin' =>[ 'top' => 120, 'right' => 50, 'bottom' => 20, 'left' => 50 ] ,
                'margin' => 20,
            ];

            //
            // --- add check nodes
            //
            foreach ($aEntries["checks"] as $aCheck) {
                $iCounter++;
                $iCheckId = $iCounter;
                $iParent = 1;
                $iGroup = false;
                $aNodes[] = [
                    '_check' => $aCheck['name'], // original check name - used for _findNodeId()
                    '_data' => $aCheck,          // original check data
                    'id' => $iCheckId,
                    'label' => $aCheck['name'],
                    'title' => '<table class="result' . $aCheck["result"] . '"><tr>'
                        . '<td align="center">'
                        . '<img src="images/icons/check-' . $aCheck["result"] . '.png"><br>'
                        . $this->_tr('Resulttype-' . $aCheck["result"])
                        . '</td><td>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;'
                        . '</td><td>'
                        . (isset($aCheck['group']) && isset($aParentsCfg[$aCheck['group']]['image']) ? '<img src="' . $aParentsCfg[$aCheck['group']]['image'] . '" width="22"> ' : '')
                        . '<strong>' . $aCheck["name"] . '</strong><br>'
                        . '<em>' . $aCheck["description"] . '</em><br>'
                        . "<br>"
                        . $aCheck['value']
                        . '</td>'
                        . '</tr></table>',

                    'shape' => 'image',
                    'image' => "images/icons/check-" . $aCheck["result"] . ".png",
                ];
            }

            //
            // --- find parent check node and draw connectors
            //
            foreach ($aNodes as $aNode) {
                $iParent = 1;
                $iGroup = false;
                if (!isset($aNode['_data'])) {
                    continue;
                }

                $aCheck = $aNode['_data'];
                $iCheckId = $aNode['id'];

                if (isset($aCheck["parent"]) && $aCheck["parent"]) {
                    $iParent = $this->_findNodeId($aCheck["parent"], '_check', $aNodes);
                }
                // --- if a group was given: detect a group connected on parent 
                if (isset($aCheck['group']) && $aCheck['group']) {
                    $sGroup2Detect = $aCheck['group'] . '_' . $iParent;
                    $iGroup = $this->_findNodeId($sGroup2Detect, '_group', $aNodes);
                    if (!$iGroup) {
                        // create group node
                        $iCounter++;
                        $iGroup = $iCounter;
                        $aNodes[] = [
                            '_group' => $aCheck['group'] . '_' . $iParent, // group name - used for _findNodeId()
                            'id' => $iGroup,
                            'label' => isset($aParentsCfg[$aCheck['group']]['label']) ? $aParentsCfg[$aCheck['group']]['label'] : '[' . $aCheck['group'] . ']',
                            'shape' => isset($aParentsCfg[$aCheck['group']]['image']) ? 'image' : 'box',
                            'image' => isset($aParentsCfg[$aCheck['group']]['image']) ? $aParentsCfg[$aCheck['group']]['image'] : 'NOIMAGE ' . $aCheck['group'],
                            'opacity' => 0.2
                        ];
                        // connect it with app or perent check
                        $aEdges[] = [
                            'from' => $iParent,
                            'to' => $iGroup,
                            'color' => ['color' => $aShapes[$aCheck['result']]['color']],
                            'width' => $aShapes[$aCheck['result']]['width']
                        ];
                    }
                }
                $aEdges[] = [
                    'from' => $iGroup ? $iGroup : $iParent,
                    'to' => $iCheckId,
                    'color' => ['color' => $aShapes[$aCheck['result']]['color']],
                    'width' => $aShapes[$aCheck['result']]['width']
                ];
                // echo '<pre>Check='.print_r($aCheck,1) . "Edges=" . print_r($aEdges,1) . "parent: $iParent<br>group: $iGroup<br></pre>..."; // die();

            }
        }

        $sReturn .= '

        </style>

        <div id="networkContainer">
            <div id="network-toolbar">
                <span id="selView"></span>
                <button class="btn btn-default" onclick="oMap.switchViewMode(); return false;">switch View</button>
                <!--
                <button class="btn btn-default" onclick="oMap.switchViewSize(); return false;">switch Size</button>
                -->
                <button class="btn btn-default" onclick="oMap.toggleFullscreen(\'networkContainer\'); return false;">Fullscreen</button>

            </div>
            <div id="mynetwork"></div>
        </div>

        <script type="text/javascript">
        
        // GRRR instance must have the name oMap at the moment
        var oMap=new visJsNetworkMap();
        if(!oMap){
            console.log("ERROR: var oMap=new visJsNetworkMap(); failed.")
        } else {
            oMap.setData(' . json_encode($aNodes) . ', ' . json_encode($aEdges) . ');
            oMap.redrawMap();
        }

      </script>        
        ';
        // echo "<pre>" . htmlentities($sReturn); die();
        // echo "<pre>" . htmlentities(json_encode($aNodes)); die();
        return $sReturn;
    }


// ----------------------------------------------------------------------
