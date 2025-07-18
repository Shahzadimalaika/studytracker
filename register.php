<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// ✅ Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// ✅ Parse JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ✅ Handle invalid JSON
if (!$data || !is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
    exit;
}

// ✅ Extract and sanitize inputs safely
$username = mysqli_real_escape_string($conn, trim($data['username'] ?? ''));
$email = mysqli_real_escape_string($conn, trim($data['email'] ?? ''));
$password = trim($data['password'] ?? '');

// ✅ Validate required fields
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// ✅ Check for existing email
$check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($check, 's', $email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
    mysqli_stmt_close($check);
    exit;
}
mysqli_stmt_close($check);

// ✅ Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// ✅ Insert user into DB
$stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Registration successful.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed.']);
}

mysqli_stmt_close($stmt);
