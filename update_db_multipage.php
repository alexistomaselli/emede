<?php
require_once 'config.php';
$db = getDB();

echo "Migrating to Multi-Page System (Adding Menu Columns)...<br>";

try {
    // Add in_menu column
    $db->exec("ALTER TABLE pages ADD COLUMN in_menu INTEGER DEFAULT 1");
    echo "Added 'in_menu' column.<br>";
} catch (Exception $e) {
    echo "Column 'in_menu' might already exist or error: " . $e->getMessage() . "<br>";
}

try {
    // Add menu_order column
    $db->exec("ALTER TABLE pages ADD COLUMN menu_order INTEGER DEFAULT 0");
    echo "Added 'menu_order' column.<br>";
} catch (Exception $e) {
    echo "Column 'menu_order' might already exist or error: " . $e->getMessage() . "<br>";
}

// Update existing pages with sensible defaults
// Ensure 'inicio' is first
$db->exec("UPDATE pages SET menu_order = 1 WHERE slug = 'inicio'");
$db->exec("UPDATE pages SET menu_order = 2 WHERE slug = 'trayectoria'");
$db->exec("UPDATE pages SET menu_order = 3 WHERE slug = 'packaging'");
$db->exec("UPDATE pages SET menu_order = 4 WHERE slug = 'posavasos'");
$db->exec("UPDATE pages SET menu_order = 5 WHERE slug = 'comercial'");
$db->exec("UPDATE pages SET menu_order = 6 WHERE slug = 'galeria'");

echo "Defaults set.<br>";
?>