<?php
require 'vendor/autoload.php';
require 'app/core/database.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=jobdating', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking announcements table structure:\n";
    echo "=====================================\n";
    
    $stmt = $db->query('DESCRIBE announcements');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo $col['Field'] . ' - ' . $col['Type'] . ' - ' . $col['Null'] . ' - ' . $col['Default'] . PHP_EOL;
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . PHP_EOL;
}
