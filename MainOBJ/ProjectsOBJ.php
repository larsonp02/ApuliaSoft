<?php 

class Projects extends Main
{
    public $table_fields = array();
    function __construct() {
        parent::__construct("Projects", "projects");
        // $this->setDeletable(true);


        
        $temp = array();
        array_push($temp, (object)["name" => "name", "type" => "text", "label" => "Nome Progetto"]);
        $this->setTable_fields($temp);

    }

 

    
    
 

   

}











?>