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

// ✅ Get total minutes per subject
$sql = "SELECT subject, SUM(duration_minutes) AS total_minutes 
        FROM study_logs 
        WHERE user_id = ? 
        GROUP BY subject";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ✅ Prepare result arrays
$subjects = [];
$minutes = [];

while ($row = mysqli_fetch_assoc($result)) {
    $subjects[] = $row['subject'];
    $minutes[] = (int) $row['total_minutes'];
}

echo json_encode([
    'success' => true,
    'subjects' => $subjects,
    'minutes' => $minutes
]);
