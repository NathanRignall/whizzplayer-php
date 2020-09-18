<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    require_once 'lib/vars.php';
    require_once 'lib/sql_connect.php';

    $pageuri = basename(($_SERVER['REQUEST_URI']));
    $pageuri = strtok($pageuri, '?');

    //page vars for include
    if ($pageuri == "playing") {
        $page = 'lib/stats/playing.php';
    } elseif ($pageuri == "time") {
        $page = 'lib/stats/time.php';
    }

    include_once $page;

    flush();
?>