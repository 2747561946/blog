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

    public function login($email,$password)
    {
        //根据email passwprod 查询数据库
        $stmt = self::$pdo->prepare("SELECT * FROm users WHERE email=? AND password=?");

        $stmt->execute([
            $email,
            $password,
           
        ]);
        $user = $stmt->fetch();
       
        if( $user )
        {
            //登录 把用户信息保存到session
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['_token'] = csrf();
            return TRUE;
            
        }
        else{
            return FALSE;
        }

    }
  
}