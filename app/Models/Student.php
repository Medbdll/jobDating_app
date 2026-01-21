<?php

namespace App\Models;

use App\Core\BaseModel;

class Student extends BaseModel
{
    protected $table = "students";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a user and student record in a transaction
     */
    public function createWithUser($userData, $studentData)
    {
        try {
            $this->db->beginTransaction();

            // Create user using User model
            $user = new User();
            $userId = $user->create($userData);
            
            if (!$userId) {
                throw new \Exception('Failed to create user');
            }

            // Insert student record
            $studentStmt = $this->db->prepare("
                INSERT INTO {$this->table} (user_id, promotion, specialisation) 
                VALUES (:user_id, :promotion, :specialisation)
            ");
            
            $studentStmt->execute([
                'user_id' => $userId,
                'promotion' => $studentData['promotion'],
                'specialisation' => $studentData['specialisation']
            ]);

            $this->db->commit();
            return $userId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Find student by user ID
     */
    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email, u.role 
            FROM {$this->table} s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all students with user info
     */
    public function getAllWithUsers()
    {
        $stmt = $this->db->prepare("
            SELECT s.*, u.name, u.email, u.created_at as user_created_at 
            FROM {$this->table} s 
            JOIN users u ON s.user_id = u.id 
            WHERE u.role = 'student'
            ORDER BY u.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
