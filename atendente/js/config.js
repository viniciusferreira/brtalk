/**
 * Script responsável pelas cofigurações do sistema
 * 
 * @file    config.js
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
var _config = {
    windowTitle : 'brTalk',
    initialMessage : 'Olá sr(a) {NAME}. Seja bem vindo(a), em que posso lhe ajudar ?'
};

var _themes = [
    {theme: 'basic', name : 'Basic'},
    {theme: 'nature', name : 'Nature'}
];

var _options = {
    enableSound : true
};

var _predefined_messages = [
    {shortcut : '/aguarde', message : 'Aguarde um momento por favor'}
]

var _info = {
    name    : 'brTalk',
    version : '1.1.0',
    author  : 'Hédi Carlos Minin',
    email   : 'hedicarlos@gmail.com',
    author2 : 'André Severino',
    email2  : 'andre@andrewd.com.br',
    url     : 'http://andrewebdeveloper.github.io/brtalk/'
}