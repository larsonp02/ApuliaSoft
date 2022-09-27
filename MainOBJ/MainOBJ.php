<?php 

class Main
{
    public $name;
    public $table;
    public $info;
    public $id;
    public $table_fields;
    protected $query;
    public $tempQuery;
    public $canView;
    public $canAdd;
    public $isDeletable;
    public $external_field;
    public $plural_label;
    public $add_label;
    

    function __construct($name, $table) {
        $this->name = $name;
        $this->external_field = 'name';
        $this->plural_label = $name;
        $this->add_label = $name;
        $this->table = $table; 
        $this->table_fields = array();
        $this->id = 0;
        $this->canView = true;
        $this->canAdd = true;
        $this->isDeletable = true;
        $this->tempQuery = "SELECT * FROM ".$this->table;
        $this->query = "SELECT * FROM (".$this->getTempQuery().") AS ".$this->table;

        
        if(count($this->getTable_fields()) == 0){
            $sql =  $this->query;
            $result = mysqli_query(CommonDB::$connection,$sql);
            $temp = array();
            while ($field = mysqli_fetch_field($result)){
                if($field->name != 'id'){
                    array_push($temp, (object)["name" => $field->name, "label" => $field->name, "isRequired" => true, "out" => "Inserire ".ucfirst($field->name), "isEditable" => true]);
                }
            }
            $this->setTable_fields($temp);
            
        }        
      }

    public function setId(Int $id){ 
        $this->id = $id;
        $this->setMainQuery($this->getQuery()." WHERE ".$this->table.".id = ".$this->getId()); 
    }
    public function getId(){ return $this->id; }

    public function setTable_fields($table_fields) {
        $temp = array((object)["name" => "id", "isRequired" => false, "isEditable" => false, "isViewable"=>false]);
        foreach($table_fields as $field){
            if(strlen(getProperty($field, 'type')) == 0){
                $field->type = $this->getDataType($field->name);
            }
        }
        $this->table_fields = array_merge($temp, $table_fields);
    }
    public function getTable_fields(Bool $woutId = true, Bool $woutFks = false){
        $fields = $this->table_fields;
        $result = $fields;
        $fe = array();
        for($i = 0; $i < count($result); $i++){
            if($woutId === true && $woutFks === true){
                if($result[$i]->name != "id" && strpos($result[$i]->name, "fk_") === false){
                    array_push($fe, $result[$i]);
                }
            }elseif($woutFks === true){
                if(strpos($result[$i]->name, "fk_") === false){
                   if(!in_array($result[$i], $fe)){array_push($fe, $result[$i]);}
                }
            }elseif($woutId === true){
                if($result[$i]->name != "id"){
                   if(!in_array($result[$i], $fe)){array_push($fe, $result[$i]);}
                }
            }
           
        }
        return (!$woutId && !$woutFks) ? $result : $fe; 
    }

    public function getPropByName($fieldName){
        foreach($this->getTable_fields() as $field){
            if($field->name == $fieldName){
                return $field;
            }
        }
    }

    public function getDataType($col_name){
        return CommonDB::result("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE table_name = '".$this->table."' AND COLUMN_NAME = '".$col_name."'", "DATA_TYPE");
    }

    public function getQuery(){ return $this->query; }
    public function getTempQuery(){ return $this->tempQuery; }
    public function setMainQuery($customQuery){ $this->query = $customQuery; }

    public function setDeletable($deletable) { $this->isDeletable = $deletable;}
    public function getDeletable() { return $this->isDeletable;}

    public function setQuery($customQuery){ 
        $this->tempQuery = $customQuery; 
        $this->setMainQuery("SELECT * FROM (".$this->getTempQuery().") AS ".$this->table);
    }

    public function getObj(Bool $woutId = false){
       
        $sql = $this->query;
        $result = mysqli_query(CommonDB::$connection,$sql);
        $info = array();
        $fields = array();
        while ($field = mysqli_fetch_field($result)){
            $field_data = new stdClass();
            $field_data->name = $field->name;
            $field_data->type = $this->getDataType($field->name);
            array_push($fields, $field_data);     
        }
        if(mysqli_num_rows($result) > 0){
                for($i = 0; $i < mysqli_num_rows($result); $i++){
                    $record = new stdClass();
                    foreach($fields as $field){
                        $record->{$field->name} = CommonDB::result($sql, $field->name, $i);
                    }
                    array_push($info, $record);
                }
            $info = ($this->id > 0) ? $info[0] : $info;
            return ($woutId === true) ? array_remove((array)$info, "id", true) : $info;
        }              
    }

}
