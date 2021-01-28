<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
/*if ($_SERVER["HTTP_HOST"] == "localhost") {
    require_once('Constants.dev.php');
} else {
    require_once('Constants.prod.php');
}*/
require_once('Constants.prod.php');
require_once('mywrap_result.php');
require_once('mywrap_connection.php');