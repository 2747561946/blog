<?php
namespace controllers;

//引入
use models\User;
class UserController
{
    public function hello()
    {
        $user = new User;
        $name = $user->getName();

        view('users.hello', [
            'name' => $name
        ]);
    }

    public function word()
    {
        echo 'word';
    }
}