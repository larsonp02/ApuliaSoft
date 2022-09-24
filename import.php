<?php
session_start();
error_reporting(1);
ini_set('display_errors', E_ALL);

require_once 'MainOBJ/DBConnectOBJ.php';
require_once 'MainFunctions.php';
require_once 'MainOBJ/MainOBJ.php';
require_once 'autoload.php';

// define('FOLDER', dirname($_SERVER["REQUEST_URI"])."/");
define('MAINURL', "http://localhost/ApuliaSoft/");