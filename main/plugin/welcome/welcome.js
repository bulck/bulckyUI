<script>

<?php
    if((isset($sd_card))&&(!empty($sd_card))) {
        echo "sd_card = " . json_encode($sd_card) ;
    } else {
        echo 'sd_card = ""';
    }
?>

var main_error = <?php echo json_encode($main_error); ?>;
var main_info = <?php echo json_encode($main_info); ?>;


console.log(<?php echo $compat ?>);

<?php if(!$compat) { ?>
$(document).ready(
function(){
    $("#compat").fadeIn(3000);
});
<?php } ?>

$(document).ready(function(){
     pop_up_remove("main_error");
     pop_up_remove("main_info");

    // For each information, show it
    $.each(main_error, function(key, entry) {
            pop_up_add_information(entry,"main_error","error");
    });

    // For each information, show it
    $.each(main_info, function(key, entry) {
            pop_up_add_information(entry,"main_info","information");
    });

     if(sd_card=="") {
        $.ajax({
            cache: false,
            async: false,
            url: "main/modules/external/set_variable.php",
            data: {name:"LOAD_LOG", value: "False", duration:36000}
        });
    }

    $("#welcome-log-img").click(function(e) {
       e.preventDefault(); 
       console.log("ohho");
       get_content("logs");
    });


    $("#welcome-log-txt").click(function(e) {
       e.preventDefault();
       get_content("logs");
    });


    $("#welcome-prog-img").click(function(e) {
       e.preventDefault();
       get_content("programs");
    });


    $("#welcome-prog-txt").click(function(e) {
       e.preventDefault();
       get_content("programs");
    });


    $("#welcome-cal-img").click(function(e) {
       e.preventDefault();
       get_content("calendar");
    });


    $("#welcome-cal-txt").click(function(e) {
       e.preventDefault();
       get_content("calendar");
    });

    $("#welcome-synop-img").click(function(e) {
       e.preventDefault();
       get_content("bulcky");
    });


    $("#welcome-synop-txt").click(function(e) {
       e.preventDefault();
       get_content("bulcky");
    });

    
});
</script>

