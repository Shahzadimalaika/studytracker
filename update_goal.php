<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// ✅ Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// ✅ Validate session and input
$userId = $_SESSION['user_id'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
$goal = isset($data['daily_goal']) ? (int)$data['daily_goal'] : 0;

if (!$userId || $goal < 10) {
    echo json_encode(['success' => false, 'message' => 'Invalid goal or unauthorized user.']);
    exit;
}

// ✅ Update user's daily goal
$stmt = mysqli_prepare($conn, "UPDATE users SET daily_goal = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $goal, $userId);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Daily goal updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update goal.']);
}

mysqli_stmt_close($stmt);
?>
