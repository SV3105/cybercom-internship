<?php
function diamond($n){
    
    $mid = ceil($n / 2);
    echo "<pre>";
    for($i=1; $i<=($mid); $i++){
       for($j=($mid-$i); $j>0; $j--){
         echo " ";
       }
       for($k=1; $k<=((2*$i)-1); $k++){
        echo "*";
       }
    
       echo "<br>";
    }
    for($i=$mid-1; $i>0; $i--){
        for($j=1; $j<=($mid-$i); $j++){
            echo " ";
        }
        for($k=((2*$i)-1); $k>0; $k--){
            echo "*";
        }
        echo "<br>";
    }
     echo "</pre>";
}


 
?>