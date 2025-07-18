<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// ✅ Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ✅ Fetch total study minutes for today
$sql = "SELECT SUM(duration) FROM study_logs WHERE user_id = ? AND study_date = CURDATE()";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total);
mysqli_stmt_fetch($stmt);

// ✅ Return result
echo json_encode([
    'success' => true,
    'total' => (int) $total
]);

// 🧹 Clean up
mysqli_stmt_close($stmt);
?>