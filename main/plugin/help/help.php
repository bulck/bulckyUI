<!DOCTYPE HTML>
<html>
    <head>
      <title>Documentation Bulcky</title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <meta name="description" content="" />
      <meta name="keywords" content="" />
      <link rel="icon" href="/bulcky/favicon.ico" />
      <link rel="stylesheet" href="/bulcky/main/plugin/help/help.css?v=<?=@filemtime('/bulcky/main/plugin/help/help.css')?>" />
      <script src="/bulcky/js/jquery.min.js?v=<?=@filemtime('/bulcky/js/jquery.min.js')?>"></script>
  </head>
  <body class="page">
    <script>
    $(document).ready(function() {
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
            $('html, body').animate( { scrollTop: $("#banner").offset().top }, 500 );
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
    });

    </script>

        
    <div id="elevator-up" class="elevator-up">
        <a href="" id="arrow_up" title="Haut de page" class="icon-elevator fa-arrow-up" style="visibility:hidden"></a>
    </div>

    <div id="elevator-down" class="elevator-down">
        <a href="" style="visibility:hidden" title="Bas de page" id="arrow_down" class="icon-elevator fa-arrow-down"></a>
    </div>

        <!-- Banner -->
        <div id="banner">
        <div class="separator"></div>
         <img src="/bulcky/images/bulck.png" class="logo_bulck" alt="" />
        <div class="separator"></div>

        </div>

        <!-- Highlights -->
        <div class="nav">
                <br />
                 <p class="subtitle_right"><b><i><a href="/bulcky/main/plugin/help/help.php">Retour au sommaire</a></i></b></p>
                <?php

                // Ouverture du fichier
                $wiki = "Home";
                if(isset($_GET['wiki']) && !empty($_GET['wiki']))
                {
                    $wiki = $_GET['wiki'];
                }


                require_once('./Michelf/Markdown.inc.php');

                $parser = new Michelf\Markdown;
                $parser->url_filter_func = function ($url) {
                    if (strpos($url, "http") !== false)
                    {
                        return $url;
                    }
                    else if (strpos($url, "img/") !== false)
                    {
                        return "./bulcky.wiki/" . $url;
                    }
                    else
                    {
                        return "help.php?wiki=" . $url;
                    }
                 };
                 $my_html = $parser->transform(file_get_contents("./bulcky.wiki/" . $wiki . ".md"));


                 echo $my_html;

                 ?>
                 <p class="subtitle_right"><b><i><a href="/bulcky/main/plugin/help/help.php">Retour au sommaire</a></i></b></p>
                <br />

       </div>
        <div class="separator" id="footer_sep"></div>

            <!-- Footer -->
            <div id="footer">
                <!-- Copyright -->
                    <div class="copyright">
                        <ul class="menu">
                            <li>Contact : <a href="mailto:info@bulck.fr">info@bulck.fr</a></li>
                            <li>&copy; Bulck SAS. Tout droits réservés</li>
                        </ul>
                    </div>

            </div>
</body>
</html>


