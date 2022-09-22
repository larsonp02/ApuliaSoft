<?php 

class Projects extends Main
{
    public $table_fields = array();
    function __construct() {
        parent::__construct("Projects", "projects");
        // $this->setDeletable(true);
        $this->plural_label = 'Progetti';
        $this->add_label = 'Nuovo Prodotto';
        $this->single_label = 'Prodotto';


        
        $temp = array();
        array_push($temp, (object)["name" => "name", "type" => "text", "label" => "Nome Progetto"]);
        $this->setTable_fields($temp);

    }

 

    
    
 

   

}











?>