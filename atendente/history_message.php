<?php
require_once('../util/header.php');
require_once('includes.php');

$session = new Session();
$session->checkSession('user');

$pdo = PDOConnection::getInstance();

$client_id = isset($_POST['client_id']) ? (int) $_POST['client_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

/* Lista mensagens */
$stmt = $pdo->prepare('SELECT m.message, TIME(m.post_date) AS time, m.type, c.name AS client_name, u.name AS user_name
FROM message_history  m
INNER JOIN client_history c ON c.client_id = m.client_id
INNER JOIN user u ON u.user_id = m.user_id
WHERE m.user_id = :user_id AND m.client_id = :client_id 
ORDER BY message_id ASC');
$stmt->bindValue('user_id', $user_id);
$stmt->bindValue('client_id', $client_id);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$json = array('messages' => $messages);

print json_encode($json);

?>