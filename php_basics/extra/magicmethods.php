<?php
    class Product{
        private $name;
        private $price;

        public function __construct($name, $price){
            $this->name = $name;
            $this->price = $price;
        }

        public function __toString(){
            $output = "Product Details: <br>";
            foreach($this as $key=>$value){
                $output .= $key .": ". $value . "<br>";
            }
            return $output;
        }

        public function __get($prop){
            if(property_exists($this, $prop)){
                return $this->$prop;
            }
            else{
                return "$prop does not exist";
            }
        }

        public function __set($prop, $val){

            if($prop == 'price'){
                if($val > 0){
                    $this->price = $val;
                } else {
                    echo "Price must be positive<br>";
                }
            } else {
                    $this->$prop = $val;
            }
        }

        public function __call($method, $args){
            echo "Method '$method' does not exist. Arguments: " . implode(", ", $args) . "<br>";
        }
    }

    $p = new Product("Smart Watch", 5000);
    echo $p . "<br>";          
    echo $p->name . "<br>";    
    $p->color = "white" ;
    $p->price = 4500;          
    $p->price = -200; 
    echo "<br>";   
    echo $p . "<br>";  
    $p->jump("high", "fast"); 
    echo "<br>";
    $p->discount = 200;
    echo $p;
?>
