var OK_button="";
var CANCEL_button="";
var CLOSE_button="";
var DELETE_button="";
var REDUCE_button="";
var EXTEND_button="";
var HIDE_button="";
var EXPORT_button="";
var SAVING="";
var DIR_CONF_UPDATE="";
var DIR_CONF_NOT_UPTODATE="";
var RELOAD_button="";
var SELECT_button="";
var LOADING="";
var NEXT_button="";
var PREVIOUS_button="";
var COMPUTE_button="";
var TEST_button="";
var diffVal=[];


var lang="fr_FR";
var reduced="";
var finished=0;

//To delete current ajax call:
$.xhrPool = [];
$.xhrPool.abortAll = function() {
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.length = 0
};

//To delete setTimeout et setInterval:
$.timeout = [];
$.timeout.abortAll = function() {
    $(this).each(function(idx, jqXHR) {
        clearTimeout(jqXHR);
    });
    $.timeout.length = 0
};



$.ajax({
    cache: false,
    async: false,
    url: "main/modules/external/get_variable.php",
    data: {name:"lang"}
}).done(function (data) {
    if(jQuery.parseJSON(data)!="0") lang=jQuery.parseJSON(data);
});

RELOAD_button="Re-scanner les réseaux";
SELECT_button="Valider";
OK_button="Continuer";
CANCEL_button="Annuler";
CLOSE_button="Fermer";
DELETE_button="Supprimer";
SAVE_button="Enregistrer";
REDUCE_button="Réduire";
EXTEND_button="Agrandir";
HIDE_button="Cacher";
EXPORT_button="Exporter";
SAVING="Sauvegarde des données en cours, patientez s'il vous plait..."
DIR_CONF_UPDATE="Votre configuration est à jour";
DIR_CONF_NOT_UPTODATE="La configuration utilisée n'est pas à jour, cliquez ici pour mettre la configuration à jour: <button id='update_conf'>Mise à jour de la configuration</button>";
LOADING="Chargement des données en cours, patientez s'il vous plait...";
NEXT_button="Etape suivante";
PREVIOUS_button="Etape précédente";
COMPUTE_button="Afficher le résultat";
TEST_button="Test";



// {{{ secondsToTime()
// ROLE transofrm seconds into MM:SS
// IN seconds to be converded
function secondsToTime(secs)
{
    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);
    if(minutes<10) minutes="0"+minutes;
 
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);
    if(seconds<10) seconds="0"+seconds;

    return minutes+"m"+seconds+"s";
}


// {{{ VerifNumber()
// ROLE function of verification of an input value
// IN input value "e" to be checked
// HOW IT WORKS: check ascii code of the input value
// USED BY: templates/programs.html and templates/wizard.html
VerifNumber = function(e) {
    if((e.which > 57) || ((e.which > 31 ) && (e.which < 44)) || (e.which == 45) || (e.which == 47)) {
        return false;
    } 
    return true;
}
// }}}



diffdate = function(d1,d2) {
    var WNbJours = d2.getTime() - d1.getTime();
    return Math.ceil(WNbJours/(1000*60*60*24)+1);
}


addZ = function(n){return n<10? '0'+n:''+n;}


clean_highchart_message = function(message) { 
    message=message.replace("'", "\'");
    return $('<div>').html(message).text();
}


//To get url param into object:
getUrlVars = function(url) {
    var hash;
    var myJson = {};
    var hashes = url.slice(url.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        myJson[hash[0]] = hash[1];
    }
    return myJson;
}   


//Return object containing form's input and value:
getFormInputs = function(form) {
    var values = {};
    var name_map = {};

    $.each($('#'+form).serializeArray(), function(i, field) {
        values[field.name] = field.value;
    });

    return values;
}


// brief : Used to clean messages
function clean_pop_up_messages() {
    $("#pop_up_information_part li").remove();
    $("#pop_up_error_part li").remove();
};


