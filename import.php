<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once 'MainOBJ/DBConnectOBJ.php';
require_once 'MainFunctions.php';
require_once 'MainOBJ/MainOBJ.php';
require_once 'autoload.php';

define('FOLDER', dirname($_SERVER["REQUEST_URI"])."/");
define('MAINURL', $_SERVER["SERVER_NAME"].dirname($_SERVER["REQUEST_URI"])."/");