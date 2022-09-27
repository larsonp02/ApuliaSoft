<?php 


function getProperty($obj,$prop){ return property_exists($obj , $prop) ? $obj->$prop : ''; }


function dd($val){ if(is_array($val)){pd($val);} else{echo "<pre>"; var_dump($val); echo "</pre>"; die;} }
function pd($val){ echo "<pre>"; print_r($val); echo "</pre>"; die; }
function ed($val){ echo $val; die; }

function is_assoc(Array $array){
    return (array_keys($array) !== range(0, count($array) - 1)) ? true : false;
}

function checkFields($fields, $rows){
    $result = 0;
    for($i = 0; $i < count($fields); $i++){
        foreach($rows[0] as $prop => $value){
            if($fields[$i]->name == $prop)$result++;
        }
    }
    return ((count($rows) == 0) ? true : ($result == count($fields))) ? true : false;
}

function array_remove(Array $array, $id, Bool $isObj = false){   
    $result = array();
    if(is_assoc($array)){
        foreach($array as $item => $content){
            if($item != $id) $result += [$item => $content];
        }
    }else{
        for($i = 0; $i < count($array); $i++){
            if($i != $id) array_push($result, $array[$i]);
        }
    }
    return ($isObj) ? (object)$result : $result;
}

function datetime_format($data){
    if(strpos($data, "-") !== false){
        return date('d/m/Y', strtotime($data));
    }elseif(strpos($data, "/") !== false){
        return date('Y-m-d', strtotime($data));
    }else{
        return $data;
    }
}

function data_format($data, $type){
    switch($type){
        case 'euro':
            return "€ ".number_format($data, 2, ",", ".");
        break;

        case 'date':
            return datetime_format($data);
        break;

        case 'datetime':
            return datetime_format($data)." ".date('H:i:s', strtotime($data));
        break;

        default:
            return $data;
        break;
    }
}

function from_dataType_to_inputType($type){
    switch($type){
        case 'euro':
            return 'text'; //da capire
        break;

        case 'date':
            return 'date';
        break;

        case 'datetime':
            return 'datetime-local';
        break;

        default:
            return $type;
        break;
    }
}
?>