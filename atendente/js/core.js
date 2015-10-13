/**
 * Script responsável pelas funções básicas do chat (atendente), como o envio e recebimento de mensagens
 * 
 * @file    core.js
 * @version 1.0
 * 
 * @author  Hédi Carlos Minin
 * @email   hedicarlos@gmail.com
 * 
 * @author  André da Silva Severino
 * @email   andre@andrewd.com.br
 * 
 * @url     http://andrewebdeveloper.github.io/brtalk/
 */
var _typing = 0;
var _buffer = new Array();
var _readTime = 3000;
var _clientsList = new Array();
var _browserFocused = true;
var _timeOutTitleScroll = null;

$(document).ready(function(){
	
    setTimeout('init()', 200);

});


function init(){
	
     new ClientWindow('clientList')
    .title('Clientes')
    .build()
    .focus();

    readMessage();
    browserFocus();
	
}

function readMessage(){
		
    $.ajax({
        url: 'read_message.php',
        type: 'POST',
        data: 'typing=' + _typing + _buffer.join(''),
        timeout : 15000,
        success: function(data){

            getWindow('clientList').addUsers(data.clients);
            getWindow('clientList').updateStatus();

            processClients(data.clients);
            processMessages(data.messages);

            setTimeout('readMessage()', _readTime);
            _typing = 0;

        },
        error: function(){
            getWindow('clientList').info('Falha ao carregar clientes, reiniciando...');
            /*
            switch(httpRequest.status){
                    case 401: getWindow('clientList').info('Acesso não autorizado'); break;
                    case 500: getWindow('clientList').info('Erro interno do servidor'); break;
            }
            */
            setTimeout('readMessage()', _readTime);  
            _typing = 0;
        }
    });

    _buffer = new Array();
    _buffer.push('');
    
}

function addMessageToBuffer(client_id, message){
    _buffer.push('&client_id[]=' + client_id);
    _buffer.push('&message[]=' + encodeURIComponent(message));	
}

function findClient(id){	
	return _clientsList[id];
}

function processClients(clients){
	
    if(clients == null || clients == undefined){
        return false;	
    }

    _clientsList = new Array();

    for(var i in clients){

        if(parseInt(clients[i].status) == 2){
            var w = getWindow(clients[i].client_id);
            if(w) w.typing(clients[i].typing);	
        }

        _clientsList[clients[i].client_id] = clients[i];
    }

    var windows = getWindows();

    for(var i in windows){
        if(windows[i] instanceof ChatWindow){
            if(findClient(i) == null){
                getWindow(i).showAlert('Atendimento encerrado pelo cliente');	
            }
        }
    }
	
}

function processMessages(messages){
	
    if(messages == null || messages == undefined){
        return false;	
    }

    if(messages.length > 0 && _browserFocused == false){

        if(_options.enableSound == true){
            flashControl('sound').play();
        }
        titleScroll(0, _config.windowTitle + ' - Nova mensagem recebida...');

    }

    for(var i in messages){

        var id = messages[i].client_id.toString();

        if(getWindow(id) == null){

            var client = findClient(id);

            new ChatWindow(id)
            .title(client.name)
            .build()
            .focus()
            .info('Atendimento em andamento')
            .addMessage(messages[i].message)
            .startCall(2)
            .call();	

        }else{
            getWindow(id)
            .addMessage(messages[i].message)
            .call();	
        }

    }
    
}

function browserFocus(){
    if((document.all ) && (!window.opera)){
        document.onfocusout = function(){ 
            _browserFocused = false; 
        }
        document.onfocusin = function(){ 
            _browserFocused = true; 
            restoreWindowTitle();
        }
    }else{	
        window.onblur = function(){ 
            _browserFocused = false; 
        }
        window.onfocus = function(){ 
            _browserFocused = true; 
            restoreWindowTitle();
        }
    }	
}

function restoreWindowTitle(){
    clearTimeout(_timeOutTitleScroll);	
    document.title = _config.windowTitle;	
}

function titleScroll(i, message){
	
    if(i == 0) clearTimeout(_timeOutTitleScroll);

    if(i < message.length){
        document.title = message.substr(i, message.length);
        i++;
        _timeOutTitleScroll = setTimeout(function(){ titleScroll(i, message); }, 100);
    }else{
        i = 0;
        document.title = message;
        _timeOutTitleScroll = setTimeout(function(){ titleScroll(i, message); }, 1000);
    }
	
}


