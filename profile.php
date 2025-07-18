<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user profile with daily goal
$sql = "SELECT username, email, created_at, daily_goal FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'user' => [
            'username' => $user['username'],
            'email' => $user['email'],
            'created_at' => $user['created_at'],
            'daily_goal' => (int)$user['daily_goal']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>
