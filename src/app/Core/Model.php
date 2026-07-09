<?php
namespace App\Core;

class Model
{
    /**
     * @var \PDO
     */
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
}
