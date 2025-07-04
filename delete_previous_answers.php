<?php
require_once 'db/config.php';

$quiz_code = $_GET['code'] ?? '';
$friend_name = $_GET['friend'] ?? '';

if (!empty($quiz_code) && !empty($friend_name)) {
    $stmt = $pdo->prepare("DELETE FROM answers WHERE quiz_code = ? AND friend_name = ?");
    $stmt->execute([$quiz_code, $friend_name]);
}
?>
