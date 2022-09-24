<?php
require 'import.php';

if(!isset($_SESSION["type_data"]) && (!isset($_SESSION["db_credentials"]) && !file_exists('db_credentials.json'))){
    ?><script>window.location.href = 'setup.php'</script><?php
}else{
    $GLOBALS["db_credentials"] = json_decode($_SESSION["db_credentials"]);
    CommonDB::connection();
}