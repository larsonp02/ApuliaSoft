<?php 

class Register extends Main
{
    public $table_fields = array();
    function __construct() {
        parent::__construct("Register", "register");
        // $this->setDeletable(true);

        $this->setQuery("SELECT *, (SELECT name FROM employees WHERE id = cod_employees) as employee, (SELECT name FROM projects WHERE id = cod_projects) as project FROM register");

        
        $temp = array();
        array_push(
            $temp, 
            (object)["name" => "employee", "type" => "text", "label" => "Dipendente", "isGrouped" => "true"],
            (object)["name" => "project", "type" => "text", "label" => "Progetto", "isGrouped" => "true"],
            (object)["name" => "hours", "type" => "text", "label" => "Ore", "hasSum" => "true"],
        );
        $this->setTable_fields($temp);

    }

 

    
    
 

   

}











?>