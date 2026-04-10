<?php
require_once 'includes/db.php'; 
require_once 'includes/auth.php';
$pageTitle = 'Home';

$stmt = $pdo->prepare("
    SELECT i.*, c.name as cat_name 
    FROM items i 
    JOIN categories c ON i.category_id = c.id 
    ORDER BY i.id ASC 
    LIMIT 4
");
$stmt->execute();
$featured = $stmt->fetchAll();

include 'includes/header.php';
?>



<?php include 'includes/footer.php'; ?>