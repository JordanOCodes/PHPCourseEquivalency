<?php
require __DIR__ . '/encryption_and_hashing.php';
require __DIR__ . '/connect_to_database.php';
$lifetime=3600;
session_start();
setcookie(session_name(),session_id(),time()+$lifetime);
if (!isset($_SESSION['session_id'])) {
    header("Location: index.php");
    exit();
} else{
    $auth = decrypt_data(get_session_auth_code($_SESSION["session_id"]), $_SESSION['key'], $_SESSION["iv"]);
    if ($auth != "Faculty") {
        header("Location: index.php");
        exit();
    }
}
?>