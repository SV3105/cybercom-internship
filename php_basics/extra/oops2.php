<?php
    trait loggable {
        public function log($msg){
            echo $msg. "<br>";
        }
    }

    class User{
        
        use loggable;
        public $name;
        public static $userCount = 0;

        public function __construct($name){
            $this->name = $name;
            self::$userCount++;
            $this->log("User Created: $this->name");
        }

       
    }

    class Product{
        use loggable;
        public static $productCount = 0;
        public $productName;
        public function __construct($productName){
            $this->productName = $productName;
            self::$productCount++;

            $this->log("Product Added: $this->productName");
        }

    }

    //we can't use this keyword in static methods
    class system{
        public static function getSystemStats(){
            echo("User: ". User::$userCount)."<br>";
            echo("Product: ".Product::$productCount)."<br>";
        }
    }

    $u1 = new User("xyz");
    $u2 = new User("jkl");
    $u3 = new User("rkh");

    $p1 = new Product("table");
    $p2 = new Product("tea");
    $p3 = new Product("book");

    system::getSystemStats();
    
    
?>