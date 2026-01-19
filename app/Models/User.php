<?php

namespace App\Models;

use App\Core\BaseModel;

class User extends BaseModel{
    protected $table = "users";


    public function __construct(){
        parent::__construct();
    }

    public function getUsers(){
        return $this->all();
    }

    public function getUsersById(){
        return $this->find(1);
    }
    
    public function findByEmail($email){
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
