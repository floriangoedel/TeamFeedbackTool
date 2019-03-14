<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once "config.php";
include_once "PHPToJSON.php";
include_once "UsefulFunctions.php";

sendProjects($pdo);