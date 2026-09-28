<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

 $image = uploadImage($_FILES['file'] ?? [], '../uploads/');
if ($image) {
    echo json_encode(['location' => $image]);  // "uploads/xxxx.jpg"
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Upload failed']);
}