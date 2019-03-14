<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include important files
require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';
require_once 'UsefulFunctions.php';


registerUser($pdo);
/**
 * registers user if input is valid and user doesnt exist yet
 * @param PDO $pdo
 */
function registerUser(PDO $pdo){
//reads JSON and writes it into $userdata
    $userdata = registerJSONToPHP();

// Prepare a select statement
    $sql = "SELECT pk_userId FROM users WHERE email = :email";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_email = trim($userdata["email"]);
        $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);
        // Set parameters
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                sendError("Diese Email Adresse ist schon in Verwendung.");
            } else {
                $_SESSION["email"] = trim($userdata["email"]);
                //assigning SESSION Variables for later use, if is valid
            }
        } else {
            sendError("Ein Fehler ist aufgetreten.");
        }
        // Close statement
        unset($stmt);
    }

    // Prepare a select statement
    $sql = "SELECT pk_userId FROM users WHERE firstname = :firstname";
    if ($stmt = $pdo->prepare($sql)) {
        // Set parameters
        $param_firstname = trim($userdata["firstname"]);

        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':firstname', $param_firstname, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            //assigning SESSION Variables for later use, if is valid
            $_SESSION["firstname"] = trim($userdata["firstname"]);
        } else {
            sendError("Ein Fehler ist aufgetreten.");
        }
        // Close statement
        unset($stmt);
    }

    // Prepare a select statement
    $sql = "SELECT pk_userId FROM users WHERE surname = :surname";
    if ($stmt = $pdo->prepare($sql)) {
        // Set parameters
        $param_surname = trim($userdata["surname"]);

        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':surname', $param_surname, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            //assigning SESSION Variables for later use, if is valid
            $_SESSION["surname"] = trim($userdata["surname"]);
        } else {
            sendError("Ein Fehler ist aufgetreten.");
        }
        // Close statement
        unset($stmt);
    }

// Prepare an insert statement
    $sql = "INSERT INTO users (email, password, firstname, surname) VALUES (:email, :password, :firstname, :surname)";

    if ($stmt = $pdo->prepare($sql)) {
        $param_email = $userdata['email'];
        $param_firstname = $userdata['firstname'];
        $param_surname = $userdata['surname'];
        $param_password = password_hash($userdata['password'], PASSWORD_DEFAULT); // Creates a password hash

        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);
        $stmt->bindParam(':firstname', $param_firstname, PDO::PARAM_STR);
        $stmt->bindParam(':surname', $param_surname, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // register successful, set LastLogin in db, get all session vars, send success
            setLatestDate($pdo);
            $sql = "SELECT pk_userId, email, password, firstname, surname FROM users WHERE email = :email";
            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $param_email = $userdata['email'];
                $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Check if email exists, if yes then verify password
                    if ($stmt->rowCount() == 1) {
                        if ($row = $stmt->fetch()) {
                            saveIntoSession($row);
                        }
                    }
                }
                sendSuccess("Sie wurden erfolgreich registriert.");
            } else {
                sendError("Ein Fehler ist aufgetreten.");
            }

            unset($stmt);
        }

    }
}