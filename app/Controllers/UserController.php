<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Security;
use App\Models\User;

class UserController extends BaseController
{
    public function show($id)
    {
        $user = new User();       
        $data = $user->find($id); 

        $this->view('user', [
            'user' => ["this user data","data 2","data 3"]
        ]);
    }

    public function index()
    {
        $this->redirect('/users');
    }




//all test

    public function allTest()
{
    $user = new User();
    $users = $user->all();

    echo "<pre>";
    print_r($users);
    echo "</pre>";
}

public function findByIdTest($id)
{
    $user = new User();
    $data = $user->find($id);

    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


//create test
    public function createTest()
{
    $user = new User();

    $user->create([
        'name' => 'Test User',
        'email' => 'test@mail.com',
        'password' => '123456'
    ]);

    echo "User success!";
}

//delet
public function deleteTest($id)
{
    $user = new User();
    $user->delete($id);

    echo "User qui a ID $id suprimé succé!";
}

//update
public function updateTest($id)
{
    $user = new User();

    $result = $user->update($id, [
        'name' => 'Updated Name',
        'email' => 'updated@mail.com'
    ]);

    if ($result) {
        echo "User qui a id $id modifié avec succé!";
    } else {
        echo "Update failed!";
    }
}


}
