<?php

/**
 * simple DB config to connect to DB
 */
$DB_SERVER = "localhost";
$DB_USERNAME = "root";
$DB_PASSWORD = "";
$DB_NAME = "FeedbackTool";

try {
    $pdo = new PDO("mysql:host=" . $DB_SERVER . ";dbname=" . $DB_NAME, $DB_USERNAME, $DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
