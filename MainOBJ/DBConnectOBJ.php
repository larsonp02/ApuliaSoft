<?php 
    

    class CommonDB{
        

        public static $DB_host;
        public static $DB_user;
        public static $DB_password;
        public static $DB_name;
        public static $connection;
        
        
        public static function connection()
        {
            CommonDB::$DB_host = $GLOBALS["db_credentials"]->host;
            CommonDB::$DB_user = $GLOBALS["db_credentials"]->username;
            CommonDB::$DB_password = $GLOBALS["db_credentials"]->password;
            CommonDB::$DB_name = $GLOBALS["db_credentials"]->db;

            CommonDB::$connection = mysqli_connect(CommonDB::$DB_host, CommonDB::$DB_user, CommonDB::$DB_password,CommonDB::$DB_name);           
        }


        public static function result($query, $fields = '', $row = ''){
            $query = mysqli_query(CommonDB::$connection, $query);
            //IMPLEMENTARE *
            if(mysqli_num_rows($query) > 0){
                $i = 0;
                while ($record = mysqli_fetch_assoc($query)){
                    
                    if($row != ''){
                        
                        if($i == $row){
                            if(is_array($fields)){
                                $give = array();
                                foreach($fields as $field){ array_push($give, $record[$field]); }
                                return $give;
                            }else{ return ($fields != '') ? $record[$fields] : ''; }
                        }
                    }else{
                        if(is_array($fields)){
                            $give = array();
                            foreach($fields as $field){ array_push($give, $record[$field]); }
                            return $give;
                        }else{ return ($fields != '') ? $record[$fields] : ''; }
                    }
                    $i++;
                }
            }else{return '';}
        }

        
    }

 ?>