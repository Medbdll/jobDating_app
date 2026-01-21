<?php

namespace App\Models;

use App\Core\BaseModel;

class Company extends BaseModel
{
    protected $table = "companies";

    public function __construct()
    {
        parent::__construct();
    }

    public function countCompanies()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}