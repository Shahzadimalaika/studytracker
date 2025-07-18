<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Get last 7 days of study logs
$sql = "SELECT study_date, SUM(duration_minutes) AS total_minutes 
        FROM study_logs 
        WHERE user_id = ? 
          AND study_date >= CURDATE() - INTERVAL 6 DAY
        GROUP BY study_date
        ORDER BY study_date ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ✅ Prepare response
$dates = [];
$minutes = [];

while ($row = mysqli_fetch_assoc($result)) {
    $dates[] = $row['study_date'];
    $minutes[] = (int) $row['total_minutes'];
}

echo json_encode([
    'success' => true,
    'dates' => $dates,
    'minutes' => $minutes
]);
