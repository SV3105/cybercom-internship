<?php 
//PHP-007
class Employee{
     public $name;
     private $salary;  
     
     function __construct($name, $salary){
        $this->name = $name;
        $this->salary = $salary;
     }
}

?>