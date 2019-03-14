<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';
require_once 'UsefulFunctions.php';

$userdata = inviteEmployeeInput();
inviteEmployee($pdo,$userdata);

/**
 * simple method to check if given email is in db
 * @param PDO $pdo
 * @param $userdata
 * @return bool gives back true if user exists
 */
function doesUserExist(PDO $pdo, $userdata){
// Prepare a select statement
    $sql = "SELECT pk_userId FROM users WHERE email = :email";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_email = trim($userdata["email"]);
        $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                return true;
            }
        } else {
            //sendError("Oops! Something went wrong. Please try again later.");
            return false;
        }
        // Close statement
        unset($stmt);
    }
    return false;
}

/**
 * "adds" user to project, aka writes their email into "worksat" table
 * @param PDO $pdo
 * @param $userdata
 */

function inviteEmployee(PDO $pdo, $userdata){
    if(doesUserExist($pdo,$userdata) == true){
        writeIntoWorksAt($pdo, emailToUserId($pdo,$userdata), $userdata['projectId']);
        sendSuccess("Mitarbeiter wurde hinzugefügt");
    }
    else{
        sendError("Nutzer existiert nicht");
    }
}
