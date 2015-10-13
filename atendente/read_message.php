<?php
require_once('../util/header.php');
require_once('includes.php');

$session = new Session();
$session->checkSession('user');

$user = $session->get('user');

$pdo = PDOConnection::getInstance();

$typing = isset($_POST['typing']) ? (int) $_POST['typing'] : 0;

/* Envia mensagens */
if(isset($_POST['client_id'])){

    foreach($_POST['client_id'] as $index => $client_id){

        $message_insert = array(
            'client_id' => $client_id,
            'user_id' => $user['user_id'],
            'type' => 0,
            'status' => 1,
            'message' => strip_tags($_POST['message'][$index])
        );

        $pdo->prepare('INSERT INTO message (
                client_id, user_id, type, status, message, post_date
        ) VALUES (
                :client_id, :user_id, :type, :status, :message, NOW()
        )')->execute($message_insert);

        /* Histórico */
        $pdo->prepare('INSERT INTO message_history (
                client_id, user_id, type, status, message, post_date
        ) VALUES (
                :client_id, :user_id, :type, :status, :message, NOW()
        )')->execute($message_insert);

    }
}

/* Atualiza tempo do atendente */
$stmt = $pdo->prepare('UPDATE user SET time = :time, typing = :typing WHERE user_id = :user_id LIMIT 1');
$stmt->bindValue('user_id', $user['user_id']);
$stmt->bindValue('time', $lifeTimeUser);
$stmt->bindValue('typing', $typing);
$stmt->execute();

/* Lista clientes */
$stmt = $pdo->prepare('SELECT client_id, user_id, name, email, status, typing FROM client WHERE time > :time AND status != :status ORDER BY client_id ASC');
$stmt->bindValue('time', $lifeTime);
$stmt->bindValue('status', 3);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* lista mensagens */
$stmt = $pdo->prepare('SELECT client_id, user_id, message FROM message WHERE user_id = :user_id AND type = :type ORDER BY message_id ASC');
$stmt->bindValue('user_id', $user['user_id']);
$stmt->bindValue('type', 1);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Apaga mensagens lidas */
$stmt = $pdo->prepare('DELETE FROM message WHERE user_id = :user_id AND type = :type');
$stmt->bindValue('user_id', $user['user_id']);
$stmt->bindValue('type', 1);
$stmt->execute();

$json = array('clients' => $clients, 'messages' => $messages);

print json_encode($json);
?>