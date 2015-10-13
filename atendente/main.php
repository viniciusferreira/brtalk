<?php
require_once('includes.php');

$session = new Session();

if(!$session->isRegistered('user')){
    header('Location: index.php');
    exit();
}

$tpl = new Template();

$tpl->assign('system_name', $systemName);
$tpl->assign('system_version', $systemVersion);

$user = $session->get('user');

$tpl->assign('user_id', $user['user_id']);
$tpl->assign('user_name', $user['name']);
$tpl->assign('user_email', $user['email']);
$tpl->assign('user_status', $user['status']);
$tpl->assign('user_photo', $user['photo']);

if($user['level'] == 1){
    $tpl->block('administrator');
}

$tpl->show();
?>