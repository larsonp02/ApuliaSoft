<?php
require_once 'import.php';
require_once 'header.php';

if(isset($_POST["type_sql"])){
  
    $obj = new stdClass();
    $obj->host = $_POST["host"];
    $obj->username = $_POST["username"];
    $obj->password = $_POST["password"];
    $obj->db = $_POST["db"];

    $GLOBALS["db_credentials"] = $obj;
    


    CommonDB::connection();
    
    if(CommonDB::$connection === false){
        ?><script>alert('Parametri di Accessi Errati!');</script><?php
    }else{

        $_SESSION["type_data"] = 'sql';
        $GLOBALS["type_data"] = 'sql';
        $_SESSION["db_credentials"] = json_encode($obj);
        include_once 'create_tables.php';
        if(isset($_POST["save"])){
            file_put_contents('db_credentials.json', json_encode($obj));
        }else{
            unlink('db_credentials.json');
        }
        ?><script>window.location.href = 'index.php';</script><?php
    }

}

?>


<div class="container setup w-50 text-center">
    <div class="row setup-title">
        <h2 class="col-sm">
            Struttura Dati
        </h2>
    </div>
    <form class="row" method="post">
        <div class="col-sm">
            <button type="submit" id="type_sql" name="type_sql" class="col-sm">
                <i class="bi bi-filetype-sql"></i>
            </button>
            <div class="input-group input-group-sm mb-3 ">
                <input type="text" name="host" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" placeholder="Host">           
                <input type="text" name="username" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" placeholder="Username">       
                <input type="password" name="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" placeholder="Password">        
                <input type="text" name="db"  class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" placeholder="DataBase">        
            </div>
            <div class="form-check form-switch form-check-reverse text-center">
                <input class="form-check-input" type="checkbox" name="save" id="save" style="margin-left:0!important; margin-right:0!important; padding-left:0!important; padding-right:0!important;">
                <label class="form-check-label" for="save" style="margin-left:0!important; margin-right:0!important; padding-left:0!important; padding-right:0!important;">Salva le info di accesso? </label>
            </div>
        </div>
    </form>
</div>


<script>
    $("#type_sql").click(function(){
        $("input[type=text]").prop('required',true);
    });
    $("#type_json").click(function(){
        $("input").prop('required',false);
    });
</script>

<?php require 'footer.php'; ?>