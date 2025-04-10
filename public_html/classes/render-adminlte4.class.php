<?php
require_once 'htmlelements.class.php';
/**
 * ______________________________________________________________________
 * 
 *     _  __  __  _    
 *    | ||  \/  || |__     Institute for Medical Education
 *    |_||_|\/|_||____|    University of Bern
 * 
 * ______________________________________________________________________
 * 
 * RENDERER FOR ADNINLTE 4 (beta) template https://adminlte.io
 * its docs: https://marlonluan.github.io/adminlte4/dist/pages/index.html
 *           https://getbootstrap.com/docs/5.3/getting-started/introduction/
 * 
 * This is a php class to render
 * - grid layout
 * - navigation
 * - widgets, components and forms
 * 
 * DOCS: https://os-docs.iml.unibe.ch/adminlte-renderer/
 * ----------------------------------------------------------------------
 * 2023-09-11  <axel.hahn@unibe.ch>  add shadows on card + callout
 * 2023-09-27  <axel.hahn@unibe.ch>  add form input fields
 * 2023-11-17  <axel.hahn@unibe.ch>  add tabbed content; "=" renders hamburger item
 * 2024-05-03  <axel.hahn@unibe.ch>  add line in sidebar menu; add getFormSelect
 * 2024-05-10  <axel.hahn@unibe.ch>  add support for bootstrap-select in getFormSelect
 * 2024-05-18  <axel.hahn@unibe.ch>  add variable types
 * 2024-07-04  <axel.hahn@unibe.ch>  added type declarations
 * 2024-07-10  <axel.hahn@unibe.ch>  WIP start using AdminLTE 4.0.0 beta (using Bootstrap 5)
 * ======================================================================
 */
class renderadminlte
{

    protected array $aPresets = [

        'bgcolor' => [
            'description' => 'background colors',
            'group' => 'styling',
            'values' => [
                // https://adminlte.io/themes/v3/pages/UI/general.html
                '' => 'no value',
                /*
                    no more colors in v4 ...

                'indigo' => 'indigo',
                'lightblue' => '',
                'navy' => '',
                'purple' => '',
                'fuchsia' => '',
                'pink' => '',
                'maroon' => '',
                'orange' => '',
                'lime' => '',
                'teal' => '',
                'olive' => '',
                */

                'black' => 'black',
                'dark' => 'dark gray',
                'gray' => 'gray',
                'light' => 'light gray',
            ]
        ],

        'type' => [
            'description' => 'type or status like info/ warning/ danger to define a color',
            'group' => 'styling',
            'values' => [
                '' => 'no value',
                'danger' => 'red',
                'info' => 'aqua',
                'primary' => 'blue',
                'secondary' => 'gray',
                'success' => 'green',
                'warning' => 'yellow',
                'dark' => 'dark gray',
                'gray' => 'gray',
            ]
        ],
        'shadow' => [
            'description' => 'use a shadow',
            'group' => 'styling',
            'values' => [
                '' => 'no value',
                'none' => 'none',
                'small' => 'small',
                'regular' => 'regular',
                'large' => 'large'
            ]
        ],
        'size' => [
            'description' => 'set a size',
            'group' => 'styling',
            'values' => [
                '' => 'no value',
                'lg' => '',
                'sm' => '',
                'xs' => '',
                'flat' => '',
            ]
        ],
        'variant' => [
            'description' => 'coloring style',
            'group' => 'styling',
            'values' => [
                '' => 'no value',
                'outline' => 'small stripe on top',
                'solid' => 'full filled widget',
                'gradient' => 'full filled with gradient',
            ]
        ],
        'visibility' => [
            'description' => '',
            'group' => 'customizing',
            'values' => [
                '' => 'no value',
                '0' => 'hide',
                '1' => 'show',
            ]
        ],
        // for keys: state
        'windowstate' => [
            'description' => 'state of a resizable widget',
            'group' => 'customizing',
            'values' => [
                '' => 'no value',
                'collapsed' => 'header only',
                'maximized' => 'full window',
            ]
        ],
        // for keys: dismissable
        'yesno' => [
            'description' => '',
            'group' => 'customizing',
            'values' => [
                '' => 'no value',
                '0' => 'no',
                '1' => 'yes',
            ]
        ],
    ];

    protected array $_aValueMappings = [
        'shadow' => [
            'default' => '',
            'none' => 'shadow-none',
            'small' => 'shadow-small',
            'regular' => 'shadow',
            'large' => 'shadow-lg',
        ]
    ];

    protected array $_aElements = [];

    /**
     * instance of htmlelements object
     * @var object
     */
    protected object $_oHtml;


    // ----------------------------------------------------------------------
    // 
    // CONSTRUCTOR
    // 
    // ----------------------------------------------------------------------
    public function __construct()
    {
        $this->_oHtml = new htmlelements();
        $this->_initElements();
        // return true;
    }

    // ----------------------------------------------------------------------
    // 
    // PRIVATE FUNCTIONS 
    // 
    // ----------------------------------------------------------------------

