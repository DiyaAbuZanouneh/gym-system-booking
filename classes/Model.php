<?php
// Base class for all models: gives them the database connection.
abstract class Model
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
