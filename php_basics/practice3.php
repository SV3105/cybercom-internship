<?php declare(strict_types=1);
// task- PHP-005
$tax = 0.18;

function calculateTotal(float $price, int $qty): float
{
    global $tax;
    
    $subtotal = $price * $qty;
    return $subtotal + ($subtotal * $tax);
}

$total = calculateTotal(500, 3);
echo $total;
echo "<br />";
?>
