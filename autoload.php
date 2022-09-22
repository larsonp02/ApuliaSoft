<?php

foreach (glob('MainObj/*.php') as $file){
    if(
        str_replace('MainObj/', '', $file) != "MainOBJ.php" &&
        str_replace('MainObj/', '', $file) != "DBConnectOBJ.php"
        ){
            // echo str_replace('MainObj/', '', $file)."<br>"; 
            require_once $file ;
        }
        // echo $file."<br>";
    }