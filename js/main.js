$(document).ready(function(){
    
    $("a[rel='tipsy']").tipsy({gravity: 'n'});

    $(document).bind("contextmenu",function(e){
        return false;
    });
    
    $(".fancybox-effects-d").fancybox({
        openEffect : 'elastic',
        openSpeed  : 150,

        closeEffect : 'elastic',
        closeSpeed  : 450,

        closeClick : true,

        helpers : {
            overlay : null
        }
    });
    
    $('#demonstracao-atendimento').click(function(){

        var width   = 500;
        var height  = 540;
        var left    = (screen.width - width) / 2;
        var top     = ((screen.height - height) / 2) - 30;

        window.open('http://lab.andrewd.com.br/brtalk/cliente/', 'at',  'height=' +height+ ', width=' +width+ ',status=no, toolbar=no, resizable=0, scrollbars=no, minimizable=no, left=' +left+ ', top=' +top);

    });
    
    var altura_div_versao = $('#list_version').height();
    
    if( altura_div_versao > 400 )
        $('#list_version').css('height', '400px');
    
    $('#ver-todas-atualizacoes').click(function(){
        $("#list_version").animate(
            {"height": altura_div_versao+"px"},
        "fast");
        $(this).hide("slow");
    });
    
});