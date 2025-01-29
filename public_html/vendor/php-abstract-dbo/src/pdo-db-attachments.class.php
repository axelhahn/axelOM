<?php
namespace axelhahn;

require_once 'pdo-db-base.class.php';

class pdo_db_attachments extends pdo_db_base{

    protected string $_sUploadDir='';
    protected string $_sUrlBase='';

    /**
     * hash for a table
     * create database column, draw edit form
     * @var array 
     */
    protected array $_aProperties = [
        'filename'       => ['create' => 'VARCHAR(255)','overview'=>1,],
        'mime'           => ['create' => 'VARCHAR(32)',],
        'description'    => ['create' => 'VARCHAR(2048)',],
        'size'           => ['create' => 'INTEGER',],
        'width'          => ['create' => 'INTEGER',],
        'height'         => ['create' => 'INTEGER',],
    ];

    public function __construct(object $oDB)
    {
        parent::__construct(__CLASS__, $oDB);
    }

    // ----------------------------------------------------------------------
    //
    // CUSTOM METHODS
    //
    // ----------------------------------------------------------------------

    /**
     * Get array of all hook actions
     * Keys:
     *   - backend_preview   {string}  method name for preview in AxelOM
     *   - <any>             {string}  method name four your custom action
     * 
     * @return array
     */
    public function hookActions(): array
    {
        return [
            'backend_preview' => 'attachmentPreview',
            'backend_upload' => 'attachmentPreview',
        ];
    }

    /**
     * Get html code for attachment preview
     * 
     * @param array  $aOptions  array of options; known keys:
     *                          - baseurl {string}  set base url for attachments that triggers -> setUrlBase(<baseurl>)
     * 
     * @return string
     */
    public function attachmentPreview(array $aOptions = []): string
    {
        $sReturn = '';
        $aFile = $this->getItem();
        if (!$aFile) {
            $this->_log(PB_LOGLEVEL_ERROR, __METHOD__, 'Item not found. Use read(<ID>) first.');
            return $sReturn;
        }
        if($aOptions['baseurl']??false){
            $this->setUrlBase($aOptions['baseurl']);
        }

        $sAttachmentUrl=$this->_sUrlBase . '/' . $aFile['filename'];

        switch($aFile['mime']){
            // ---- audio
            case 'audio/mpeg':
            case 'audio/ogg':
            case 'audio/wav':
                $sReturn .= "<audio controls><source src=\"$sAttachmentUrl\" type=\"$aFile[mime]\"></audio>";
                break;

            // ---- images
            case 'image/gif':
            case 'image/jpeg':
            case 'image/png':
                $sReturn .= "<img src=\"$sAttachmentUrl\" alt=\"$aFile[filename]\">";
                break;

            // ---- video
            case 'video/mp4':
            case 'video/ogg':
                $sReturn .= "<video controls><source src=\"$sAttachmentUrl\" type=\"$aFile[mime]\"></video>";
                break;
        }

        $sReturn.=''
            . ($sReturn ? '<br><br>' : '')
            . '<a href="' . $sAttachmentUrl . '" target="_blank">' . basename($aFile['filename']) . '</a><br>'
            .$aFile['mime'].'<br>'
            ;

        return $sReturn ? "<div class=\"preview\">$sReturn</div>" : '';
    }

    // ----------------------------------------------------------------------
    // SETTER
    // ----------------------------------------------------------------------

    /**
     * Sets the upload directory for the file attachments.
     *
     * @param string $sDir The directory path to set as the upload directory.
     * @return bool Returns true if the directory is set successfully, false otherwise.
     */
    public function setUploadDir(string $sDir) :bool{
        if(!is_dir($sDir)){
            $this->_log(PB_LOGLEVEL_ERROR, __METHOD__ . '('.$sDir.')', 'Given directory not found.');
            return false;
        }
        $this->_sUploadDir = $sDir;
        return true;
    }

    /**
     * Sets the base URL for the file attachments
     *
     * @param string $sUrl The base URL to set.
     * @return bool
     */
    public function setUrlBase(string $sUrl) :bool {
        $this->_sUrlBase = $sUrl;
        return true;
    }

}