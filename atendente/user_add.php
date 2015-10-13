<?php
require_once('../util/header.php');
require_once('includes.php');

$session = new Session();
$session->checkSession('user');

$vld = new Validation();

$vld->validate();

if($vld->hasErrors() == false){

    extract($_POST, EXTR_SKIP);

    $pdo = PDOConnection::getInstance();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE user = :user');
    $stmt->bindValue('user', $user);
    $stmt->execute();

    if($stmt->fetchColumn() > 0){

        $vld->addError('Já existe um cadastro com o usuário ('.$user.')');	

    }else{

        $user_add = array(
                'status' => 1,
                'typing' => 0,
                'name' => $name,
                'level' => $level,
                'email' => $email,
                'photo' => NULL,
                'user' => $user,
                'password' => sha1($password.$systemPasswordSalt),
                'time' => 0
            );

        $pdo->prepare('INSERT INTO user (
                status, typing, level, name, email, photo, user, password, register_date, time
        ) VALUES (
                :status, :typing, :level, :name, :email, :photo, :user, :password, NOW(), :time
        )')->execute($user_add);

        $vld->addMessage('Usuário ('.$user.'), adicionado com sucesso');	

    }

}

$vld->jsonResult();
?>