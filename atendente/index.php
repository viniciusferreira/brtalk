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

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE user = :user AND password = :password AND status = :status');
        $stmt->bindParam('user', $user);
        $stmt->bindParam('password', sha1($password.$systemPasswordSalt));
        $stmt->bindValue('status', 1);
        $stmt->execute();

        if($stmt->fetchColumn() > 0){

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE user = :user  AND time > :time AND status = :status');
            $stmt->bindParam('user', $user);
            $stmt->bindValue('time', $lifeTime);
            $stmt->bindValue('status', 1);
            $stmt->execute();

            if($stmt->fetchColumn() == 0){

                $stmt = $pdo->prepare('SELECT * FROM user WHERE user = :user AND password = :password AND status = :status LIMIT 1');
                $stmt->bindParam('user', $user);
                $stmt->bindParam('password', sha1($password.$systemPasswordSalt));
                $stmt->bindValue('status', 1);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $session = new Session();
                $session->register('user', $user);

                header('Location: main.php');
                exit();

            }else{
                $vld->addError('Já existe um usuário logado com os dados informados');	
            }

        }else{
            $vld->addError('Usuário ou senha inválido');	
        }

    }

}

$tpl->assign('error', $vld->getErrorsAsHtml());

$tpl->show();
?>