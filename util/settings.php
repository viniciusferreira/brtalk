<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$systemName = 'brTalk';
$systemVersion = '1.1.0';
$systemPasswordSalt = '@#vp$ek&1!';

$lifeTime = time();  
$lifeTimeUser = $lifeTime + 120;  
$lifeTimeClient = $lifeTime + 120;  
$lifeTimeMessage = $lifeTime + 120;  

/*
    # Status Atendimento
    1 - atendimento iniciado
    2 - atendimento em andamento
    3 - atendimento encerrado
    4 - atendimento iniciado por outro atendente


    # Status Cliente
    1 - aguardando atendimento
    2 - atendimento em andamento
    3 - atendimento encerrado

    # Status Usuário
    0 - excluído
    1 - ativo
    2 - inativo

    # Tipo Usuário
    1 - Administrador
    2 - Atendente
*/
?>