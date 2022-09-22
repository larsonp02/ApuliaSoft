<?php
require 'require.php';
include 'header.php';

if(isset($_GET["logout"])){
    session_destroy();
    unlink('db_credentials.json');
    ?><script>window.location.href = 'index.php';</script><?php
}

$obj = $_GET['object'];
$obj = new $obj();
$resultObj = $obj->getObj();
$field = array();
?>


<div class="container container-table text-center">
    <div class="row">
        <div class="col-sm">

        <table class="table  table-hover">
        <thead class="table-dark">
            <tr>
                <?php foreach ($obj->getTable_fields() as $column) {
                     $data_field = new stdClass();
                     $data_field->name = $column->name;
                     $data_field->type = getProperty($column, "type");
                     
                     array_push($field, $data_field);
                    ?>
                    <th scope="col"><?php echo $column->label; ?></th>
                <?php }?>
            </tr>
          </thead>
          <tbody>
            <tr>
                <?php 
                if(count($resultObj) == 0){ ?>
                    <td colspan="<?php echo count($field) ?>">Nessun Risultato Trovato</td>
                <?php }else{ ?>
                    <?php for ($i = 0; $i < count($resultObj); $i++) {  ?> 
                            <tr style="cursor: pointer;">
                            <?php for ($j = 0; $j < count($field); $j++){ ?>
                                <td><?php echo data_format($resultObj[$i]->{$field[$j]->name}, $field[$j]->type)  ?></td>
                            <?php } ?>
                        </tr> 
                    <?php } ?>
                <?php } ?>
            </tr>
          </tbody>
        </table>
        </div>
    </div>
    <!-- <div class="col-xs-12 p-4">
        <div class="text-end"><a target="_blank" href="set.php?<?php echo setParams($obj->name) ?>"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i>&emsp;Aggiungi</button></a></div>
    </div> -->
    <!-- <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                
                <thead>
                    <tr>
                        <?php $tables = $obj->table_fields;
                         for ($i = 0; $i < count($tables); $i++) { ?>
                            <?php if(getProperty($tables[$i], "isViewable") !== false) { 
                                
                                $data_field = new stdClass();
                                $data_field->name = $tables[$i]->name;
                                $data_field->type = getProperty($tables[$i], "type");
                                
                                array_push($field, $data_field);
                                
                                ?>
                                <th scope="col"><?php echo $tables[$i]->label; ?></th>
                            <?php }?>
                        <?php }?>
                    </tr>
                </thead>

                <tbody>
                    <?php if(checkFields($field, $resultObj)){ ?>
                        <?php if(count($resultObj) == 0 && get("getError") == 1){ ?>
                            <td colspan="<?php echo count($field) ?>"><?php echo $obj->getTempQuery() ?></td>
                        <?php }elseif(count($resultObj) == 0){ ?>
                            <td colspan="<?php echo count($field) ?>">Nessun Risultato Trovato</td>
                        <?php }else{ ?>
                            <?php for ($i = 0; $i < count($resultObj); $i++) {  ?> 
                                    <tr style="cursor: pointer;">
                                    <?php for ($j = 0; $j < count($field); $j++){ ?>
                                        <td><a href="details.php?<?php echo setParams($obj->name, $resultObj[$i]->id) ?>"> <?php echo data_format($resultObj[$i]->{$field[$j]->name}, $field[$j]->type)  ?></a></td>
                                    <?php } ?>
                                </tr> 
                            <?php } ?>
                        <?php } ?>
                    <?php }elseif(get("getError") == 1){ ?>
                        <tr><td colspan="<?php echo count($field) ?>"><?php echo $obj->getTempQuery() ?></td></tr>
                    <?php }else{ ?>  
                        <tr><td colspan="<?php echo count($field) ?>">ERRORE</td></tr>  
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
</div> -->

<?php
require 'footer.php';

?>

