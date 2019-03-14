<?php
require "config.php";
require "PHPToJSON.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
/**
 * Calls SendUserData(), which sends the data the front end needs
 */

SendUserData();