// brief : Used to add a message in popup
// message : Message to show
// id to define
// type : information or error
function pop_up_add_information(message, id, type) {
    // Add message
    if (type == "information")
        $("#pop_up_information_part ul").append('<li class="li_info" id="' + id + '">' + message + '</li>');
        
    if (type == "error")
        $("#pop_up_error_part ul").append('<li class="li_info" id="' + id + '">' + message + '</li>');

    // If there is element in error part, show this part
    if ($("#pop_up_error_part ul li").length > 0)
        $("#pop_up_error_container").css("display", "");
        
    if ($("#pop_up_information_part ul li").length > 0)
        $("#pop_up_information_container").css("display", "");
                
};

// brief : Remove a message in popup
// id to remove
function  pop_up_remove(id) {

    // Delete information
    $("#" + id).remove();
    
    if ($("#pop_up_error_part ul li").length < 1)
        $("#pop_up_error_container").css("display", "none");
        
    if ($("#pop_up_information_part ul li").length < 1)
        $("#pop_up_information_container").css("display", "none");
    
};


// brief : select the matching menu
// menu to be activated
function active_menu(menu) {
    $("#menubar-ul").find('li').each(function(){
        if($(this).attr('id')=="menu-"+menu) {
            $(this).addClass("active");
            $(this).addClass("current");
            $(this).find('span').each(function(){
                $(this).addClass("active");
            });
        } else {
            $(this).removeClass("active");
            $(this).removeClass("current");
            $(this).find('span').each(function(){
                $(this).removeClass("active");
            });
        }
    });
}


// brief : get content and display in it the main content div
// page: page to be displayed in the content div
function get_content(page,get_array) {
   //Clean AJAX calls:
   $.xhrPool.abortAll();

   //Clean setTimeout, setInterval:
   $.timeout.abortAll();

   //Clean popup message:
   clean_pop_up_messages();

   //Clean dialog box except the software dialog box:
   $(".ui-dialog-content").not('.message').dialog('destroy').remove();

   $.blockUI({
        message: LOADING+" <img src=\"main/libs/img/waiting_small.gif\" />",
        centerY: 0,
        css: {
            top: '20%',
            border: 'none',
            padding: '5px',
            backgroundColor: 'grey',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .9,
            color: '#fffff'
        },
        onBlock: function() {
            $.ajax({
                cache: false,
                async: false,
                url: "main/modules/external/get_content.php",
                data: {
                    page:page,
                    get_array:JSON.stringify(get_array)
                }
            }).done(function (data) {
                //Some odd chars appear when including php files due to echo include that returns true value, removing them:
                $("#content").html(data);
                $("#content").load();
                active_menu(page);

                //Logs hav a special behaviour: interface is unlocked after graph is loaded.
                if(page != "logs") {
                    $.unblockUI();
                }
            });
        }
    });
}

$(window).unload( function () { 
    $.ajax({
        cache: false,
        async: false,
        url: "main/modules/external/set_variable.php",
        data: {
            name:"ADHOC",
            value: "False",
            duration: 86400*30
        }
    });

     $.ajax({
        cache: false,
        url: "main/modules/external/enable_webcam.php",
        data: {
            action:"disable",
            webcam: "1"
        }
    });
});

