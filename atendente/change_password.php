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

    $user_session = $session->get('user');

    $stmt = $pdo->prepare('SELECT * FROM user WHERE user_id = :user_id');
    $stmt->bindValue('user_id', $user_session['user_id']);
    $stmt->execute();
    $user = $stmt->fetch();

    if(sha1($password.$systemPasswordSalt) != $user['password']){

        $vld->addError('Senha atual não confere');	

    }else{

        $stmt = $pdo->prepare('UPDATE user SET password = :password WHERE user_id = :user_id');
        $stmt->bindValue('user_id', $user_session['user_id']);
        $stmt->bindValue('password', sha1($new_password.$systemPasswordSalt));
        $stmt->execute();

        $vld->addMessage('Senha alterada com sucesso');	

    }

}

$vld->jsonResult();
?>