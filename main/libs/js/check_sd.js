<script type="text/javascript">
$(document).ready(function(){
pop_up_add_information("<?php echo __('WAIT_UPDATED_CONF') ;?> <img src=\"main/libs/img/waiting_small.gif\" />", "check_conf_progress", "information");

$.ajax({
    type: "GET",
    url: "main/modules/external/compare_conf.php",
    async: true,
    beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
    },
    complete: function(jqXHR) {
        var index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) {
            $.xhrPool.splice(index, 1);
        }
    },
    success: function(data, textStatus, jqXHR) {
        // Parse result
        var json = jQuery.parseJSON(data);

        pop_up_remove("display_diff");
        pop_up_remove("check_conf_status");
        
        if(json == "") {
             pop_up_add_information("<?php echo __('DIR_CONF_UPDATE'); ?>","check_conf_status","information");
        } else {
            pop_up_add_information("<?php echo __('DIR_CONF_NOT_UPTODATE'); ?>","check_conf_status","error");
            pop_up_add_information("<?php echo __('DISPLAY_DIFF'); ?>","display_diff","error");

            <?php
             
                // First check if cookie UPDATED_CONF exists
                if (!array_key_exists('UPDATED_CONF', $_COOKIE)) {
                    $_COOKIE['UPDATED_CONF'] = 'False';
                } 
                if(((!isset($_COOKIE['DISABLE_POPUP']))||($_COOKIE['DISABLE_POPUP']!='True')) && $_COOKIE['UPDATED_CONF']=='True') {
            ?>
                $.ajax({
                    cache: false,
                    url: "main/modules/external/set_variable.php",
                    data: {name:"tooltip_msg_box", value: "False", duration: 2592000}
                });

                // On cache l'oeil et on affiche la boîte de messages
                $("#tooltip_msg_box").fadeOut("slow");
                $(".message").dialog("open");
                
                $("#diff_conf").dialog({
                     resizable: false,
                     width: 550,
                     modal: true,
                     closeOnEscape: false,
                     dialogClass: "popup_error",
                     buttons: [{
                         text: "<?php echo __('CLOSE_BUTTON','js'); ?>",
                         id: "close-diff-conf",
                         click: function () {
                            if($("#disable_popup").is(':checked')) {
                                 $.ajax({
                                    cache: false,
                                    async: true,
                                    url: "main/modules/external/set_variable.php",
                                    data: {name:"DISABLE_POPUP", value: "True", duration: 86400 * 365}
                                });
                            }
                            $("#diff_conf").dialog('destroy');
                            
                         }
                    }]
                 });

                 $.ajax({
                     cache: false,
                     async: true,
                     url: "main/modules/external/set_variable.php",
                     data: {name:"UPDATED_CONF", value: "False", duration: 86400 * 365}
                 });
            <?php } ?>
        }

        // Delete information
        pop_up_remove("check_conf_progress");
    }
});

});
</script>
