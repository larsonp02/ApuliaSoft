<?php 

        /****************************************************************** */
            //moreCheck regole:

                //  = 'stringa' -> uguale a stringa,
                //  != 'stringa' -> diverso a stringa,
                //  = campo -> uguale a campo (campo della tabella corrente), **Da ragionare**
                //  != campo -> diverso da campo (campo della tabella corrente), **Da ragionare**
                //  = numero -> uguale a numero,
                //  != numero -> diverso da numero,
                //  > numero -> maggiore di numero,
                //  < numero -> minore di numero,
                //  <= numero -> minore uguale di numero,
                //  >= numero -> maggiore uguale di numero
         /****************************************************************** */

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

    public function add($set_fields = array()){
        if($this->check_insert($this->table_fields, $set_fields) === true){
            $query = "INSERT INTO ".$this->table." (";
            foreach($set_fields as $set_field){
                $query .= $set_field->name.",";
            }
            $query = substr($query, 0, -1);
            $query .= ") VALUES (";
            foreach($set_fields as $set_field){
                $query .= (is_numeric($set_field->value)) ? $set_field->value."," : "'".$set_field->value."',";
             }
             $query = substr($query, 0, -1);
             $query .= ")";
             mysqli_query(CommonDB::$connection, $query);
             return mysqli_insert_id(CommonDB::$connection);
             
        }else return $this->check_insert($this->table_fields, $set_fields);
    }

    public function update($set_fields){
        if(intval($this->getid) > 0){
            if($this->check_update($this->table_fields, $set_fields) === true){
                foreach($this->getTable_fields() as $field){
                    foreach($set_fields as $set_field){
                        if($field->name == $set_field->name){
                                $query = "UPDATE ".$this->table." SET ".$set_field->name." = ";
                                $query .= (is_numeric($set_field->value)) ? $set_field->value : "'".$set_field->value."'";
                                $query .= " WHERE id = ".$this->getId();
                                mysqli_query(CommonDB::$connection, $query);
                            }
                        }
                    }
                    return $this->getId();
            }else return 'Edit is not enabled';
        }else return "ID is not setted";
    }

    public function delete(){
        if(intval($this->getId()) > 0 && $this->getDeletable()){
            mysqli_query(CommonDB::$connection, "DELETE FROM ".$this->table." WHERE id = ".$this->getId());
            return "deleted";
        }else return !$this->getDeletable() ? "Delete is not Allowed" : "ID is not Setted";
    }

    public function setObj($mode, $set_fields = array()){
        
        if($mode != ''){
            switch($mode){
                case "insert":
                    return  $this->add($set_fields);
                break;

                case "update":
                    return  $this->update($set_fields);
                break;

                case "delete":
                  return  $this->delete();
                break;
            }
        }else{ return "Query Mode is not setted"; }
    }

    public function check_insert($fields, $set_fields){
        $out = '';
        for($i = 0; $i < count($fields); $i++){
            if(getProperty($fields[$i], "isRequired") == true){
                $moreCheck = getProperty($fields[$i], "moreCheck");
                $check = false;
                for($j = 0; $j < count($set_fields); $j++){
                    if($set_fields[$j]->name == $fields[$i]->name){
                        if($moreCheck != ''){
                            if($this->checkMoreCheck($set_fields[$j], $moreCheck)) $check = true;
                        }else{
                            $check = true;
                        }
                    }
                }
                if(!$check) $out = $fields[$i]->out;
            }
        }
        return ($check) ? true : $out;
    }
    
    
    public function check_update($fields, $set_fields){
        $out = false;
        for($j = 0; $j < count($set_fields); $j++){
            $out = false;
            for($i = 0; $i < count($fields); $i++){
                if($fields[$i]->name == $set_fields[$j]->name){
                    if(getProperty($fields[$i], "isEditable") == true){
                        $moreCheck = getProperty($fields[$i], "moreCheck");
                        if($moreCheck != ''){
                            if($this->checkMoreCheck($set_fields[$j], $moreCheck)) $out = true;
                        }else{
                            $out = true;
                        }
                    }
                }
            }
            
        }
        return $out;
    }
    
    
    public function checkMoreCheck($set_field, $moreCheck){
        $compared = '';
        switch($moreCheck){
            case strpos($moreCheck, ">") !== false && strpos($moreCheck, "=") === false && strpos($moreCheck, "<") === false && strpos($moreCheck, "!") === false: $compare = '>'; break;
            case strpos($moreCheck, ">=") !== false && strpos($moreCheck, "<") === false && strpos($moreCheck, "!") === false: $compare = '>='; break;
            case strpos($moreCheck, "<") !== false && strpos($moreCheck, "=") === false && strpos($moreCheck, ">") === false && strpos($moreCheck, "!") === false: $compare = '>'; break;
            case strpos($moreCheck, "<=") !== false && strpos($moreCheck, ">") === false && strpos($moreCheck, "!") === false: $compare = '>='; break;
            case strpos($moreCheck, "=") !== false && strpos($moreCheck, ">") === false && strpos($moreCheck, "<") === false && strpos($moreCheck, "!") === false: $compare = '=='; break;
            case strpos($moreCheck, "!=") !== false && strpos($moreCheck, "<") === false && strpos($moreCheck, ">") === false: $compare = '!='; break;
        }
        $str_compare = substr($moreCheck, strlen($compare));
        $str_compare = str_replace(" ", "", $str_compare);
    
        if(!is_numeric($str_compare) && !is_numeric($set_field->value)){
            if($compare == '==' || $compare == '!='){
                if(strpos($str_compare, "'") !== false){
                    $compared = "'".$set_field->value."' ".$compare." ".$str_compare;
                }else{
                    // $compared = "'".$fields[$i]."' ".$compare." '".$fields[$i]->$str_compare."'";
                }
            }
        }else{ 
            $compared = $set_field->value." ".$compare." ".$str_compare;
        }
    
        return eval("return $compared;");
    }


    public function details(Array $fks = array()){  
        // ->name
        // ->fields with properties (editable required)
        $obj = new $this->name;
        $obj->setId($this->getId());
        $rObj = $obj->getObj(true);
        $obj_details = array();
        $fields = $this->getTable_fields(true);
        for($i = 0; $i < count($fields); $i++){
            $values = $fields[$i];
            $name = $values->name;
            $values->value = $rObj->$name;
        }
        
        $obj_det = new stdClass();
        $obj_det->name = "Informazioni Principali";
        $obj_det->fields = $this->getTable_fields(true, true);
        array_push($obj_details, $obj_det);
        $thisObj = $this->getObj();
        foreach($fks as $fk){
            $objName = getObjNameFromFk($fk);
            $obj = new $objName();
            $obj->setId(intval($thisObj->$fk));
            $istance = $obj->getObj();
            $istance_fields = $obj->getTable_fields(true, true);
            $fk_obj = new stdClass();
            $fk_obj->name = ucfirst($obj->name);
            foreach($istance_fields as $field){
                $name = $field->name;
                $field->value = $istance->$name;
            }
            $fk_obj->fields = $istance_fields;
            array_push($obj_details, $fk_obj);
        }
        return $obj_details;

    }




























    
    /***************************************************************************************************/
    // public function getFromCustomQuery($query){
    //     $result = mysqli_query(CommonDB::$connection,$query);
    //     $rfields = array();
    //     $info = array();
    //     if(mysqli_num_rows($result) > 0){
            
    //         while ($row = mysqli_fetch_assoc($result)){
    //             while ($field = mysqli_fetch_field($result)){
    //                 $rfields[$field->name] = $row[$field->name];
    //             }
    //             array_push($info, (object)$rfields);
    //             $rfields = array();
    //         }
    //     }
    
    //     return (object)$info;
        
           
        
    // }



    // public function getTable(){
    //     $sql = "SELECT (COLUMN_NAME) as 'col' FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->table."' ORDER BY ORDINAL_POSITION;";
    //     $result = mysqli_query(CommonDB::$connection,$sql);
    //     $col_name = array();
    //     $val = array();
    //     $info = array();
    //     if(mysqli_num_rows($result) > 0){
            
    //         while ($row = mysqli_fetch_assoc($result)){
    //             array_push($col_name, $row["col"]);
                
    //         }
    //     }
      
    //     $sql = "SELECT * FROM ".$this->table." WHERE id = ".$this->id;
        
    //     for($i=0;$i<count($col_name);$i++){
    //         array_push($val, CommonDB::result($sql, $col_name[$i]));  
    //     }

    //     for($i=0;$i<count($col_name);$i++){ 
           
    //         $info[$col_name[$i]] = $val[$i];
        
    //     }
    
    //     return (object)$info;
        
    // }

    


}
