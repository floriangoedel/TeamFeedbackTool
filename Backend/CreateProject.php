<?php
require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';
require_once 'UsefulFunctions.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

writeIntoProject($pdo, createProjectInput());

/**
 * checks if projectName is unique, if it is, create project -> write into project table
 * @param PDO $pdo
 * @param $userdata
 */
function writeIntoProject(PDO $pdo, $userdata){
    if (empty(trim($userdata["projectName"]))) {
        sendError("Please enter a projectName.");
    } else {
        // Prepare a select statement
        $sql = "SELECT pk_projectId FROM project WHERE projectName = :projectName";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':projectName', $param_projectName, PDO::PARAM_STR);

            // Set parameters
            $projectName = $param_projectName = trim($userdata["projectName"]);
            $param_userId = $_SESSION['userId'];
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    sendError("This projectName is already taken.");
                } else {
                    $_SESSION["projectName"] = $projectName;

                    // Prepare an insert statement
                    $sql = "INSERT INTO project (projectname,fk_leaderId) VALUES (:projectName, :userId)";

                    if ($stmt = $pdo->prepare($sql)) {
                        // Bind variables to the prepared statement as parameters
                        $stmt->bindParam(':projectName', $param_projectName, PDO::PARAM_STR);
                        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);

                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            // instantly logged in if everything was fine
                            writeIntoWorksAt($pdo, $_SESSION['userId'],0);
                        } else {
                            sendError("Something went wrong. Please try again later.");
                        }
                    }
                    unset($stmt);
                }
            } else {
                sendError("Oops! Something went wrong. Please try again later.");
            }
        }
    }
}





















