<?php
require_once('includes.php');

$session = new Session();

if(!$session->isRegistered('client')){
    header('Location:index.php');
    exit();
}

$tpl = new Template();

$tpl->assign('system_name', $systemName);
$tpl->assign('system_version', $systemVersion);

$client = $session->get('client');
$tpl->assign('client_id', $client['client_id']);
$tpl->assign('client_name', $client['name']);
$tpl->assign('client_email', $client['email']);
$tpl->assign('client_status', $client['status']);

$user = $session->get('client_user');
$tpl->assign('client_user_id', $user['user_id']);
$tpl->assign('client_user_name', $user['name']);

$tpl->show();
?>