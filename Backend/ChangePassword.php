<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';

ChangePassword($pdo);

/**
 * changes Password in database to new password
 * @param PDO $pdo
 */
function ChangePassword(PDO $pdo){
    //JSON to $userdata for later use
    $userdata = changePasswordInput();
    if(checkOldPassword($pdo,$userdata) == true) {
        try {
            $email = trim($_SESSION['email']);
            $password = password_hash($userdata['newPassword'], PASSWORD_DEFAULT);
            $sql = ("UPDATE users
                         SET password = :password
                         WHERE email = :email");

            // Prepare statement
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':email', $email);

            // execute the query
            $stmt->execute();

            // echo a message to say the UPDATE succeeded
            sendSuccess("Passwort erfolgreich geändert");

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    else{
        sendError("Something went wrong, sry");
    }

}

/**
 * sub-function of ChangePassword(), checks if old password is correct
 * @param PDO $pdo
 * @param $userdata
 * @return bool returns true if old password was correct
 */
function checkOldPassword(PDO $pdo, $userdata){
    $password = trim($userdata['oldPassword']);
    $sql = "SELECT pk_userId, email, password, firstname, surname FROM users WHERE email = :email";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_email = $_SESSION['email'];
        $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Check if email exists, if yes then verify password
                if ($row = $stmt->fetch()) {
                    $hashed_password = $row['password'];
                    if (password_verify($password, $hashed_password)) {
                        //sendSuccess("Old Password was correct");
                        return true;
                    } else {
                        // Send an error message if password is not valid
                        sendError('The old password you entered was not valid.');
                        return false;
                    }
                }
        }
    }
    else {
        sendError("Something went wrong");
        return false;
    }
    sendError("Something went wrong");
    return false;
}