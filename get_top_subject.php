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

// ✅ Fetch top 3 subjects by total minutes
$sql = "
    SELECT subject, SUM(duration_minutes) AS total_minutes
    FROM study_logs
    WHERE user_id = ?
    GROUP BY subject
    ORDER BY total_minutes DESC
    LIMIT 3
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$subjects = [];
$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $subjects[] = $row;
    $total += $row['total_minutes'];
}

// ✅ Add percentage to each subject
foreach ($subjects as &$s) {
    $s['percentage'] = $total > 0 ? round(($s['total_minutes'] / $total) * 100) : 0;
}

echo json_encode([
    'success' => true,
    'subjects' => $subjects
]);
