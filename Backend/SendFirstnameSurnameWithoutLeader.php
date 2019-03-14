<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'PHPToJSON.php';
require_once 'UsefulFunctions.php';
userIdsToFirstnameSurname($pdo);