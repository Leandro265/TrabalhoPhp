<?php
class Connection {
    private $manager;

    public function __construct() {
        $this->manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    }
    public function getConnection() {
        return $this->manager;
    }
}