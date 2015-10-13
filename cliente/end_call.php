<?php
require_once('includes.php');

$session = new Session();
$tpl = new Template();

$tpl->assign('system_name', $systemName);
$tpl->assign('system_version', $systemVersion);

if($session->isRegistered('client')){
	
    $pdo = PDOConnection::getInstance();

    $client_session = $session->get('client');

    $client = array(
        'client_id' => $client_session['client_id'],
        'status' => 3
    );

    $pdo->prepare('UPDATE client SET status = :status WHERE client_id = :client_id LIMIT 1')->execute($client);

    /* Histórico */
    $pdo->prepare('UPDATE client_history SET status = :status WHERE client_id = :client_id LIMIT 1')->execute($client);

    $session->destroy('client');
	
}

$tpl->show();
?>