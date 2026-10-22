<?php

session_start();


$host = 'localhost';
$dbname = 'cooking_project';
$username = 'root';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}


function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function show_message($type, $message) {
    $bg_color = $type == 'success' ? '#d4edda' : '#f8d7da';
    $text_color = $type == 'success' ? '#155724' : '#721c24';
    $border_color = $type == 'success' ? '#c3e6cb' : '#f5c6cb';
    
    return '<div style="padding: 15px; background: ' . $bg_color . '; color: ' . $text_color . '; 
            border: 1px solid ' . $border_color . '; border-radius: 5px; margin-bottom: 20px;font-family: \'Tajawal\',sans-serif;">
            ' . $message . '
            </div>';
}

?>
