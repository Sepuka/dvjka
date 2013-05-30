<?php

class Mongo extends MongoClient
{
    const DBname        = 'dvjka';

    protected $_conn    = null;
    protected $_db      = null;

    public function __construct($accessDetails) {
        try {
            $this->_conn = parent::__construct($accessDetails);
            $this->_db = $this->selectDB(SELF::DBname);
        } catch (MongoException $ex) {
            exit($ex->getMessage());
        }
    }
}

new Mongo('mongodb://localhost:27017');