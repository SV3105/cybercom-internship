<?php
    interface logger {
        public function log($message);
    }

    abstract class account{
        protected $accountNumber;
        protected $balance;
        function __construct($accountNumber, $balance){
            $this->accountNumber = $accountNumber;
            $this->balance = $balance;
        }

        abstract function deposit($amount);
        abstract function withdraw($amount);

        function getBalance(){
            echo "balance: ".$this->balance."<br>";
        }
    }

    class savingsAccount extends account implements logger{
        public $interestRate = 0.05;

        public function log($message){
            echo $message . "<br>";
        }

        public function withdraw($amount){
            if($this->balance - $amount < 500){
                $this->log("Transaction Failed");
            }
            else{
                $this->balance -= $amount;
                $this->log("Withdrawn: ".$amount);
            }
        }

        function applyInterest(){
            $interest = $this->balance * $this->interestRate;
            $this->balance += $interest;
            $this->log("Interest Applied: ". $interest); 
        }

        public function deposit($amount){
            if($amount>0){
                $this->balance += $amount  ;
                $this->log("Deposited: ".$amount); 
               
            }
        }
    }

    class CurrentAccount extends account implements logger {
       public $overdraftLimit = 1000;

       public function log($message){
            echo $message . "<br>";
        }

        function withdraw($amount){
            if($this->balance + $this->overdraftLimit >= $amount){
                $this->balance -= $amount;
                
                $this->log("Withdrawn: ".$amount);
            }
            else{
                 $this->log("Transaction Failed: Overdraft limit exceeded");
            }
        }
        public function deposit($amount){
            if($amount>0){
                $this->balance += $amount  ;
                $this->log("Deposited: ".$amount); 
            }
        }
    }

    $ps1 = new savingsAccount(1234, 15000);
    $ps1->getBalance();
    $ps1->withdraw(200);
    $ps1->getBalance();
    $ps1->applyInterest();
    $ps1->deposit(5000);
    $ps1->getBalance();

    echo "<br>";

    $pc1 = new CurrentAccount(5265, 200);
    $pc1->getBalance();
    $pc1->withdraw(1200);
    $pc1->getBalance();
    $pc1->deposit(10000);
    $pc1->getBalance();

?>
