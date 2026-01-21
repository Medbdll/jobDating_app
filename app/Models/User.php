<?php

namespace App\Models;

use App\Core\BaseModel;

class User extends BaseModel
{
    protected $table = "users";


    public function __construct()
    {
        parent::__construct();
    }

    public function getUsers()
    {
        return $this->all();
    }

    public function getUsersById()
    {
        return $this->find(1);
    }
    
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Verify password against hashed password
     * @param string $password The plain text password to verify
     * @param string $hashedPassword The hashed password from database
     * @return bool True if password matches, false otherwise
     */
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
    
    /**
     * Create a new user with hashed password
     * @param array $data User data including name, email, password, role
     * @return int|false The ID of the created user or false on failure
     */
    public function create($data)
    {
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
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
     * Create a student user (role = 'student')
     * @param array $data User data
     * @return int|false User ID or false on failure
     */
    public function createStudent($data)
    {
        $data['role'] = 'student';
        return $this->create($data);
    }
    
    /**
     * Create an admin user (role = 'admin')
     * @param array $data User data
     * @return int|false User ID or false on failure
     */
    public function createAdmin($data)
    {
        $data['role'] = 'admin';
        return $this->create($data);
    }
    
    /**
     * Count all students in the database
     * @return int Number of students
     */
    public function countStudents()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE role = 'student'");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }
}
