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

// ✅ Fetch distinct study dates (latest first)
$sql = "SELECT DISTINCT study_date FROM study_logs WHERE user_id = ? ORDER BY study_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$dates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $dates[] = $row['study_date'];
}

// ✅ Calculate streak
$streak = 0;
$expectedDate = date('Y-m-d');
foreach ($dates as $date) {
    if ($date === $expectedDate) {
        $streak++;
        $expectedDate = date('Y-m-d', strtotime($expectedDate . ' -1 day'));
    } else {
        break;
    }
}

// ✅ Response
echo json_encode([
    'success' => true,
    'streak' => $streak
]);
