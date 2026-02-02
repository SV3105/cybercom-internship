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

function numberedDiamond($rows){
$count = 1;
$mid = ceil($rows/2);
echo "<pre>";
for($i=1; $i<=$mid; $i++){
    
    for($j=($mid-$i); $j>0; $j--){
        echo "   ";
    }
    for($k=1;$k<=($i * 2) - 1; $k++){
        
        printf("%3d", $count);
        $count++;
    }
    echo "<br>";
    
}
for($i=$mid-1; $i>0; $i--){
  
    for($j=1; $j<=$mid-$i; $j++){
        echo "   ";
    }
    for($k=(2*$i)-1; $k>0; $k--){
       
        printf("%3d", $count);
        $count++;
    }
    echo "<br>";
}
 echo "</pre>";
}

function butterflyPattern($rows){
$mid = round($rows / 2);
echo "<pre>";
for($i=1; $i<=$mid; $i++){
    for($j=1; $j<=$i; $j++){
        echo "*";
    }
    for($k=2*($mid - $i); $k>0; $k--){
        echo " ";
    }
    for($j=1; $j<=$i; $j++){
        echo "*";
    }
    echo "<br>";
 }
for($i=$mid; $i>0; $i--){
    for($j=$i; $j>0; $j--){
        echo "*";
    }
    for($k=1; $k<=2*($mid-$i); $k++){
        echo " ";
    }
     for($j=$i; $j>0; $j--){
        echo "*";
    }
    echo "<br>";
}
echo "</pre>";
}

// diamond(7);
// numberedDiamond(9);
// butterflyPattern(10);
?>

