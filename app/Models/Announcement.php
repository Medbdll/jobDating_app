<?php

namespace App\Models;

use App\Core\BaseModel;

class Announcement extends BaseModel
{
    protected $table = "announcements";

    public function __construct()
    {
        parent::__construct();
    }

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE deleted = 0");
        return $stmt->fetchColumn();
    }

    public function countArchived()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE deleted = 1");
        return $stmt->fetchColumn();
    }

    public function getRecent($limit = 3)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.name as company_name, c.location as company_location 
            FROM {$this->table} a 
            LEFT JOIN companies c ON a.company_id = c.id 
            WHERE a.deleted = 0 
            ORDER BY a.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getArchived()
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.name as company_name, c.location as company_location 
            FROM {$this->table} a 
            LEFT JOIN companies c ON a.company_id = c.id 
            WHERE a.deleted = 1 
            ORDER BY a.updated_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}