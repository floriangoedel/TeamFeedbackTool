<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';
require_once "UsefulFunctions.php";

LoginUser($pdo);

/**
 * Simple script that takes user json input, looks user up, if exists and everything good
 * logs user in
 * @param PDO $pdo
 */
function LoginUser(PDO $pdo){

//JSON to $userdata for later use
    $userdata = loginJSONToPHP();
    $password = trim($userdata['password']);

// Prepare a select statement
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
                    $hashed_password = $row['password'];
                    if (password_verify($password, $hashed_password)) {
                        /* Password is correct, so start a new session and
                        save the email, firstname, surname to the session for later use*/
                        saveIntoSession($row);
                        setLatestDate($pdo);
                        sendSuccess("Sie wurden erfolgreich eingeloggt.");

                    } else {
                        // Send an error message if password is not valid
                        sendError('Das eingegebene Passwort ist nicht korrekt.');
                    }
                }
            } else {
                sendError('Es wurde kein Account mit dieser Email gefunden.');
            }
        } else {
            sendError("Ein Fehler ist aufgetreten.");
        }
    }
    // Close connection
    unset($pdo);
}




