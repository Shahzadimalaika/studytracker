<?php
session_start();
header('Content-Type: application/json');

// Destroy all session data
session_unset();
session_destroy();

// Optional: delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Return JSON response
echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