    /**
     * used in cosntructor
     * initialize all element definitions
     * @return void
     */
    protected function _initElements(): void
    {
        $this->_aElements = [

            // ------------------------------------------------------------
            'alert' => [
                'label' => 'Alert',
                'description' => 'Colored box with title and a text',
                'method' => 'getAlert',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => 'warning'],
                    'dismissible' => ['select' => $this->aPresets['yesno'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'title' => [
                        'description' => 'Title in a bit bigger font',
                        'group' => 'content',
                        'example_value' => 'Alert title'
                    ],
                    'text' => [
                        'description' => 'Message text',
                        'group' => 'content',
                        'example_value' => 'I am a message. Read me, please.'
                    ],
                ]
            ],
            // ------------------------------------------------------------
            'badge' => [
                'label' => 'Badge',
                'description' => 'Tiny additional info; mostly as counter',
                'method' => 'getBadge',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => 'danger'],
                    'bgcolor' => ['select' => $this->aPresets['bgcolor'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'id' => [
                        'group' => 'customizing',
                        'description' => 'optional: id attribute',
                        'example_value' => ''
                    ],
                    'title' => [
                        'group' => 'content',
                        'description' => 'optional: title attribute for mouseover',
                        'example_value' => 'Errors: 5'
                    ],
                    'text' => [
                        'group' => 'content',
                        'description' => 'Text or value in the badge',
                        'example_value' => '5'
                    ],
                ]
            ],
            // ------------------------------------------------------------
            'button' => [
                'label' => 'Button',
                'description' => 'Buttons<br>In this component you can add other parmeter keys too - these will be added as attributes in the button tag.',
                'method' => 'getButton',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => 'primary'],
                    'size' => ['select' => $this->aPresets['size'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'text' => [
                        'group' => 'content',
                        'description' => 'Text/ html code on the button',
                        'example_value' => 'Click me'
                    ],
                ]
            ],
            // ------------------------------------------------------------
            'callout' => [
                'label' => 'Callout',
                'description' => 'Kind of infobox',
                'method' => 'getCallout',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => 'danger'],
                    'shadow' => ['select' => $this->aPresets['shadow'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'title' => [
                        'group' => 'content',
                        'description' => 'Title in a bit bigger font',
                        'example_value' => 'I am a callout'
                    ],
                    'text' => [
                        'group' => 'content',
                        'description' => 'Message text',
                        'example_value' => 'Here is some description to whatever.'
                    ],
                ]
            ],
            // ------------------------------------------------------------
            'card' => [
                'label' => 'Card',
                'description' => 'Content box with header, text, footer',
                'method' => 'getCard',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => 'primary'],
                    'variant' => ['select' => $this->aPresets['variant'], 'example_value' => 'outline'],
                    'shadow' => ['select' => $this->aPresets['shadow'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'state' => [
                        'group' => 'customizing',
                        'select' => $this->aPresets['windowstate'],
                        'example_value' => ''
                    ],

                    'tb-collapse' => ['description' => 'show minus symbol as collapse button', 'select' => $this->aPresets['visibility'], 'example_value' => ''],
                    'tb-expand' => ['description' => 'show plus symbol to expand card', 'select' => $this->aPresets['visibility'], 'example_value' => ''],
                    'tb-maximize' => ['description' => 'show maximize button for fullscreen', 'select' => $this->aPresets['visibility'], 'example_value' => ''],
                    'tb-minimize' => ['description' => 'show minimize button to minimize', 'select' => $this->aPresets['visibility'], 'example_value' => ''],
                    'tb-remove' => ['description' => 'show cross symbol to remove card', 'select' => $this->aPresets['visibility'], 'example_value' => ''],

                    'title' => [
                        'group' => 'content',
                        'description' => 'Title in the top row',
                        'example_value' => 'I am a card'
                    ],
                    'tools' => [
                        'group' => 'content',
                        'description' => 'Html code for the top right',
                        'example_value' => ''
                    ],
                    'text' => [
                        'group' => 'content',
                        'description' => 'Main content',
                        'example_value' => 'Here is some beautiful content.'
                    ],
                    'footer' => [
                        'group' => 'content',
                        'description' => 'optional: footer content',
                        'example_value' => 'Footer'
                    ],
                ]
            ],
            // ------------------------------------------------------------
            'infobox' => [
                'label' => 'Info box',
                'description' => 'Box with icon to highlight a single value; optional with a progress bar',
                'method' => 'getInfobox',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => ''],
                    'iconbg' => ['select' => $this->aPresets['type'], 'example_value' => 'info'],
                    'shadow' => ['select' => $this->aPresets['shadow'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'icon' => [
                        'group' => 'content',
                        'description' => 'css class for an icon',
                        'example_value' => 'fa-regular fa-thumbs-up'
                    ],
                    'text' => [
                        'group' => 'content',
                        'description' => 'short information text',
                        'example_value' => 'Likes'
                    ],
                    'number' => [
                        'group' => 'content',
                        'description' => 'a number to highlight',
                        'example_value' => "41,410"
                    ],
                    'progressvalue' => [
                        'group' => 'content',
                        'description' => 'optional: progress value 0..100 to draw a progress bar',
                        'example_value' => 70
                    ],
                    'progresstext' => [
                        'group' => 'content',
                        'description' => 'optional: text below progress bar',
                        'example_value' => '70% Increase in 30 Days'
                    ]
                ]
            ],
            // ------------------------------------------------------------
            'smallbox' => [
                'label' => 'Small box',
                'description' => 'Solid colored box to highlight a single value; optional with a link',
                'method' => 'getSmallbox',

                'params' => [
                    'type' => ['select' => $this->aPresets['type'], 'example_value' => 'info'],
                    'shadow' => ['select' => $this->aPresets['shadow'], 'example_value' => ''],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => ''
                    ],
                    'icon' => ['group' => 'content', 'description' => 'css class for an icon', 'example_value' => 'fa-solid fa-shopping-cart'],

                    'text' => ['group' => 'content', 'description' => 'short information text', 'example_value' => 'New orders'],
                    'number' => ['group' => 'content', 'description' => 'a number to highlight', 'example_value' => "150"],
                    'url' => ['group' => 'content', 'description' => 'optional: url to set a link on the bottom', 'example_value' => '#'],
                    'linktext' => ['group' => 'content', 'description' => 'used if a url was given: linked text', 'example_value' => 'More info']
                ]
            ],
            // ------------------------------------------------------------
            'input' => [
                'label' => 'Form: input',
                'description' => 'Input form fiels',
                'method' => 'getFormInput',

                'params' => [
                    'label' => [
                        'group' => 'styling',
                        'description' => 'label for the input field',
                        'example_value' => 'Enter something'
                    ],
                    'type' => [
                        'select' => [
                            'description' => 'type or input field',
                            'group' => 'styling',
                            'values' => [

                                'button' => 'button',
                                'checkbox' => 'checkbox',
                                'color' => 'color',
                                'date' => 'date',
                                'datetime-local' => 'datetime-local',
                                'email' => 'email',
                                'file' => 'file',
                                'hidden' => 'hidden',
                                'image' => 'image',
                                'month' => 'month',
                                'number' => 'number',
                                'password' => 'password',
                                'radio' => 'radio',
                                'range' => 'range',
                                'reset' => 'reset',
                                'search' => 'search',
                                'submit' => 'submit',
                                'tel' => 'tel',
                                'text' => 'text',
                                'time' => 'time',
                                'url' => 'url',
                                'week' => 'week',
                            ]
                        ],
                        'example_value' => 'text'
                    ],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => 'myclass'
                    ],
                    'prepend' => [
                        'group' => 'styling',
                        'description' => 'optional: content on input start',
                        'example_value' => ''
                    ],
                    'append' => [
                        'group' => 'styling',
                        'description' => 'optional: content on input end',
                        'example_value' => ''
                    ],
                    'name' => [
                        'group' => 'content',
                        'description' => 'name attribute',
                        'example_value' => 'firstname'
                    ],
                    'value' => [
                        'group' => 'content',
                        'description' => 'Value',
                        'example_value' => 'Jack'
                    ],
                ]
            ],
            // ------------------------------------------------------------
            // WIP
            'select' => [
                'label' => 'Form: select',
                'description' => 'Select box',
                'method' => 'getFormSelect',

                'params' => [
                    'label' => [
                        'group' => 'styling',
                        'description' => 'label for the select field',
                        'example_value' => 'Enter text'
                    ],
                    'bootstrap-select' => [
                        'select' => [
                            'description' => 'Enable bootstrap-select plugin',
                            'group' => 'styling',
                            'values' => [
                                '0' => 'no',
                                '1' => 'yes',
                            ]
                        ]
                    ],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => 'myclass'
                    ],
                    'options' => [
                        'example_value' => [
                            ["value" => "1", "label" => "one"],
                            ["value" => "2", "label" => "two"],
                            ["value" => "3", "label" => "three"],
                        ],
                    ]
                ],
            ],
            // ------------------------------------------------------------
            'textarea' => [
                'label' => 'Form: textarea',
                'description' => 'textarea or html editor',
                'method' => 'getFormTextarea',

                'params' => [
                    'label' => [
                        'group' => 'styling',
                        'description' => 'label for the input field',
                        'example_value' => 'Enter text'
                    ],
                    'type' => [
                        'select' => [
                            'description' => 'type or input field',
                            'group' => 'styling',
                            'values' => [
                                '' => 'text',
                                'html' => 'html editor',
                            ]
                        ],
                    ],
                    'class' => [
                        'group' => 'styling',
                        'description' => 'optional: css classes',
                        'example_value' => 'myclass'
                    ],
                    'name' => [
                        'group' => 'content',
                        'description' => 'name attribute',
                        'example_value' => 'textdata'
                    ],
                    'value' => [
                        'group' => 'content',
                        'description' => 'Value',
                        'example_value' => 'Here is some text...'
                    ],
                ]
            ],
        ];
    }
    /**
     * helper function: a shortcut for $this->_oHtml->getTag
     * @param  string  $sTag         name of html tag
     * @param  array   $aAttributes  array of its attributes
     * @param  string  $sContent     content between opening and closing tag
     * @param  bool    $bClosetag    flag: write a closing tag or not? default: true
     * @return string
     */
    protected function _tag(string $sTag, array $aAttributes, string $sContent = '', bool $bClosetag = true): string
    {
        if ($sContent) {
            $aAttributes['label'] = (isset($aAttributes['label']) ? $aAttributes['label'] : '') . $sContent;
        }
        return $this->_oHtml->getTag($sTag, $aAttributes, $bClosetag);
    }
    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS AdminLTE 3.2
    // 
    // ----------------------------------------------------------------------

    /**
     * render a page by using template 
     * @param  string  $stemplate  html template with placeholders
     * @param  array   $aReplace   key = what to replace .. value = new value
     * @return string
     */
    public function render(string $sTemplate, array $aReplace): string
    {
        return str_replace(
            array_keys($aReplace),
            array_values($aReplace),
            $sTemplate
        );
    }

    /**
     * add a wrapper: wrap some content into a tag
     * 
     * @param  string  $sTag      name of html tag
     * @param  array   $aOptions  array of its attributes
     * @param  string  $sContent  html content inside
     * @return string
     */
    public function addWrapper(string $sTag, array $aOptions, string $sContent): string
    {
        $aOptions['label'] = $sContent;
        return $this->_tag($sTag, $aOptions) . PHP_EOL;
    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: NAVIGATION
    // 
    // ----------------------------------------------------------------------

    /** 
     * get a single navigation item on top bar
     * 
     * @param  array    $aLink   Array of html attributes; key children can contain subitems
     * @param  integer  $iLevel  Menu level; 1=top bar; 2=pupup menu with subitems
     * @return string
     */
    public function getNavItem(array $aLink, int $iLevel = 1): string
    {
        static $iCounter;
        if (!isset($iCounter)) {
            $iCounter = 0;
        }

        switch ($aLink['label']) {
            // special menu entry: horizontal bar (label is "-")
            case '-':
                return '<div class="dropdown-divider"></div>';

            // special menu entry: hamburger menu item (label is "=")
            case '=':
                return '<li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="fas fa-bars"></i> </a>';

            // special menu entry: hamburger menu item (label is "|")
            // requires css: .navbar-nav li.divider{border-left: 1px solid rgba(0,0,0,0.2);}
            case '|':
                return '<li class="divider"></li>';
        }

        $aChildren = isset($aLink['children']) && is_array($aLink['children']) && count($aLink['children']) ? $aLink['children'] : false;

        $aLink['class'] = 'nav-link'
            . (isset($aLink['class']) ? ' ' . $aLink['class'] : '')
            . ($aChildren ? ' dropdown-toggle' : '')
        ;
        if ($aChildren) {
            $iCounter++;
            $sNavId = "navbarDropdown" . $iCounter;
            $aLink = array_merge($aLink, [
                'id' => $sNavId,
                'role' => "button",
                'data-toggle' => "dropdown",
                'aria-haspopup' => "true",
                'aria-expanded' => "false",
            ]);
            unset($aLink['children']); // remove from html attributes to draw
        }
        $sReturn = $this->_tag('a', $aLink) . "\n";
        if ($aChildren) {
            $iLevel++;
            $sReturn .= $this->addWrapper(
                'div',
                [
                    'aria-labelledby' => $sNavId,
                    'class' => 'dropdown-menu'
                ],
                $this->getNavItems($aChildren, $iLevel)
            ) . "\n";
            $iLevel--;
        }

        if ($iLevel == 1) {
            $sLiClass = 'nav-item' . ($aChildren ? ' dropdown' : '');
            $sReturn = $this->addWrapper(
                'li',
                ['class' => $sLiClass],
                $sReturn
            );
        }
        return $sReturn;
    }

    /** 
     * get html code for navigation on top bar
     * 
     * @param array $aLinks  array of navigation items
     * @param int   $iLevel  current navigation level; default: 1
     * @return string|bool
     */
    public function getNavItems(array $aLinks, int $iLevel = 1): string|bool
    {
        $sReturn = '';
        if (!$aLinks || !is_array($aLinks) || !count($aLinks)) {
            return false;
        }
        foreach ($aLinks as $aLink) {
            $sReturn .= $this->getNavItem($aLink, $iLevel);
        }
        return $sReturn;
    }

    /**
     * get a top left navigation for a top navigation bar
     * 
     * @example
     * <code>
     * $aTopnav=[
     *     ['href'=>'/index.php', 'label'=>'MyHome' , 'class'=>'bg-gray'],
     *     ['href'=>'#',          'label'=>'Contact'],
     *     ['href'=>'#',          'label'=>'Help',
     *         'children'=>[
     *             ['href'=>'#',      'label'=>'FAQ'],
     *             ['label'=>'-'],
     *             ['href'=>'#',      'label'=>'Support'],
     *         ]
     *     ]
     * ];
     *  echo '<nav class="main-header navbar navbar-expand navbar-white navbar-light">'
     *     .$renderAdminLTE->getTopNavigation($aTopnav)
     *     .'</nav>'
     * </code>
     * 
     * @param  array  $aNavItems        array of navigation items/ tree
     * @param  array  $aUlOptions       array of html attrubutes for wrapping UL tag
     * @param  array  $aNavItemsRight   array of html attrubutes for wrapping UL tag
     * @param  array  $aUlOptionsRight  array of html attrubutes for wrapping UL tag
     * @return string
     */
    public function getTopNavigation(array $aNavItems, array $aUlOptions = [], array $aNavItemsRight = [], array $aUlOptionsRight = []): string
    {
        // array_unshift($aNavItems, ['class'=>'nav-link', 'data-widget'=>'pushmenu', 'href'=>'#', 'role'=>'button', 'label'=>'<i class="fa-solid fa-bars"></i>']);
        $aUlOptLeft = count($aUlOptions) ? $aUlOptions : ['class' => 'navbar-nav'];
        $aUlOptRight = count($aUlOptionsRight) ? $aUlOptionsRight : ['class' => 'navbar-nav ml-auto'];
        return $this->addWrapper('ul', $aUlOptLeft, $this->getNavItems($aNavItems))
            . (count($aNavItemsRight)
                ? $this->addWrapper('ul', $aUlOptRight, $this->getNavItems($aNavItemsRight))
                : ''
            )
        ;
    }

    // ----------------------------------------------------------------------


    /** 
     * Get a navigation items for sidebar
     * Links can be nested with the key "children".
     * 
     * Remark: for a horizontal line ($aLink['label']='-') this css is required
     *   .nav-item hr{color: #505860; border-top: 1px solid; height: 1px; padding: 0; margin: 0; }
     * 
     * @param  array  $aLinks  list of link items
     * @return string|bool
     */
    public function getNavi2Items(array $aLinks): string|bool
    {
        $sReturn = '';
        if (!$aLinks || !is_array($aLinks) || !count($aLinks)) {
            return false;
        }

        foreach ($aLinks as $aLink) {

            if ($aLink['label'] == '-') {
                // TODO: draw a nice line
                $sReturn .= '<li class="nav-item"><hr></li>';
                continue;
            }
            // to render active or open links: 
            $aLink['class'] = 'nav-link' . (isset($aLink['class']) ? ' ' . $aLink['class'] : '');

            $aChildren = isset($aLink['children']) ? $aLink['children'] : false;
            $aLiClass = 'nav-item' . ($aChildren && strstr($aLink['class'], 'active') ? ' menu-open' : '');
            $sSubmenu = '';
            if ($aChildren) {
                unset($aLink['children']);
                $aLink['label'] .= '<i class="right fa-solid fa-angle-left"></i>';
                $sSubmenu .= $this->getSidebarNavigation($aChildren, ['class' => 'nav nav-treeview']);
            }
            $aLink['label'] = $this->addWrapper('p', [], $aLink['label']);
            $sReturn .= $this->addWrapper(
                'li',
                ['class' => $aLiClass],
                $this->_tag('a', $aLink) . $sSubmenu
            ) . "\n";
        }
        return $sReturn;
    }

    /**
     * get html code for sidebar navigation
     * 
     * @param  array  $aNavItems   navigation item
     * @param  array  $aUlOptions  aatributes for UL tag
     * @param string
     */
    public function getSidebarNavigation(
        array $aNavItems,
        array $aUlOptions = [
            'class' => 'nav sidebar-menu nav-flat__ flex-column',
            'data-widget' => 'treeview',
            'role' => 'menu',
            'data-accordion' => 'false'
        ]
    ): string {
        return $this->addWrapper(
            'ul',
            $aUlOptions,
            $this->getNavi2Items($aNavItems)
        ) . "\n";
    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: CONTENT - BASIC FUNCTIONS
    // 
    // ----------------------------------------------------------------------

    /**
     * add page row
     * 
     * @param  string  $sContent  html content inside
     * @return string
     */
    public function addRow(string $sContent): string
    {
        return $this->addWrapper('div', ['class' => 'row'], $sContent);
    }

    /**
     * add page column
     * 
     * @param  string   $sContent  html content inside
     * @param  integer  $iCols     column width; 12 = full width
     * @param  string   $sFloat    css value for float attribute; default=false
     * @return string
     */
    public function addCol(string $sContent, int $iCols = 6, string $sFloat = ''): string
    {
        $aAttr = ['class' => "col-md-$iCols"];
        if ($sFloat) {
            $aAttr['style'] = "float: $sFloat";
        }
        return $this->addWrapper('div', $aAttr, $sContent);
    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: CONTENT - WIDGET HELPERS
    // 
    // ----------------------------------------------------------------------

    /**
     * get a list of all defined components that can be rendered
     * @param   bool  $bSendData  flag: send including subkeys of the hash; default: false (keys only)
     * @return  array
     */
    public function getComponents(bool $bSendData = false): array
    {
        return $bSendData
            ? $this->_aElements
            : array_keys($this->_aElements)
        ;
    }

    /**
     * get data of a component
     * @param  string  $sComponent  id of the component
     * @return array|bool
     */
    public function getComponent(string $sComponent): array|bool
    {
        if (!isset($this->_aElements[$sComponent])) {
            return false;
        }
        $aReturn = array_merge(['id' => $sComponent], $this->_aElements[$sComponent]);
        unset($aReturn['params']);
        return $aReturn;
    }

    /**
     * get parameter keys of a component
     * @param  string  $sComponent  id of the component
     * @param  bool    $bSendData   flag: send including subkeys of the hash; default: false (keys only)
     * @return array|bool
     */
    public function getComponentParamkeys(string $sComponent, bool $bSendData = false)
    {
        if (!isset($this->_aElements[$sComponent])) {
            return false;
        }
        $aKeys = array_keys($this->_aElements[$sComponent]['params']);
        if (!$bSendData) {
            return $aKeys;
        }
        $aReturn = [];
        foreach ($aKeys as $sKey) {
            $aReturn[$sKey] = $this->getComponentParamkey($sComponent, $sKey);
        }
        // $aReturn=$this->_aElements[$sComponent]['params'];
        return $aReturn;
    }

    /**
     * get information for a parameter key of a given component
     * @param  string  $sComponent  id of the component
     * @param  string  $sKey        key in the options array
     * @return array|bool
     */
    public function getComponentParamkey(string $sComponent, string $sKey): array|bool
    {
        if (!isset($this->_aElements[$sComponent]['params'][$sKey])) {
            return false;
        }
        $aReturn = $this->_aElements[$sComponent]['params'][$sKey];
        // get description from a preset
        if (!isset($aReturn['description']) && isset($aReturn['select']['description'])) {
            $aReturn['description'] = $aReturn['select']['description'];
        }
        if (!isset($aReturn['group']) && isset($aReturn['select']['group'])) {
            $aReturn['group'] = $aReturn['select']['group'];
        }
        return $aReturn;
    }

    /**
     * get a flat list of valid parameters for a key in a component
     * @param  string  $sComponent  id of the component
     * @param  string  $sKey        key in the options array
     * @return array|bool
     */
    public function getValidParamValues(string $sComponent, string $sKey): array|bool
    {
        $aOptionkey = $this->getComponentParamkey($sComponent, $sKey);
        if (!$aOptionkey || !isset($aOptionkey['select']['values'])) {
            return false;
        }
        return array_keys($aOptionkey['select']['values']);
    }


    // ----------------------------------------------------------------------

    /**
     * helper: add a css value with prefix
     * this handles option keys in get[COMPONENT] methods
     * if a value is set then this function returns a space + prefix (param 2) + value
     * @param  string  $sValue   option value
     * @param  string  $sPrefix  prefix in front of css value
     * @return string
     */
    protected function _addClassValue(string $sValue, string $sPrefix = ''): string
    {
        return $sValue ? " $sPrefix$sValue" : '';
    }

    /**
     * helper function for get[COMPONENTNAME] methods:
     * ensure that all wanted keys exist in an array. Non existing keys will 
     * be added with value false
     * 
     * @param string $sComponent    id of the component
     * @param array  $aOptions      hash with keys for all options
     * @return array
     */
    protected function _ensureOptions(string $sComponent, array $aOptions = []): array
    {

        $aParams = $this->getComponentParamkeys($sComponent, 0);
        if (!$aParams) {
            $aOptions['_infos'][] = "Warning: no definition was found for component $sComponent.";
            return $aOptions;
        }
        foreach ($aParams as $sKey) {
            if (!isset($aOptions) || !isset($aOptions[$sKey])) {
                $aOptions[$sKey] = false;
                if (!isset($aOptions['_infos'])) {
                    $aOptions['_infos'] = [];
                }
                $aOptions['_infos'][] = "added missing key: $sKey";
            }

            // $aParamdata
            $aValidvalues = $this->getValidParamValues($sComponent, $sKey);
            if ($aValidvalues) {
                if (array_search($aOptions[$sKey], $aValidvalues) === false) {
                    echo "ERROR: [" . $sComponent . "] value &quot;" . $aOptions[$sKey] . "&quot; is not a valid for param key [" . $sKey . "]; it must be one of " . implode("|", $aValidvalues) . '<br>';
                }
            }

            // $this->_checkValue($sKey, $aOptions[$sKey], __METHOD__);
        }
        // echo '<pre>' . print_r($aOptions, 1) . '</pre>';
        return $aOptions;
    }
    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: CONTENT - WIDGETS
    // 
    // ----------------------------------------------------------------------

    /**
     * return a alert box      
     * https://adminlte.io/themes/v3/pages/UI/general.html
     * 
     * @param array $aOptions  hash with keys for all options
     *                          - type - one of [none]|danger|info|primary|success|warning
     *                          - dismissible - if dismissible - one of true|false; default: false
     *                          - title
     *                          - text
     * @return string
     */
    public function getAlert(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('alert', $aOptions);
        $aAlertIcons = [
            'danger' => 'icon fa-solid fa-ban',
            'info' => 'icon fa-solid fa-info',
            'warning' => 'icon fa-solid fa-exclamation-triangle',
            'success' => 'icon fa-solid fa-check',
        ];

        $aElement = [
            'class' => 'alert'
                . $this->_addClassValue($aOptions['type'], 'alert-')
                . $this->_addClassValue($aOptions['dismissible'], 'alert-')
            ,
            'label' => ''
                . ($aOptions['dismissible'] ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' : '')
                . $this->_tag('h5', [
                    'label' => ''
                        . (isset($aAlertIcons[$aOptions['type']]) ? '<i class="' . $aAlertIcons[$aOptions['type']] . '"></i> ' : '')
                        . $aOptions['title']
                ])
                . $aOptions['text']
        ];

        return $this->_tag('div', $aElement);
    }

    /**
     * get html code for a badge
     * Badges can be used as part of links or buttons to provide a counter.
     * https://getbootstrap.com/docs/5.3/components/badge/
     * 
     * INFO: AdminLTE v4: "bgcolor" was removed
     * 
     * css classes for rounded or round pill:
     * - rounded-pill
     * - rounded-circle
     * 
     * Examples
     * <span class="badge text-bg-primary">Primary</span>
     * <span class="badge text-bg-secondary">Secondary</span>
     * <span class="badge text-bg-success">Success</span>
     * 
     * @param array $aOptions  hash with keys for all options
     *                          - class   - css class; eg position-absolute top-0 start-100 translate-middle badge rounded-pill
     *                          - id      - optional: id attribute
     *                          - text    - visible text
     *                          - title   - optional: title attribute
     *                          - type    - one of [none]|danger|dark|info|primary|secondary|success|warning
     * @return string
     */
    public function getBadge(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('badge', $aOptions);
        $aElement = [];
        $aElement['class'] = 'badge'
            . $this->_addClassValue($aOptions['class'], '')
            . $this->_addClassValue($aOptions['type'], 'text-bg-')
        ;
        if ($aOptions['id']) {
            $aElement['id'] = $aOptions['id'];
        }
        $aElement['title'] = $aOptions['title'];
        $aElement['label'] = $aOptions['text'];

        return $this->_tag('span', $aElement);
    }

    /**
     * Get a button.
     * You can use any other key that are not named here. Those keys will be rendered 
     * as additional html attributes without modification.
     * https://marlonluan.github.io/adminlte4/dist/pages/UI/general.html
     * https://getbootstrap.com/docs/5.3/components/buttons/
     * 
     * @example
     * <button type="button" class="btn btn-primary mb-2">Primary</button>
     * 
     * @param array $aOptions  hash with keys for all options
     *                          - type  - one of [none]|danger|dark|info|primary|secondary|success|warning
     *                          - size  - one of [none]|lg|sm|xs|flat
     *                          - class - any css class for customizing, eg. "disabled"
     *                          - icon  - not supported yet
     *                          - text  - text on button
     * @return string
     */
    public function getButton(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('button', $aOptions);
        $aElement = $aOptions;
        $aElement['class'] = 'btn'
            . $this->_addClassValue($aOptions['type'], 'btn-')
            . $this->_addClassValue($aOptions['size'], 'btn-')
            . $this->_addClassValue($aOptions['class'], '')
        ;
        $aElement['label'] = $aOptions['text'] ? $aOptions['text'] : '&nbsp;';
        foreach (['_infos', 'type', 'size', 'icon', 'text'] as $sDeleteKey) {
            unset($aElement[$sDeleteKey]);
        }
        return $this->_tag('button', $aElement);
    }

    /**
     * get a callout (box with coloered left border; has type, title + text)
     * https://adminlte.io/themes/v3/pages/UI/general.html
     * 
     * @param array $aOptions  hash with keys for all options
     *                        >> styling
     *                          - type    - one of [none]|danger|dark|info|primary|secondary|success|warning
     *                          - class   - optional css class
     *                          - shadow  - size of shadow; one of [none] (=default: between small and regular)|none|small|regular|large     * 
     *                        >> texts/ html content
     *                          - title   - text: title of the card
     *                          - text    - text: content of the card
     * @return string
     */
    public function getCallout(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('callout', $aOptions);
        $sClass = 'callout'
            . $this->_addClassValue($aOptions['type'], 'callout-')
            . $this->_addClassValue($aOptions['class'], '')
            . ($aOptions['shadow'] && isset($this->_aValueMappings['shadow'][$aOptions['shadow']])
                ? ' ' . $this->_aValueMappings['shadow'][$aOptions['shadow']] : '')
        ;

        return $this->addWrapper(
            'div',
            ['class' => $sClass],
            ($aOptions['title'] ? $this->_tag('h5', ['label' => $aOptions['title']]) : '')
            . ($aOptions['text'] ? $this->_tag('p', ['label' => $aOptions['text']]) : '')
        );
    }

    /**
     * get a card
     * https://adminlte.io/docs/3.2/components/cards.html
     * https://adminlte.io/docs/3.2/javascript/card-widget.html
     * 
     * @param array $aOptions  hash with keys for all options
     *                        >> styling
     *                          - variant: "default"  - titlebar is colored
     *                                     "outline"  - a small stripe on top border is colored
     *                                     "solid"    - whole card is colored
     *                                     "gradient" - whole card is colored with a gradient
     *                          - type    - one of [none]|danger|dark|info|primary|secondary|success|warning
     *                          - shadow  - size of shadow; one of [none] (=default: between small and regular)|none|small|regular|large 
     *                          - class   - any css class for customizing, eg. "disabled"
     *                          - state   - one of [none]|collapsed|maximized
     * 
     *                        >> toolbar icons - set to true to show it; default: none of it is visible
     *                         - tb-collapse
     *                         - tb-expand    it is added automatically if you set 'state'=>'collapsed'
     *                         - tb-maximize 
     *                         - tb-minimize  it is added automatically if you set 'state'=>'maximized'
     *                         - tb-remove
     * 
     *                        >> texts/ html content
     *                          - title   - text: title of the card
     *                          - tools   - text: titlebar top right elements
     *                          - text    - text: content of the card
     *                          - footer  - text: footer of the card
     * @return string
     */
    public function getCard(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('card', $aOptions);
        // css class prefixes based on "variant" value
        $aVariants = [
            'default' => 'card-',
            'outline' => 'card-outline card-',
            'solid' => 'bg-',
            'gradient' => 'bg-gradient-',
        ];

        // window states: css class and toolbar buttons to add
        $aStates = [
            'collapsed' => ['class' => 'collapsed-card', 'tool' => 'tb-expand'],
            'maximized' => ['class' => 'maximized-card', 'tool' => 'tb-minimize'],
        ];
        $aTools = [
            'tb-collapse' => '<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fa-solid fa-minus"></i></button>',
            'tb-expand' => '<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fa-solid fa-plus"></i></button>',

            'tb-maximize' => '<button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fa-solid fa-expand"></i></button>',
            'tb-minimize' => '<button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fa-solid fa-compress"></i></button>',

            'tb-remove' => '<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fa-solid fa-times"></i></button>',
        ];

        // print_r($aOptions);

        $sVariantPrefix = isset($aVariants[$aOptions['variant']]) ? $aVariants[$aOptions['variant']] : $aVariants['default'];
        $sClass = 'card'
            . $this->_addClassValue($aOptions['type'], $sVariantPrefix)
            . ($aOptions['shadow'] && isset($this->_aValueMappings['shadow'][$aOptions['shadow']])
                ? ' ' . $this->_aValueMappings['shadow'][$aOptions['shadow']] : '')
            . $this->_addClassValue($aOptions['class'], '')
        ;

        // check window state
        foreach ($aStates as $sStatus => $aStatus) {
            if ($aOptions['state'] === $sStatus) {
                $sClass .= ' ' . $aStatus['class'];
                $aOptions[$aStatus['tool']] = 1;
            }
        }

        // add toolbar buttons - from given options or by window state
        foreach ($aTools as $sTool => $sHtml) {
            $aOptions['tools'] .= ($aOptions[$sTool] ? $sHtml : '');
        }
        // build parts of the card
        $sCardHeader = $aOptions['title']
            ? $this->addWrapper(
                'div',
                ['class' => 'card-header'],
                $this->_tag('h3', ['class' => 'card-title', 'label' => $aOptions['title']])
                . ($aOptions['tools'] ? $this->_tag('div', ['class' => 'card-tools', 'label' => $aOptions['tools']]) : '')
            )
            : ''
        ;

        $sCardBody = $this->_tag('div', ['class' => 'card-body', 'label' => $aOptions['text']]);
        $sCardFooter = $aOptions['footer'] ? $this->_tag('div', ['class' => 'card-footer', 'label' => $aOptions['footer']]) : '';

        // merge all
        return $this->addWrapper('div', ['class' => $sClass], $sCardHeader . $sCardBody . $sCardFooter);
    }


    /**
     * return an info-box:
     * A colored box with large icon, text and a value.
     * https://adminlte.io/docs/3.2/components/boxes.html
     * 
     * @param array $aOptions  hash with keys for all options
     *                        styling:
     *                          - type    - color of the box; one of [none]|danger|dark|info|primary|secondary|success|warning
     *                          - iconbg  - background color or type of icon; use it for default type (type="")
     *                          - shadow  - size of shadow; one of [none] (=default: between small and regular)|none|small|regular|large 
     * 
     *                        content
     *                          - icon    - icon class
     *                          - text    - information text
     *                          - number  - value (comes in bold text)
     *                          - progressvalue - integer: progress bar; range: 0..100
     *                          - progresstext  - text below progress bar
     * @return string
     */
    public function getInfobox(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('infobox', $aOptions);

        // print_r($aOptions);
        $sClass = 'info-box'
            . $this->_addClassValue($aOptions['type'], 'bg-')
            . $this->_addClassValue($aOptions['class'], '')
            . ($aOptions['shadow'] && isset($this->_aValueMappings['shadow'][$aOptions['shadow']])
                ? ' ' . $this->_aValueMappings['shadow'][$aOptions['shadow']] : '')
        ;

        // build parts
        $sIcon = $aOptions['icon']
            ? $this->addWrapper("span", [
                'class' => 'info-box-icon' . ($aOptions['iconbg'] ? ' bg-' . $aOptions['iconbg'] : '')
            ], $this->_tag('i', ['class' => $aOptions['icon']]))
            : ''
        ;
        $sContent = $this->addWrapper(
            "div",
            ['class' => 'info-box-content'],
            ''
            . ($aOptions['text'] ? $this->_tag('span', ['class' => 'info-box-text', 'label' => $aOptions['text']]) : '')
            . ($aOptions['number'] ? $this->_tag('span', ['class' => 'info-box-number', 'label' => $aOptions['number']]) : '')
            . ($aOptions['progressvalue'] !== false && $aOptions['progressvalue'] !== ''
                ? $this->addWrapper(
                    'div',
                    ['class' => 'progress'],
                    $this->_tag('div', ['class' => 'progress-bar' . ($aOptions['iconbg'] ? ' bg-' . $aOptions['iconbg'] : ''), 'style' => 'width: ' . (int) $aOptions['progressvalue'] . '%'])
                )
                . ($aOptions['progresstext'] ? $this->_tag('span', ['class' => 'progress-description', 'label' => $aOptions['progresstext']]) : '')
                : ''
            )
        );

        // merge all
        return $this->_tag('div', ['class' => $sClass], $sIcon . $sContent);
    }


    /**
     * return a small box:
     * A colored box with large icon, text and a value.
     * https://adminlte.io/docs/3.2/components/boxes.html
     * https://adminlte.io/themes/v3/pages/widgets.html
     * 
     * @param array $aOptions  hash with keys for all options
     *                        styling:
     *                          - type    - color of the box; one of [none]|danger|dark|info|primary|secondary|success|warning
     *                          - shadow  - size of shadow; one of [none] (=default: between small and regular)|none|small|regular|large 
     *                        content
     *                          - icon    - icon class for icon on the right
     *                          - text    - information text
     *                          - number  - value (comes in bold text)
     *                          - url     - integer: progress bar; range: 0..100
     *                          - linktext- text below progress bar
     * @return string
     */
    public function getSmallbox(array $aOptions): string
    {
        $aOptions = $this->_ensureOptions('smallbox', $aOptions);
        // print_r($aOptions);
        $sClass = 'small-box'
            . $this->_addClassValue($aOptions['type'], 'bg-')
            . $this->_addClassValue($aOptions['class'], '')
            . ($aOptions['shadow'] && isset($this->_aValueMappings['shadow'][$aOptions['shadow']])
                ? ' ' . $this->_aValueMappings['shadow'][$aOptions['shadow']] : '')
        ;

        // build parts
        $sContent = $this->addWrapper(
            "div",
            ['class' => 'inner'],
            ''
            . ($aOptions['number'] ? $this->_tag('h3', ['label' => $aOptions['number']]) : '')
            . ($aOptions['text'] ? $this->_tag('p', ['class' => 'info-box-text', 'label' => $aOptions['text']]) : '')
        );
        $sIcon = $aOptions['icon']
            ? $this->addWrapper(
                "div",
                ['class' => 'icon'],
                $this->_tag('i', ['class' => $aOptions['icon']])
            )
            : ''
        ;
        $sFooter = ($aOptions['url']
            ? $this->addWrapper(
                "a",
                [
                    'class' => 'small-box-footer',
                    'href' => $aOptions['url'],
                ],
                ''
                . ($aOptions['linktext'] ? $aOptions['linktext'] : $aOptions['url'])
                . ' '
                . $this->_tag('i', ['class' => 'fa-solid fa-arrow-circle-right'])
            )
            : ''
        );

        // merge all
        return $this->_tag('div', ['class' => $sClass], $sContent . $sIcon . $sFooter);
    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: CONTENT - FORM
    // https://getbootstrap.com/docs/5.0/forms/overview/
    // 
    // ----------------------------------------------------------------------


    /**
     * Generates a horizontal form element with a label, input, and optional hint.
     *
     * @param string $sInput The HTML input element to be rendered.
     * @param string $sLabel The label for the input element.
     * @param string $sId The ID attribute for the label and input elements.
     * @param string $sHint An optional hint to be displayed below the input element.
     * @return string The generated HTML for the horizontal form element.
     */
    public function getHorizontalFormElement(string $sInput, string $sLabel = '', string $sId = '', string $sHint = ''): string
    {
        return '<div class="row mb-3">'
            . '<label for="' . $sId . '" class="col-sm-2 col-form-label">' . $sLabel . '</label>'
            . '<div class="col-sm-10">'
            . ($sHint
                ? '<div class="text-navy hint">' . $sHint . '</div>'
                : '')
            . $sInput
            . '</div>'
            . '</div>'
        ;
    }

    /**
     * return a text input field:
     * https://adminlte.io/themes/v3/pages/forms/general.html
     * 
     * @param array $aOptions  hash with keys for all options
     *                        styling:
     *                          - type    - field type: text, email, password, hidden and all other html 5 input types
     *                        content
     *                          - label   - label tag
     *                          - name    - name attribute for sending form
     *                          - value   - value in field
     *                        more:
     *                          - hint    - hint to be displayed above the field
     *                                      If not set, no hint is displayed.
     *                                      css for ".row .hint" to customize look and feel
     * @return string
     */
    public function GetFormInput(array $aOptions): string
    {
        // $aOptions=$this->_ensureOptions('input', $aOptions);
        $aElement = $aOptions;
        $aElement['class'] = ''
            . 'form-control '
            . (isset($aOptions['class']) ? $aOptions['class'] : '')
        ;
        $sFormid = (isset($aOptions['id'])
            ? $aOptions['id']
            : (isset($aOptions['name']) ? $aOptions['name'] : 'field') . '-' . md5(microtime(true))
        );
        $aElement['id'] = $sFormid;

        $sLabel = isset($aOptions['label']) ? $aOptions['label'] : '';
        if ($aOptions['required']??false) {
            $sLabel .= ' <span class="required">*</span>';
        }

        $sHint = isset($aOptions['hint']) ? $aOptions['hint'] : '';
        $sPrepend = '';
        $sAppend = '';


        if (isset($aOptions['prepend']) && $aOptions['prepend']) {
            $sWrapperclass = 'input-group';
            $sPrepend = $this->_tag(
                'div',
                ['class' => 'input-group-prepend'],
                $this->_tag('span', ['class' => 'input-group-text'], $aOptions['prepend'])
            );
        }
        if (isset($aOptions['append']) && $aOptions['append']) {
            $sWrapperclass = 'input-group';
            $sAppend = $this->_tag(
                'div',
                ['class' => 'input-group-append'],
                $this->_tag('span', ['class' => 'input-group-text'], $aOptions['append'])
            );
        }

        foreach (['_infos', 'label', 'append', 'prepend', 'debug'] as $sDeleteKey) {
            if (isset($aElement[$sDeleteKey])) {
                unset($aElement[$sDeleteKey]);
            }
        }

        // return data

        switch ($aElement['type']) {
            case 'checkbox':
            case 'radio':
                $aElement['class'] = str_replace('form-control ', 'form-check-input', $aElement['class']);
                $aElement['title'] = $aElement['title'] ?? $sHint;
                return $this->_tag(
                    'div',
                    ['class' => 'form-check'],
                    $this->_tag('input', $aElement, '', false) . $this->_tag('label', ['for' => $sFormid, 'label' => $sLabel], '')
                );

            case 'hidden':
                $aElement['title'] ??= $sHint;
                return $this->_tag('input', $aElement, '', false);

            case 'range':
                $aElement['class'] = str_replace('form-control ', 'form-range', $aElement['class']);
                $aElement['title'] ??= $sHint;
            default:
                return $this->getHorizontalFormElement(
                    $sPrepend . $this->_tag('input', $aElement, '', false) . $sAppend,
                    $sLabel,
                    $sFormid,
                    $sHint
                );
        }
    }

    /**
     * return a textarea field .. or html editor using summernote
     * @param array $aOptions  hash with keys for all options
     *                        styling:
     *                          - type    - field type: [none]|html
     *                        content
     *                          - label   - label tag
     *                          - name    - name attribute for sending form
     *                          - value   - value in 
     *                        more:
     *                          - hint    - hint to be displayed above the field
     *                                      If not set, no hint is displayed.
     *                                      css for ".row .hint" to customize look and feel
     * @return string
     */
    public function getFormTextarea(array $aOptions): string
    {
        // $aOptions=$this->_ensureOptions('textarea', $aOptions);
        $aElement = $aOptions;
        $aElement['class'] = ''
            . 'form-control '
            . ((isset($aOptions['type']) && $aOptions['type'] == 'html') ? 'summernote ' : '')
            . ($aOptions['class'] ?? '')
        ;
        $sFormid = (isset($aOptions['id'])
            ? $aOptions['id']
            : ($aOptions['name'] ?? 'field') . '-' . md5(microtime(true))
        );
        $sLabel = isset($aOptions['label']) ? $aOptions['label'] : '';
        $sHint = isset($aOptions['hint']) ? $aOptions['hint'] : '';
        $aElement['id'] = $sFormid;

        $value = $aOptions['value'] ?? '';

        foreach (['_infos', 'label', 'debug', 'type', 'value'] as $sDeleteKey) {
            if (isset($aElement[$sDeleteKey])) {
                unset($aElement[$sDeleteKey]);
            }
        }
        return $this->getHorizontalFormElement(
            $this->_tag('textarea', $aElement, $value),
            $sLabel,
            $sFormid,
            $sHint
        );

    }

    /**
     * return a select box field
     * @param array $aOptions  hash with keys for all options
     *                        option fields
     *                          - options - array of options with keys per item:
     *                              - value   - value in the option
     *                              - label   - visible text in the option
     *                              other keys are attributes in the option
     *                        styling:
     *                          - bootstrap-select  - set true to enable select
     *                                      box with bootstrap-select and
     *                                      live search
     *                          - class   - css class
     *                        select tag
     *                          - label   - label tag
     *                          - name    - name attribute for sending form
     *                          other keys are attributes in the select
     *                        more:
     *                          - hint    - hint to be displayed above the field
     *                                      If not set, no hint is displayed.
     *                                      css for ".row .hint" to customize look and feel
     * @return string
     */
    public function getFormSelect(array $aOptions): string
    {
        $aElement = $aOptions;
        $aElement['class'] = ''
            . 'form-control '
            . (isset($aOptions['class']) ? $aOptions['class'] . ' ' : '')
            . (isset($aOptions['bootstrap-select']) ? 'selectpicker ' : '') //$aOptions
        ;
        if (isset($aOptions['bootstrap-select']) && $aOptions['bootstrap-select']) {
            $aElement['data-live-search'] = "true";
        }

        $sFormid = (isset($aOptions['id'])

            ? $aOptions['id']
            : (isset($aOptions['name']) ? $aOptions['name'] : 'field') . '-' . md5(microtime(true))
        );
        $aElement['id'] = $sFormid;
        $sLabel = isset($aOptions['label']) ? $aOptions['label'] : '';
        $sHint = $aOptions['hint'] ?? '';

        $sOptionTags = '';
        foreach ($aOptions['options'] as $aField) {
            $optionText = $aField['label'];
            unset($aField['label']);
            $sOptionTags .= $this->_tag('option', $aField, $optionText) . "\n";
        }

        foreach (['_infos', 'label', 'debug', 'type', 'value', 'options'] as $sDeleteKey) {
            if (isset($aElement[$sDeleteKey])) {
                unset($aElement[$sDeleteKey]);
            }
        }
        return $this->getHorizontalFormElement(
            $this->_tag(
                'div',
                ['class' => 'form-group'],
                $this->_tag('select', $aElement, $sOptionTags)
            ),
            $sLabel,
            $sFormid,
            $sHint
        );

    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: CONTENT - TABBED CONTENT
    // 
    // ----------------------------------------------------------------------

    /**
     * return a box with tabbed content
     * @param array $aOptions  hash with keys for all options
     *                           - tabs {array} key=tab label; value=content
     * @param bool  $asArray   optional flag: return hash with keys or as string
     * @return bool|string|array
     */
    public function getTabbedContent(array $aOptions, bool $asArray = false): bool|string|array
    {
        static $iTabCounter;
        if (!isset($aOptions['tabs']) || !is_array($aOptions['tabs'])) {
            return false;
        }
        if (!isset($iTabCounter)) {
            $iTabCounter = 1;
        } else {
            $iTabCounter++;
        }

        $id = "tab-content-$iTabCounter";
        $iCounter = 0;

        $sTabs = '';
        $sContent = '';
        foreach ($aOptions['tabs'] as $sLabel => $sTabContent) {
            $iCounter++;
            $sTabId = "$id-tabitem-$iCounter-tab";
            $sContentId = "$id-tabitem-$iCounter-content";

            $sTabs .= $this->_tag(
                'li',
                ['class' => 'nav-item'],
                $this->_tag(
                    'a',
                    [
                        'class' => 'nav-link' . ($iCounter == 1 ? ' active' : ''),
                        'id' => $sTabId,
                        'data-toggle' => 'tab',
                        'href' => "#$sContentId",
                        'role' => 'tab',
                        'aria-controls' => 'custom-tabs-one-profile',
                        'aria-selected' => ($iCounter == 1 ? true : false),
                    ],
                    $sLabel
                )
            );
            $sContent .= $this->_tag('div', [
                'class' => 'tab-pane fade' . ($iCounter == 1 ? ' active show' : ''),
                'id' => $sContentId,
                'role' => 'tabpanel',
                'aria-labelledby' => $sTabId,
            ], $sTabContent);
        }
        $sTabs = $this->_tag('ul', ['class' => 'nav nav-tabs', 'role' => 'tablist'], $sTabs);
        $sContent = $this->_tag('div', ['class' => 'tab-content'], $sContent);

        return $asArray
            ? ['tabs' => $sTabs, 'content' => $sContent]
            : "$sTabs$sContent"
        ;
    }

    // ----------------------------------------------------------------------
    // 
    // PUBLIC FUNCTIONS :: CONTENT - Bootstrap 5
    // 
    // ----------------------------------------------------------------------

    /**
     * Get html for bootstrap 5 accordeon
     * https://getbootstrap.com/docs/5.3/components/accordion/
     * 
     * It returns false if argument has no key "tabs"
     * 
     * @param array $aOptions  hash with keys for all options
     *                           - tabs {array} key=tab label; value=content
     * @return bool|string
     */
    public function getBsAccordeon(array $aOptions): bool|string
    {
        static $iTabCounter;
        if (!isset($aOptions['tabs']) || !is_array($aOptions['tabs'])) {
            return false;
        }
        if (!isset($iTabCounter)) {
            $iTabCounter = 1;
        } else {
            $iTabCounter++;
        }

        $sContent = '';
        $iContentCounter = 0;
        $sAccordeonId = "accordeon-$iTabCounter";

        foreach ($aOptions['tabs'] as $sLabel => $sTabContent) {

            $iContentCounter++;
            $sContentId = "accordeon-$iTabCounter-item-$iContentCounter";
            $sContent .= $this->_tag(
                'div',
                [
                    'class' => 'accordion-item',
                ],

                $this->_tag(
                    'h2',
                    [
                        'class' => 'accordion-header'
                    ],
                    $this->_tag(
                        'button',
                        [
                            'class' => 'accordion-button',
                            'type' => 'button',
                            'data-bs-toggle' => 'collapse',
                            'data-bs-target' => "#$sContentId",
                            'aria-expanded' => 'true',
                            'aria-controls' => 'collapseOne',
                        ],
                        $sLabel
                    )
                )
                . $this->_tag(
                    'div',
                    [
                        'id' => $sContentId,
                        'class' => 'accordion-collapse collapse' . ($iContentCounter == 1 ? ' show' : ''),
                        'data-bs-parent' => "#$sAccordeonId",
                    ],
                    $this->_tag(
                        'div',
                        [
                            'class' => 'accordion-body'
                        ],
                        $sTabContent
                    )
                )
            );
        }

        $sContent = $this->_tag('div', ['class' => 'accordion'], $sContent);

        return $sContent;
    }

}
