<?php
require_once('includes.php');

$session = new Session();

$user = $session->get('user');

if($user){

    $session->destroy('user');

    $pdo = PDOConnection::getInstance();

    $stmt = $pdo->prepare('UPDATE user SET time = :time WHERE user_id = :user_id LIMIT 1');
    $stmt->bindValue('user_id', $user['user_id']);
    $stmt->bindValue('time', 0);
    $stmt->execute();
	
}

header('Location: index.php');
exit();
?>