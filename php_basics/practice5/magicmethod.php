<?php 
    class userAccount{
        private $name;
        private $email;
        private $status;

        function __construct($name, $email, $status="Active"){
            $this->name = $name;
            $this->email = $email;
            $this->status = $status;

            echo("user session started<br>");

        }

        function __set($prop, $value){
            $this->$prop = $value;
        }

        function __get($prop){
            if(isset($this->$prop)){
                echo "$this->$prop"."<br>";
            }
            else{
                echo "not found<br>";
            }
        }

        function __toString(){
            return "Username: {$this->name}<br>
            Email: {$this->email}<br>
            Status: {$this->status}<br>";
        }

        function __destruct(){
            echo "<br>";
            echo "user session ended<br>";
        }

    }

    $u1 = new userAccount("abc", "abc@hm.hjk");
    echo "$u1";
    echo "$u1->age";
    $u1->age = 30;
    $u1->name = "tyh";
    $u1->email="dfg@ui.hjl";
    $u1->status="Offline";
    echo "$u1";
    echo $u1->age;

?>