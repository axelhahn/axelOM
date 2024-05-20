<?php
namespace axelhahn;

require_once 'pdo-db-base.class.php';

class pdo_db_attachments extends pdo_db_base{

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
}