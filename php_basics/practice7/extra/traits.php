<?php 
trait connection{
    function conn(){
        echo "connected!";
    }
}

class Database{
    use connection;
}

class API{
    use connection;
}

$call = new Database();
$call->conn();

?>