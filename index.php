<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="css/sc_style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="container">
        <?php
            require_once 'song.php';
            echo "<title id='refresh'>$sc_stats->SONGTITLE</title>";
            echo '<div id="history">';
            require_once 'history.php';
            echo '</div>';
        ?>
        </div>
        <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
        <script type="text/javascript">
            $(function() {
                setInterval(getTrackName,10000);
            });
            function getTrackName() {
                $.ajax({
                    url: "song.php"
                })
                .done(function( data ) {
                    $( "#refresh" ).html( data );
                });
                $.ajax({
                    type: "GET",
                    url: "history.php",
                    headers: {'Access-Control-Allow-Origin': '*'},
                    success: function(response){
                        document.getElementById('history').innerHTML = response;
                        return false;
                    },
                    error: function(error){
                        console.log(error);
                        document.getElementById('history').innerHTML = "Networ Error";
                    }
                });
            }
        </script>
    </body>
</html>
