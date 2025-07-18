<?php
$host = 'localhost';
$db = 'study_tracker';
$user = 'root';
$pass = ''; // or your XAMPP password

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
