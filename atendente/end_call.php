<?php
require_once('../util/header.php');
require_once('includes.php');

$session = new Session();
$session->checkSession('user');

$user = $session->get('user');

$client_id = isset($_POST['client_id']) ? (int) $_POST['client_id'] : 0;

$pdo = PDOConnection::getInstance();

$client_update = array(
    'client_id' => $client_id,
    'user_id' => $user['user_id'],
    'status' => 3
);

$pdo->prepare('UPDATE client SET status = :status, user_id = :user_id WHERE client_id = :client_id LIMIT 1')->execute($client_update);

/* Histórico */
$pdo->prepare('UPDATE client_history SET status = :status, user_id = :user_id WHERE client_id = :client_id LIMIT 1')->execute($client_update);

$json = array('call_status' => 3);

print json_encode($json);
?>