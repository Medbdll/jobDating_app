<?php

namespace App\Models;

use App\Core\BaseModel;

class Application extends BaseModel
{
    protected $table = "applications";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new application
     */
    public function create($data)
    {
        $data['applied_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Check if student has already applied to announcement
     */
    public function hasApplied($studentId, $announcementId = null)
    {
        if ($announcementId) {
            // Check specific announcement
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM {$this->table} 
                WHERE student_id = :student_id AND announcement_id = :announcement_id
            ");
            $stmt->execute([
                'student_id' => $studentId,
                'announcement_id' => $announcementId
            ]);
        } else {
            // Check any application
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM {$this->table} 
                WHERE student_id = :student_id
            ");
            $stmt->execute([
                'student_id' => $studentId
            ]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get student's applications
     */
    public function getByStudent($studentId)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, ann.title, c.name as company_name, ann.location, ann.contract_type as announcement_type,
                   ann.created_at as announcement_created_at
            FROM {$this->table} a
            INNER JOIN announcements ann ON a.announcement_id = ann.id
            INNER JOIN companies c ON ann.company_id = c.id
            WHERE a.student_id = :student_id
            ORDER BY a.applied_at DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get applications for an announcement
     */
    public function getByAnnouncement($announcementId)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, s.promotion, s.specialisation, u.name as student_name, u.email as student_email
            FROM {$this->table} a
            INNER JOIN students s ON a.student_id = s.id
            INNER JOIN users u ON s.user_id = u.id
            WHERE a.announcement_id = :announcement_id
            ORDER BY a.applied_at DESC
        ");
        $stmt->execute(['announcement_id' => $announcementId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update application status
     */
    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = :status, updated_at = :updated_at 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get application by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, ann.title, c.name as company_name, ann.location, ann.contract_type as announcement_type,
                   s.promotion, s.specialisation, u.name as student_name, u.email as student_email
            FROM {$this->table} a
            INNER JOIN announcements ann ON a.announcement_id = ann.id
            INNER JOIN companies c ON ann.company_id = c.id
            INNER JOIN students s ON a.student_id = s.id
            INNER JOIN users u ON s.user_id = u.id
            WHERE a.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get student ID from user ID
     */
    public function getStudentIdByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM students WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    /**
     * Count applications by status for a specific student
     */
    public function countByStatus($status = null, $studentId = null)
    {
        if ($studentId) {
            if ($status) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) FROM {$this->table} WHERE status = :status AND student_id = :student_id
                ");
                $stmt->execute(['status' => $status, 'student_id' => $studentId]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) FROM {$this->table} WHERE student_id = :student_id
                ");
                $stmt->execute(['student_id' => $studentId]);
            }
        } else {
            if ($status) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) FROM {$this->table} WHERE status = :status
                ");
                $stmt->execute(['status' => $status]);
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
            }
        }
        return $stmt->fetchColumn();
    }

    /**
     * Get all applications with student and announcement details
     */
    public function getAllWithDetails()
    {
        $stmt = $this->db->prepare("
            SELECT a.*, ann.title as announcement_title, ann.location as announcement_location,
                   c.name as company_name, u.name as student_name, u.email as student_email,
                   s.promotion, s.specialisation
            FROM {$this->table} a
            INNER JOIN announcements ann ON a.announcement_id = ann.id
            INNER JOIN companies c ON ann.company_id = c.id
            INNER JOIN students s ON a.student_id = s.id
            INNER JOIN users u ON s.user_id = u.id
            ORDER BY a.applied_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Get recent applications with details
    
    public function getRecentApplications($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, ann.title as announcement_title, ann.location as announcement_location,
                   c.name as company_name, u.name as student_name, u.email as student_email,
                   s.promotion, s.specialisation
            FROM {$this->table} a
            INNER JOIN announcements ann ON a.announcement_id = ann.id
            INNER JOIN companies c ON ann.company_id = c.id
            INNER JOIN students s ON a.student_id = s.id
            INNER JOIN users u ON s.user_id = u.id
            ORDER BY a.applied_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
