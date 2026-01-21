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

    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['deleted'] = 0;
        
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $setClause = implode(", ", $setClause);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $data['id'] = $id;
        
        return $stmt->execute($data);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.name as company_name, c.location as company_location 
            FROM {$this->table} a 
            LEFT JOIN companies c ON a.company_id = c.id 
            WHERE a.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}