$(document).ready(function() {
   $("[title]").tooltip({
        position: { my: "left+20 center-30", at: "right+15 center-10" },
        close: function (event, ui) {
            ui.tooltip.hover(
            function () {
                $(this).stop(true).fadeTo(300, 1);
            },
            function () {
                $(this).fadeOut("300", function () {
                    $(this).remove();
                })
            });
        }
    });

    var position_set=$("#content").position();

    $('a').click(function(){
        $(this).blur();
    });


     // Tooltip only Text
     $(document).on('mouseenter','a',function(){
                alert("test");
                // Hover over code
                var title = $(this).attr('title');
                if(title) {
                    $(this).data('tipText', title).removeAttr('title');
                    $('<p class="tooltip"></p>')
                    .text(title)
                    .appendTo('body')
                    .fadeIn('slow');
                }
        }, function() {
                // Hover out code
                if($(this).data('tipText')) {
                    $(this).attr('title', $(this).data('tipText'));
                    $('.tooltip').remove();
                }
        }).mousemove(function(e) {
                var mousex = e.pageX + 20; //Get X coordinates
                var mousey = e.pageY + 10; //Get Y coordinates
                $('.tooltip')
                .css({ top: mousey, left: mousex })
        });


    $(document.body).on('click', '.scroll' ,function(e){
            e.preventDefault();
            $('html, body').animate( { scrollTop: $($(this).attr('href')).offset().top }, 500 );
        });
        
        $(window).scroll(function (event) {
            var scroll = $(window).scrollTop();
            if(scroll>0) {
                if($("#arrow_up").css('visibility') == 'hidden') {
                    $("#arrow_up").css('visibility','visible');
                }
                
                if(scroll+$(window).height()<$(document).height()) {
                    if($("#arrow_down").css('visibility') == 'hidden') {
                        $("#arrow_down").css('visibility','visible');
                    }
                } else {
                    $("#arrow_down").css('visibility','hidden');
                }
            } else {
                $("#arrow_up").css('visibility','hidden');
            } 
            
        });
        
        $("#arrow_up").click(function (e){
            e.preventDefault();
            $('html, body').animate( { scrollTop: $("#header").offset().top }, 500 );
            $("#arrow_up").css('visibility','hidden');
        });
        
        $("#arrow_down").click(function (e){
            e.preventDefault();
            $('html, body').animate( { scrollTop: $("#footer").offset().top }, 500 );
            $("#arrow_down").css('visibility','hidden');
        });
        
        if($(document).height()>$(window).height()) {
                $("#arrow_down").css('visibility','visible');
        }


     $.ajax({
        cache: false,
        async: false,
        url: "main/modules/external/set_variable.php",
        data: {name:"CONTENT_TOP", value: position_set.top, duration: 36000}
     });

    $.ajax({
        cache: false,
        async: false,
        url: "main/modules/external/set_variable.php",
        data: {name:"CONTENT_LEFT", value: position_set.left, duration: 36000}
     });

     $.ajax({
        cache: false,
        async: false,
        url: "main/modules/external/set_variable.php",
        data: {name:"CONTENT_RIGHT", value: position_set.left+$("#content").width(), duration: 36000}
     });


    if (!Date.now) {
        Date.now = function() { return new Date().getTime(); }
    }


    $.ajax({
        cache: false,
        async: false,
        url: "main/modules/external/update_date.php",
        data: {date:Math.floor(Date.now() / 1000)}
    });


    var search = location.search.substring(1);
    var get_array = getUrlVars(search);

    if(( typeof get_array['menu']!="undefined")&&(['menu']!="")) { 
        get_content(get_array['menu'],get_array);
    } else {
        get_content("welcome",get_array);
    } 


    $(".href-welcome").click(function(e) {
        e.preventDefault();
        get_content("welcome",get_array);
    });

    $(".href-configuration").click(function(e) {
       e.preventDefault();
       get_content("configuration",get_array);
    });

    $(".href-logs").click(function(e) {
       e.preventDefault();
       get_content("logs",get_array);
    });

    $(".href-programs").click(function(e) {
       e.preventDefault();
       get_content("programs",get_array);
    });

    $(".href-plugs").click(function(e) {
       e.preventDefault();
       get_content("plugs",get_array);
    });

    $(".href-calendar").click(function(e) {
       e.preventDefault();
       get_content("calendar",get_array);
    });

    $(".href-cost").click(function(e) {
       e.preventDefault();
       get_content("cost",get_array);
    });

    $(".href-wizard").click(function(e) {
       e.preventDefault();
       get_content("wizard",get_array);
    });

    $(".href-webcam").click(function(e) {
        e.preventDefault();
        get_content("webcam",get_array);
    });

    $(".welcome-logo").click(function(e) {
       e.preventDefault();
       get_content("welcome",get_array);
    });

    $(".href-bulcky").click(function(e) {
       e.preventDefault();
       get_content("bulcky",get_array);
    });

    //To deal with dynamic content with href in message box
    $('div.error').on('click', 'a', function(e) {
        e.preventDefault();
        var url_vars=getUrlVars($(this).attr('href'));
        get_content(url_vars['menu'],url_vars);
    });

    function updateConfiguration () {
        // block user interface during checking and saving
        $.blockUI({
            message: SAVING+" <img src=\"main/libs/img/waiting_small.gif\" />",
            centerY: 0,
            css: {
                top: '20%',
                border: 'none',
                padding: '5px',
                backgroundColor: 'grey',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .9,
                color: '#fffff'
            },
            onBlock: function() {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "main/modules/external/sync_conf.php",
                    success: function(data) {
                        // Parse result
                        var json = jQuery.parseJSON(data);
                        // Delete information
                        pop_up_remove("check_conf_status");
                        pop_up_remove("display_diff");

                        if(json==0) {
                            pop_up_add_information(DIR_CONF_UPDATE,"check_conf_status","information");
                            $("#sync_conf").dialog({
                                resizable: false,
                                height:150,
                                width: 500,
                                closeOnEscape: false,
                                modal: true,
                                dialogClass: "popup_message",
                                buttons: [{
                                    text: CLOSE_button,
                                    click: function () {
                                        $( this ).dialog( "close" ); return false;
                                    }
                                }]
                            });

                        } else {
                            pop_up_add_information("<?php echo __('DIR_CONF_NOT_UPTODATE'); ?>","check_conf_status","error");
                            pop_up_add_information("<?php echo __('DISPLAY_DIFF'); ?>","display_diff","error");
                        }
                        $.unblockUI();
                    },
                    error: function(data) {
                        $.unblockUI();
                    }
                });
            }
        });
    }


    function showDiff() {
        $("#diff_conf_list").html("");
        // block user interface during checking and saving
        $.blockUI({
            message: SAVING+" <img src=\"main/libs/img/waiting_small.gif\" />",
            centerY: 0,
            css: {
                top: '20%',
                border: 'none',
                padding: '5px',
                backgroundColor: 'grey',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .9,
                color: '#fffff'
            },
            onBlock: function() {
                $.ajax({
                    cache: false,
                    async: false,
                    url: "main/modules/external/compare_conf.php"
                }).done(function(data) {
                    $.unblockUI();
                    var objJSON = jQuery.parseJSON(data);
                   
                     
                    toDisplay = "<ul class='list_diff'>";
                    $.each( objJSON, function( key, value ) {
                        toDisplay = toDisplay + "<li><a href class='note_link' name='details_diff_link' target='"+key+"'>" + value['base'] + "</a><div name='details_diff' id='details_diff_"+key+"' class='div_code' style='display:none'></div></li>"
                        diffVal[key]=value['diff'];
                    });
                    toDisplay = toDisplay + "</ul>";
                        
                    $("#diff_conf_list").html(toDisplay);
                    $("#diff_conf_list").dialog({
                        resizable: true,
                        width: 750,
                        closeOnEscape: false,
                        modal: true,
                        dialogClass: "popup_message",
                        buttons: [{
                            text: CLOSE_button,
                            click: function () {
                                 $("#diff_conf_list").dialog('destroy'); return false;
                            }
                        }]
                    });
                });
             }
        });
    }


    $("a[name='details_diff_link']").live('click',function(e) {
        e.preventDefault();
        var show=false;
        if($("#details_diff_"+$(this).attr('target')).css('display')=="none") {
            show=true;
        }

        $("div[name='details_diff']").html();
        $("div[name='details_diff']").css('display','none');

        if(show) {
            $("#details_diff_"+$(this).attr('target')).html(diffVal[$(this).attr('target')]);
            $("#details_diff_"+$(this).attr('target')).show();
        }
    });
    
    //To update the configuration:
    $('div.error').on('click', 'button', function(e) {
        e.preventDefault();
        id=$(this).attr('id');
        if(id=="show_diff_conf") {
            showDiff();
        } else {
            updateConfiguration();
        }
    });

    
    $.ajax({
        cache: false,
        url: "main/modules/external/position.php"
    }).done(function (data) {

        if ( typeof data.split(',')[0] !== "undefined" && data.split(',')[0]) {
            var x = parseInt(data.split(',')[0].replace("\"", ""));
        } else {
            var x = 15;
        }

        if ( typeof data.split(',')[1] !== "undefined" && data.split(',')[1]) {
            var y = parseInt(data.split(',')[1].replace("\"", ""));
        } else {
            var y = 15;
        }

        if ( typeof data.split(',')[2] !== "undefined" && data.split(',')[2]) {
            var wid = parseInt(data.split(',')[2].replace("\"", ""));
        } else {
            var wid = 325;
        }

        if ( typeof data.split(',')[3] !== "undefined" && data.split(',')[3]) {
            reduced  = String(data.split(',')[3].replace("\"", ""));
        } else {
            reduced="False";
        }


        $( ".message" ).dialog({
            width: wid,
            closeOnEscape: false,
            resizable: true,
            buttons: [{
                text: HIDE_button,
                id: "button_hide" ,
                click: function() {
                    //Action lors de l'enclenchement du bouton "Cacher" de la boîte de messages:

                    //Fermeture de la boîte de dialogue
                    $( this ).dialog( "close" );

                    //Mise a True de la variable de session TOOLTIP_MSG_BOX qui définit si on doit afficher ou non la boîte de message lorsqu'on change de page
                    $.ajax({
                            cache: false,
                            url: "main/modules/external/set_variable.php",
                            data: {name:"tooltip_msg_box", value: "True", duration: 2592000}
                    });


                    //Affichage de l'oeil permettant de réafficher la boîte de message:
                    // On affiche l'oeil sur le bord exterieur gauche de l'interface. La position dépend de la résolution de l'écran.
                    // On récupère donc la taille de l'écran et la taille du div central de l'interface pour afficher l'oeil au bon endroit:
                    // position = 90*(taille de l'écran - taille du div central)/200
                    // taille de l'écran - taille du div central /2 ==> correspond à la taille de la marge à gauche (ou à droite) séparant le bord du div central
                    // on affiche donc l'oeil a 90% de la marge exterieure

                    var element_div_width=$("#maininner").width();
                    var element_body_width=$( window ).width();

                    if((element_body_width!="")&&(element_div_width!="")) {
                        var dist=90*(element_body_width-element_div_width)/200;
                        $("#tooltip_msg_box").css("padding-left",dist+"px");
                    }


                    //On l'oeil et son tooltip:
                    $("#eyes_msgbox").attr('title', title_msgbox);
                    $("#tooltip_msg_box").fadeIn("slow");
                }
            },
            {
                text: REDUCE_button,
                id: "button_reduce" ,
                click: function() {
                    if(reduced=="True") {
                        $(this).dialog('option', 'height', 'auto');
                        $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(REDUCE_button);
                        var tmp_reduced="False";
                    } else {
                        $(this).dialog('option', 'height', 10);
                        $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(EXTEND_button);
                        var tmp_reduced="True";
                    }
                    var tmp = $(".message").dialog( "option", "position" );
                    var width = $(".message").dialog( "option", "width" );

                    $.ajax({
                    cache: false,
                    url: "main/modules/external/position.php",
                    data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: tmp_reduced }
                    });
                    reduced=tmp_reduced;
            }}],
            hide: "fold",
            dialogClass: "dialog_message",
            position: [x,y],
            dragStop: function( event, ui ) {
                if(data!="") {
                    var tmp = $(".message").dialog( "option", "position" );
                    var width = $(".message").dialog( "option", "width" );
                    $.ajax({
                        cache: false,
                        url: "main/modules/external/position.php",
                        data: { POSITION_X: tmp[0], POSITION_Y: tmp[1], WIDTH: width, REDUCED: reduced }
                        });
                }
            },  
            create: function( event, ui ) {
                if(reduced=="True") {
                    $(this).dialog('option', 'height', 10);
                    $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(EXTEND_button);
                }
            },
            resizeStop: function( event, ui ) {
                if(data!="") {
                    var tmp = $(".message").dialog( "option", "position" );
                    var width = $(".message").dialog( "option", "width" );

                    $.ajax({
                        cache: false,
                        url: "main/modules/external/position.php",
                        data: { WIDTH: width, POSITION_X: tmp[0], POSITION_Y: tmp[1],WIDTH: width, REDUCED: reduced }
                    });

                    reduced="False";
                    $(this).parent().find(".ui-dialog-buttonset .ui-button-text:eq(1)").text(REDUCE_button);
               }
            } ,
            open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
         });
         $(".message").dialog().parent().css('position', 'fixed');
    });




    //To change the language dynamically:
    $('li.translate a').click(function(e) {
        e.preventDefault();
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"lang", value: $(this).attr('id'),duration: 31536000}
        });
        window.location = "/bulcky/";
    });

     // Au chargement d'une page, on vérifie la variable tooltip_msg_box qui détermine si on doit affiche la boîte de message ou l'oeil:
    $.ajax({
        cache: false,
        url: "main/modules/external/get_variable.php",
        data: {name:"tooltip_msg_box"}
    }).done(function (data) {
        //Si l'oeil doit être affiché:
        if(jQuery.parseJSON(data)=="True") {
            //On cache la boîte de message:
            $(".message").dialog("option", "hide",{ effect: "slideUp", duration: 0 } );
            $(".message").dialog('close');


            //Affichage de l'oeil permettant de réafficher la boîte de message:
            // On affiche l'oeil sur le bord exterieur gauche de l'interface. La position dépend de la résolution de l'écran.
            // On récupère donc la taille de l'écran et la taille du div central de l'interface pour afficher l'oeil au bon endroit:
            // position = 90*(taille de l'écran - taille du div central)/200
            // taille de l'écran - taille du div central /2 ==> correspond à la taille de la marge à gauche (ou à droite) séparant le bord du div central
            // on affiche donc l'oeil a 90% de la marge exterieure

            var element_div_width=$("#maininner").width();
            var element_body_width=$( window ).width();
            
            if((element_body_width!="")&&(element_div_width!="")) {
                var dist=90*(element_body_width-element_div_width)/200
                $("#tooltip_msg_box").css("padding-left",dist+"px");
            }   
       

            //Affichage de l'oeil et de son tooltip: 
            $("#eyes_msgbox").attr('title', title_msgbox);
            $("#tooltip_msg_box").show();
        } else {
            //Sinon on cache l'oeil - la boîte de messages étant par défaut affichée:
            $("#tooltip_msg_box").css("display","none");
        }
    });


  $(".pop_up_message").dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [{ text: CLOSE_button, click: function() { $( this ).dialog( "close" ); if(typeof anchor != 'undefined') {  $('html, body').animate({ scrollTop: $("#"+anchor).offset().top}, 300); } } } ], hide: "fold", modal: true,  dialogClass: "popup_message"  });
   $( ".pop_up_error" ).dialog({ width: 550, resizable: false, closeOnEscape: false, buttons: [ { text: CLOSE_button, click: function() { $( this ).dialog( "close" ); if(typeof anchor != 'undefined') {  $('html, body').animate({ scrollTop: $("#"+anchor).offset().top}, 300);  } } } ], hide: "fold", modal: true,  dialogClass: "popup_error" });


    //Lors du click sur l'oeil - on doit cacher l'oeil et afficher la boîte de messages:     
    $("#tooltip_msg_box").click(function(e) {
        e.preventDefault();
        //On positionne le COOKIE tooltip_msg_box qui détermine si on doit afficher l'oeil ou pas lors du chargement d'une page
        // en fonction des actions utilisateurs. La variable est positionnée à False, au chargement d'une page l'oeil sera donc caché et 
        // la boîte de messages affichée.
        $.ajax({
            cache: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"tooltip_msg_box", value: "False", duration: 2592000}
        });

        // On cache l'oeil et on affiche la boîte de messages
        $("#tooltip_msg_box").fadeOut("slow");
        $(".message").dialog("open");
    });

    if ($("#pop_up_error_part ul li").length < 1)
        $("#pop_up_error_container").css("display", "none");

    if ($("#pop_up_information_part ul li").length < 1)
        $("#pop_up_information_container").css("display", "none");

});
