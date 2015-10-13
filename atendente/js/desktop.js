/**
 * jQuery Desktop
 * 
 * @file    desktop.js
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
var _zIndex  = 5;
var _windows = new Array();
var _currentTheme = null;
var _opacityEnabled = false;
var _performanceMode = false;

function getWindow(id){
    return _windows[id];	
}

function getWindows(){
    return _windows;	
}

function showDesktop(){
    for(var i in _windows){
        _windows[i].minimize(true);	
    }
    $(document).attr('title', _config.windowTitle);
}

function Window(id){
	
    this.id = id;
    this.windowTitle = 'New Window';
    this.windowIcon = 'img/' +_currentTheme+ '/default_icon.png';
    this.windowBounds = {top: 0, left: 0, width: 300, height: 400};

    this.isResizable = true;
    this.isMaximizable = true;
    this.isMinimizable = true;
    this.isCloseable = true;
    this.isFocusable = true;

    this.callTimeout = null;
    this.isMaximized = false;
    this.isMinimized = false;

    this.closeButton = $('<a></a>');
    this.minimizeButton = $('<a></a>');
    this.maximizeButton = $('<a></a>');

    this.layoutMain = $('<div></div>');
    this.layoutTitle = $('<h4></h4>');
    this.layoutTitleBar = $('<div></div>');
    this.layoutStatusBar = $('<div></div>');
    this.layoutContent = $('<div></div>');
    this.layoutMove = $('<div></div>');

    this.taskBar = $('#taskbar');
    this.taskBarButton = $('<a></a>');

    this.onfocus = function(){};
    this.onclose = function(){};

    this.title = function(title){
            this.windowTitle = title;
            $(this.layoutTitle).text(title);	
            $(this.taskBarButton).text(title);
            return this;
    }

    this.icon = function(icon){
            this.windowIcon = icon;
            $(this.layoutTitle).css('backgroundImage', 'url(' +icon+ ')');
            $(this.taskBarButton).css('backgroundImage', 'url(' +icon+ ')');
            return this;
    }

    this.content = function(content){
            $(this.layoutContent).html(content);	
            return this;
    }

    this.append = function(content){
            $(this.layoutContent).append(content);	
            return this;
    }

    this.getContent = function(){
            return this.layoutContent;
    }

    this.bounds = function(top, left, width, height){
            this.windowBounds = {top: top, left: left, width: width, height: height};
            return this;
    }

    this.resizable = function(resizable){
            this.isResizable = resizable;		
            return this;
    }

    this.maximizable = function(maximizable){
            this.isMaximizable = maximizable;	
            return this;
    }

    this.minimizable = function(minimizable){
            this.isMinimizable = minimizable;		
            return this;
    }

    this.closeable = function(closeable){
            this.isCloseable = closeable;		
            return this;
    }

    this.focusable = function(focusable){
            this.isFocusable = focusable;
            return this;
    }

    var parent = this;

    this.build = function(){

        if(_windows[this.id] != null) return _windows[this.id];

        $(this.layoutMain)
        .attr('id', 'j' +this.id)
        .addClass('window')
        .css('visibility', 'visible')
        .css('zIndex', _zIndex)
        .mousedown(function(){
            parent.focus();
        })
        .mouseup(function(){
            $(document).unbind('mousemove');
            enableTextSelect();
        });


        $(this.layoutTitle)
        //.attr('title', 'Mover')
        .text(this.windowTitle)
        .css('backgroundImage', 'url(' +this.windowIcon+ ')')
        .mousedown(function(e){

            if(parent.isMaximized == false){

                var p = $(parent.layoutMain).position();
                var difY = (e.pageY - p.top);
                var difX = (e.pageX - p.left);	

                disableTextSelect();	

                var move = parent.layoutMain;

                if(_performanceMode){

                    var b = parent.getBounds();

                    $(parent.layoutMove).css('top' , b.top).css('left' , b.left).css('height' , b.height).css('width' , b.width)
                    .css('z-index' , _zIndex + 10)
                    .addClass('window_move')
                    .mouseup(function(){
                        parent.setBounds($(this).position().top, $(this).position().left, $(this).width(), $(this).height());
                        $(this).remove();	
                        $(document).unbind('mousemove');
                    });

                    $(document.body).append(parent.layoutMove);

                    move = parent.layoutMove;

                }

                $(document).mousemove(function(e){					   
                    $(move).css('top', (e.pageY - difY));					   
                    $(move).css('left', (e.pageX - difX));	
                });

            }

        });

        if(this.isMaximizable == true){
            $(this.layoutTitleBar).dblclick(function(){
                parent.maximize(parent.isMaximized == false ? true : false);
            });
        }

        $(this.layoutStatusBar)
        .attr('title', 'Redimensionar')
        .mousedown(function(e){

            if(parent.isMaximized == false){
                var difY = e.pageY - $(parent.layoutMain).height();
                var difX = e.pageX - $(parent.layoutMain).width();	

                disableTextSelect();

                var resize = parent.layoutMain;

                if(_performanceMode){

                    var b = parent.getBounds();

                    $(parent.layoutMove).css('top' , b.top).css('left' , b.left).css('height' , b.height).css('width' , b.width)
                    .css('z-index' , _zIndex + 10)
                    .addClass('window_move')
                    .mouseup(function(){
                        parent.setBounds($(this).position().top, $(this).position().left, $(this).width(), $(this).height());
                        $(this).remove();	
                        $(document).unbind('mousemove');
                    });

                    $(document.body).append(parent.layoutMove);

                    resize = parent.layoutMove;

                }

                $(document).mousemove(function(e){
                    var height = e.pageY - difY;
                    var width = e.pageX - difX;
                    if(height > 200){
                        $(resize).css('height', height); 
                    }
                    if(width > 200){
                        $(resize).css('width', width); 
                    }
                });

            }

        });

        $(this.taskBarButton)
        .attr('href', 'javascript:void(0)')
        .attr('title', this.windowTitle)
        .text(this.windowTitle)
        .css('backgroundImage', 'url(' +this.windowIcon+ ')')
        .click(function(){

            clearTimeout(parent.callTimeout);

            if($(parent.layoutMain).css('zIndex') != _zIndex){
                parent.focus();		
            }else{
                if(parent.isMinimized == false){
                    parent.minimize(true);
                }else{
                    parent.focus();	
                }
            }			

        })
        .mouseup(function(e){
            if (e.button == 2) {		
                $(document).bind('contextmenu', function(){ return false; });	

                if(parent.isCloseable == true){
                    var p = $(this).offset();
                    $('#context_menu')
                    .css('top', p.top - 30)
                    .css('left', p.left)
                    .show()
                    .unbind('click')
                    .click(function(){ 
                        parent.close(); 
                    });
                }
            }
        });

        $(this.closeButton)
        .attr('href', 'javascript:void(0)')
        .attr('title', 'Fechar')
        .addClass('window_close_buttom')
        .click(function(){
            parent.close();	
        });

        $(this.minimizeButton)
        .attr('href', 'javascript:void(0)')
        .attr('title', 'Minimizar')
        .addClass('window_minimize_buttom')
        .click(function(){
            parent.minimize(true);
        });

        $(this.maximizeButton)
        .attr('href', 'javascript:void(0)')
        .attr('title', 'Maximizar')
        .addClass('window_maximize_buttom')
        .click(function(){
            parent.maximize(parent.isMaximized == false ? true : false);
        });

        if(this.windowBounds.height > $(document).height()){
            this.windowBounds.height = $(document).height() - 100;
        }
        if(this.windowBounds.width > $(document).width()){
            this.windowBounds.width = $(document).width() - 100;
        }
        if(this.windowBounds.top == 0){
            this.windowBounds.top = ($(document).height() - this.windowBounds.height) / 2;	
        }
        if(this.windowBounds.left == 0){
            this.windowBounds.left = ($(document).width() - this.windowBounds.width) / 2;
        }

        this.setBounds(this.windowBounds.top, this.windowBounds.left, this.windowBounds.width, this.windowBounds.height);

        if(this.isCloseable){
            $(this.layoutTitleBar).append(this.closeButton);
        }
        if(this.isMaximizable){
            $(this.layoutTitleBar).append(this.maximizeButton);
        }
        if(this.isMinimizable){
            $(this.layoutTitleBar).append(this.minimizeButton);	
        }

        $(this.layoutTitleBar).append(this.layoutTitle);		
        $(this.layoutMain).append(this.layoutTitleBar);
        $(this.layoutMain).append(this.layoutContent);
        $(this.taskBar).append(this.taskBarButton);

        if(this.isResizable){
            $(this.layoutMain).append(this.layoutStatusBar);
        }else{
            $(this.layoutContent).css('bottom', 0);
        }
        $(document.body).append(this.layoutMain);

        _windows[this.id] = this;

        return this;
    }

    this.close = function(){

        this.onclose();

        $(document).attr('title', _config.windowTitle);

        $(this.layoutMain).animate({
                opacity: 0,
                height: 'hide'
            }, 250, function() {
                $(parent.layoutMain).remove();	
                $(parent.taskBarButton).remove();
                delete _windows[parent.id];
        });

    }

    this.focus = function(){

        this.onfocus();

        _zIndex++;
        $(this.layoutMain).css('zIndex', _zIndex);
        this.minimize(false);

        $('.window').removeClass('window_focus');
        $(this.layoutMain).addClass('window_focus');

        if(_opacityEnabled){
            $('.window').css('opacity', 0.6);
            $(this.layoutMain).css('opacity', 1);
        }

        $('#taskbar a').removeClass();
        $('#taskbar a').addClass('taskbar');
        $(this.taskBarButton).addClass('taskbar_focus');

        $(document).attr('title', this.windowTitle);

        return this;

    }

    this.minimize = function(minimized){

        this.isMinimized = minimized;
        $(this.layoutMain).css('visibility', minimized == true ? 'hidden' : 'visible')
        return this;

    }

    this.maximize = function(maximized){

        this.isMaximized = maximized;

        if(maximized == true){
            var b = this.getBounds();
            this.windowBounds = {top: b.top, left: b.left, width: b.width, height: b.height};
            this.setBounds(0, 0, ($(document).width() - 2), ($(document).height() - $(this.taskBar).innerHeight()) - 2);
            $(this.maximizeButton)
            .removeClass('window_maximize_buttom')
            .addClass('window_restaure_buttom')
            .attr('title', 'Restaurar'); 
        }else{
            this.setBounds(this.windowBounds.top, this.windowBounds.left, this.windowBounds.width, this.windowBounds.height);	
            $(this.maximizeButton)
            .removeClass('window_restaure_buttom')
            .addClass('window_maximize_buttom')
            .attr('title', 'Maximizar'); 
        }
        return this;
        
    }

    this.setBounds = function(top, left, width, height){
        $(this.layoutMain).css('top', top); 
        $(this.layoutMain).css('left', left); 
        $(this.layoutMain).css('width', width); 
        $(this.layoutMain).css('height', height); 
    }

    this.getBounds = function(){
        return {
            top: $(this.layoutMain).position().top,
            left: $(this.layoutMain).position().left,
            height: $(this.layoutMain).height(),
            width: $(this.layoutMain).width()	
        }
    }

    this.callLimit = 0;
    this.call = function(){

        if($(this.layoutMain).attr('class') != 'window') return this;

        if(this.callLimit < 7){
            if(this.callLimit % 2 == 0){
                $(this.taskBarButton).removeClass('taskbar_focus').addClass('taskbar_call');
            }else{
                $(this.taskBarButton).removeClass('taskbar_call').addClass('taskbar_focus');	
            }
            this.callLimit++;
            this.callTimeout = setTimeout(function() { parent.call(); }, 800);
        }else{
            this.callLimit = 0;	
        }

    }
}

function disableTextSelect(){
    document.onmousedown = function(){ return false; }	
    document.onselectstart = function(){ return false; }
}

function enableTextSelect(){
    document.onmousedown = function(){ return true; }	
    document.onselectstart = function(){ return true; }	
}

function resizeWindow(){
	
    var windows = getWindows();
    for(var i in windows){
        if(windows[i].isMaximized == true){
            windows[i].setBounds(0, 0, ($(document.body).width() - 2), ($(document.body).height() - $(windows[i].taskBar).innerHeight()) - 2);
        }
    }
	
}

function ChangeTheme(){
	
    var w = new Window('themes')
    .title('Temas')
    .icon('img/theme_icon.png')
    .bounds(0, 0, 380, 300)
    .maximizable(false)
    .build()
    .focus();

    var div = $('<div></div>').addClass('themes');

    for(var i in _themes){
        var theme = _themes[i];
        $(div).append(
            $('<p></p>')
            .text(theme.name)
            .append(
                $('<img>')
                .attr('id', 'theme_' +theme.theme)
                .attr('src', 'theme/' +theme.theme + '/trumb.jpg').click(function(){
                    ApplyTheme($(this).attr('id').replace('theme_', ''));
                })
            )
        )
    }

    w.append(div);

}

function ApplyTheme(theme){
	
    _currentTheme = theme;
    $('#css_desktop').attr('href', 'theme/' +theme+ '/desktop.css');

    var date = new Date();
    date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
    $.cookie('theme', theme, { expires: date });

}

function initGUI(){
	
    $(window).resize(function(){
        resizeWindow();						  
    });

    $(document.body).click(function(){
        $(document).unbind('contextmenu');
        $('#context_menu').hide();
    });	

    if($.cookie('theme')){
        ApplyTheme($.cookie('theme'));	
    }else{
        _currentTheme = _themes[0].theme;
    }

    $(document.body).append(
        $('<div></div>')
        .attr('id', 'taskbar')
        .addClass('taskbar').append(
            $('<span></span>')
            .attr('title', 'Mostrar área de trabalho')
            .click(function(){
                showDesktop();																			
            })
        )
    );

    $(document.body).append(
        $('<a></a>')
        .attr('href', 'javascript:void(0)')
        .attr('title', 'Fechar janela')
        .attr('id', 'context_menu')
        .text('Fechar')
    );

}

$(document).ready(function(){
						
    initGUI();

});