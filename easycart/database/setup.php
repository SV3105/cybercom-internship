<?php
// database/setup.php
// Run this script to execute schema.sql and create the database tables.

require_once '../includes/db.php';

echo "Setting up Database Tables...\n";

try {
    $sql = file_get_contents('schema.sql');
    
    // Check if file read was duplicate
    if ($sql === false) {
        throw new Exception("Could not read schema.sql");
    }

    $pdo->exec($sql);
    echo "Tables created successfully!\n";

} catch (Exception $e) {
    echo "Setup Failed: " . $e->getMessage() . "\n";
}
?>
