<?php
namespace models;
use PDO;
class User extends Base
{
    
   
    public function add($email,$password)
    {
        
        $stmt =  self::$pdo->prepare("INSERT INTO users (email,password) VALUES(?,?)");
        // var_dump('INSERT INTO users (email,password) VALUES(?,?)');
        return $stmt->execute([
                        $email,
                        $password,
                    ]);
    }
  
}