<?php
require 'require.php';
include 'header.php';

if (isset($_GET["logout"])) {
    session_destroy();
    unlink('db_credentials.json');
    ?><script>window.location.href = 'index.php';</script><?php
}
           


    if (isset($_GET['object'])) {

                $obj = $_GET['object'];
                $obj = new $obj();
                if(isset($_POST["sum_field"])){
                    $query = $obj->getTempQuery();
                    if(strpos($query, $_POST["sum_field"]) !== false){
                        $query = str_replace($_POST["sum_field"], "SUM(".$_POST["sum_field"].")", $query);
                    }else{
                        if(strpos($query, "*") !== false){
                            $query = str_replace("SELECT *", "SELECT *, SUM(".$_POST["sum_field"].")", $query);
                        }else{
                            $query = explode(")", $query)[0] . ",SUM(".$_POST["sum_field"].")" . explode(")", $query)[1];
                        }
                    }
                    if(strpos($query, "GROUP BY") !== false){
                        $query = explode("GROUP BY", $query)[0] . "GROUP BY " . implode(",",$_POST["group_fields"]) . "," . explode("GROUP BY", $query)[1];
                    }else{
                        $query .= " GROUP BY ". implode(",",$_POST["group_fields"]);
                    }
                    dd($query);
                }
                $resultObj = $obj->getQuery();
                dd($resultObj);
                $field = array();
                ?>
    <div class="container container-table text-center">
        <div class="row">
            <div class="col-sm">
                <div><b>Aggregazioni Disponibili</b></div>
                <form action="" method="post" id="form_group">
                <?php foreach ($obj->getTable_fields() as $column) {
                    if (getProperty($column, "isGrouped") == true) {
                ?>
                        <input class="form-check-input" type="checkbox" name="group_fields[]" value="<?php echo $column->name ?>"> <?php echo $column->label ?><br>
                <?php }else{ ?>
                            <input type="hidden" name="sum_field" value="<?php echo $column->name ?>">
                <?php }
                } ?>
                </form>
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
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            if (count($resultObj) == 0) { ?>
                                <td colspan="<?php echo count($field) ?>">Nessun Risultato Trovato</td>
                            <?php } else { ?>
                                <?php for ($i = 0; $i < count($resultObj); $i++) {  ?>
                        <tr style="cursor: pointer;">
                            <?php for ($j = 0; $j < count($field); $j++) { ?>
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
    <?php
            }
            require 'footer.php';

    ?>