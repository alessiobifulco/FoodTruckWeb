<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
$cart_data = json_decode(file_get_contents('php://input'), true);
if (is_array($cart_data)) {
    $_SESSION['cart'] = $cart_data;
    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false]);
}
