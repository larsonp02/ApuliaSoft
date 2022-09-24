<?php 

class Employees extends Main
{
    public $table_fields = array();
    function __construct() {
        parent::__construct("Employees", "employees");
        // $this->setDeletable(true);


        
        $temp = array();
        array_push($temp, (object)["name" => "name", "type" => "text", "label" => "Nome Dipendente"]);
        $this->setTable_fields($temp);

    }

 

    
    
 

   

}











?>