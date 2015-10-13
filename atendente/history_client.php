<?php
require_once('../util/header.php');
require_once('includes.php');

$session = new Session();
$session->checkSession('user');

$vld = new Validation();

$vld->validate();

if($vld->hasErrors() == false){

    $pdo = PDOConnection::getInstance();

    extract($_POST, EXTR_SKIP);

    $call_date = implode('-', array_reverse(explode('/', $call_date)));

    /* Lista clientes */
    $stmt = $pdo->prepare('SELECT client_id, name, email FROM client_history WHERE user_id = :user_id AND DATE(call_date) = :call_date ORDER BY name ASC');
    $stmt->bindValue('user_id', $user_id);
    $stmt->bindValue('call_date', $call_date);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $json = array('clients' => $clients);

    print json_encode($json);

}else{

    $vld->jsonResult();
	
}
?>