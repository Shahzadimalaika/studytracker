<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$sql = "
  SELECT study_date, SUM(duration_minutes) as total
  FROM study_logs
  WHERE user_id = ?
    AND MONTH(study_date) = MONTH(CURRENT_DATE())
    AND YEAR(study_date) = YEAR(CURRENT_DATE())
  GROUP BY study_date
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[$row['study_date']] = (int)$row['total'];
}

echo json_encode(['success' => true, 'logs' => $data]);
