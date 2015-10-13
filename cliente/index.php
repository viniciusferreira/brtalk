<?php
require_once('includes.php');

$tpl = new Template();
$vld = new Validation();

$tpl->assign('system_name', $systemName);
$tpl->assign('system_version', $systemVersion);

$pdo = PDOConnection::getInstance();

if(isset($_POST['login'])){

    $vld->validate();

    if($vld->hasErrors() == false){

        extract($_POST, EXTR_SKIP);

        $stmt = $pdo->prepare('DELETE FROM client WHERE time < :time');
        $stmt->bindValue('time', $lifeTime);
        $stmt->execute();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM client WHERE email = :email AND status != :status');
        $stmt->bindValue('email', $email);
        $stmt->bindValue('status', 3);
        $stmt->execute();

        if($stmt->fetchColumn() == 0){

            $ip_address = $_SERVER['REMOTE_ADDR'];

            $client_insert = array(
                'user_id' => 0,
                'status' => 1,
                'typing' => 0,
                'name' => $name,
                'email' => $email,
                'ip_address' => $ip_address,
                'time' => $lifeTimeClient
            );

            $pdo->prepare('INSERT INTO client (
                    user_id, status, typing, name, email, ip_address, call_date , start_call_date , time
            ) VALUES (
                    :user_id, :status, :typing, :name, :email, :ip_address, NOW(), NULL, :time
            )')->execute($client_insert);

            $client_insert['client_id'] =  $pdo->lastInsertId();

            /* Hitórico */
            $pdo->prepare('INSERT INTO client_history (
                    client_id, user_id, status, typing, name, email, ip_address, call_date , start_call_date , time
            ) VALUES (
                    :client_id, :user_id, :status, :typing, :name, :email, :ip_address, NOW(), NULL, :time
            )')->execute($client_insert);

            /* Sessão */
            $client_insert['name'] = $name;

            $user = array(
                'user_id' => 0,
                'name' => NULL
            );

            $session = new Session();
            $session->register('client', $client_insert);
            $session->register('client_user', $user);

            header('Location: main.php');
            exit();

        }else{
            $vld->addError('Já existe um cliente com o e-mail informado');	
        }

    }

}

$tpl->assign('error', $vld->getErrorsAsHtml());

$stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE time > :time AND status = :status');
$stmt->bindValue('time', $lifeTime);
$stmt->bindValue('status', 1);
$stmt->execute();

if($stmt->fetchColumn() == 0){
    $tpl->block('info');
}else{
    $tpl->block('form');
}

$tpl->show();
?>