<?php
//task- PHP-009
class Employee{
     public $name;
     public $salary;  
     
     function __construct($name, $salary){
        $this->name = $name;
        $this->salary = $salary;
     }

     public function getDetails(){
       return "name: " . $this->name . " salary: " . $this->salary;
     }
    }
class Manager extends Employee{
    public $department;

    function __construct($name, $salary, $department){
        parent::__construct($name, $salary);
        $this->department = $department;
    }

    public function getDetails(){
        return parent::getDetails(). " department: " . $this->department;
    }
}

$obj = new Manager("xyz", 10000, "IT");
echo $obj->getDetails(). "<br />";
echo "<br />"
?>

<?php 
class user{
    public $name;
    public $age;
   

    function __construct($name, $age){
        $this->name = $name;
        $this->age = $age;
     
    }

    public function __toString(){
        return json_encode($this);
    }

    public function __get($property){
        return "The property '$property' does not exist.";
    }

    public function __set($property, $value){
        return "$property set to $value";
    }
}


$obj = new user("xyz", 25);

echo $obj. "<br />";
echo $obj->email = "sneha@gmail.com". "<br />";
echo $obj->email;
?>