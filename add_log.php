<?php
session_start();
require_once '../../includes/db.php';
header('Content-Type: application/json');

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ✅ Get and sanitize input
$data = json_decode(file_get_contents("php://input"), true);
$subject = mysqli_real_escape_string($conn, trim($data['subject']));
$duration = (int) $data['duration'];
$study_date = mysqli_real_escape_string($conn, trim($data['study_date']));
$user_id = $_SESSION['user_id'];

// ✅ Validate fields
if (empty($subject) || $duration <= 0 || empty($study_date)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid.']);
    exit;
}

// ✅ Optional: Prevent multiple identical logs on same date+subject (optional but helpful)
$check_sql = "SELECT id FROM study_logs WHERE user_id = ? AND subject = ? AND study_date = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, 'iss', $user_id, $subject, $study_date);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    echo json_encode(['success' => false, 'message' => 'You already logged this subject today.']);
    exit;
}

// ✅ Insert into DB
$insert_sql = "INSERT INTO study_logs (user_id, subject, duration_minutes, study_date)
               VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $insert_sql);
mysqli_stmt_bind_param($stmt, 'isis', $user_id, $subject, $duration, $study_date);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Study log added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add study log.']);
}
