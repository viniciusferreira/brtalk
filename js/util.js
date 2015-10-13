/**
 * Script contendo funções úteis
 * 
 * @author  Hédi Carlos Minin
 * @email   hedicarlos@gmail.com
 * 
 * @author  André da Silva Severino
 * @email   andre@andrewd.com.br
 * 
 * @url     http://andrewebdeveloper.github.io/brtalk/
 * 
 * @version 1.0
 */

function flashControl(movieName){
	
    this.flash = null;

    if (document.embeds[movieName]) {
        this.flash = document.embeds[movieName];
    }else if(window.document[movieName]){
        this.flash = window.document[movieName];
    }else{
        this.flash = document.getElementById(movieName);
    }

    //this.flash = document.getElementById(id);

    this.play = function(){
        this.flash.Play();	
        return this;
    }

    this.rewind = function(){
        this.flash.Rewind();	
        return this;
    }

    this.stop = function(){
        this.flash.StopPlay();	
        return this;
    }	

    this.pause = function(){
        this.flash.PausePlay();	
        return this;
    }

    this.gotoFrame = function(frame){
        this.flash.GotoFrame(frame);
        return this;
    }

    return this;
    //TotalFrames()
    //SetVariable( variableName, value )
    //PercentLoaded() 
    //IsPlaying()
    //GotoFrame( frameNumber ) 
}

function formBuilder(form){
	
    var f = $('<form></form>');

    for(var i in form){

        var field = null;

        switch(form[i].field){
            case 'text':
                field = $('<input/>').attr('type', 'text');	
            break;
            case 'password':
                field = $('<input/>').attr('type', 'password');	
            break;
            case 'select':
                field = $('<select></select>');	
                for( var j in form[i].options){
                    $(field).append($('<option></option>').attr('value', form[i].options[j].value).text(form[i].options[j].label));
                }
            break;
            case 'textarea':
                field = $('<textarea></textarea>');	
            break;
        }

        if(field != null){

            for(var a in form[i].attributes){
                $(field).attr(a, form[i].attributes[a]);
            }

            $(f).append(
                $('<div></div>')
                .append($('<label></label>').text(form[i].label))
                .append(field)							
            );
        }

    }

    return f;
}

function getCurrentTime(){
	
    var date = new Date();	
    var h = date.getHours();
    var m = date.getMinutes();
    var s = date.getSeconds();

    h = h < 10 ? '0' +h : h;
    m = m < 10 ? '0' +m : m;
    s = s < 10 ? '0' +s : s;

    return h+ ':' +m+ ':' +s;
}

function getCurrentDate(){
	
    var date = new Date();	
    var d = date.getDate();
    var m = date.getMonth() + 1;
    var a = date.getFullYear();

    d = d < 10 ? '0' +d : d;
    m = m < 10 ? '0' +m : m;

    return d+ '/' +m+ '/' +a;
}