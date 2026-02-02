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

function pascalTriangle($rows){

echo "<pre>";

$triangle = [];

for($i=0; $i<=$rows; $i++){
    for($j=($rows-$i); $j>0; $j--){
        echo " ";
    }
    for($k=0; $k<=$i; $k++){
        if($k == 0 || $k == $i){
            $sum = 1;
        }
        else{
            $sum = $triangle[$i-1][$k-1] + $triangle[$i-1][$k];
        }

        $triangle[$i][$k] = $sum;
        echo $sum . " ";
    }
    echo "<br>";
}

echo "</pre>";
}


// diamond(7);
// numberedDiamond(9);
// butterflyPattern(10);
// pascalTriangle(4);

?>

