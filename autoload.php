<?php

foreach (glob('MainObj/*.php') as $file){
    if(
        str_replace('MainObj/', '', $file) != "MainOBJ.php" &&
        str_replace('MainObj/', '', $file) != "DBConnectOBJ.php"
        ){
            require_once $file ;
        }
    }