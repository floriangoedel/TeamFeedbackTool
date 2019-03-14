<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include important files
require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';
require_once 'UsefulFunctions.php';


/**
 * deletes a project based on which project the user trys to delete
 * @param $pdo
 */
function deleteProject($pdo){


}

/**
 * checks if user has enough rights to delete to project
 * "leaderId"
 * @param $pdo
 * @return mixed
 */
function isUserLeader($pdo){
    $projectId = readProjectsForUser($pdo);
    $sql = "SELECT fk_leaderId FROM project WHERE pk_projectId = :projectId";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $projectId;
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                if ($row = $stmt->fetch()) {
                    if($_SESSION['userId'] == $row['fk_leaderId']){
                        sendSuccess("Der User ist der Leader");
                        return true;
                    }
                    sendError("insufficient rights");
                    return false;
                }
            } else {
                sendError("wait what");
                return false;
            }
        }
    }
    return false;

}