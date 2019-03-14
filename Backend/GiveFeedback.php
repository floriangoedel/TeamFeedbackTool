<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';
require_once "UsefulFunctions.php";

giveFeedback($pdo);
/**
 * @param PDO $pdo
 * for better overview
 */
function giveFeedback(PDO $pdo)
{
    if (checkIfOneWeekOver($pdo) == true) {
        insertIntoFeedback($pdo);

    } else {
        sendError("You gotta wait a week buddy");
    }
}

/**
 * @param PDO $pdo
 * checks if user has not submitted a feedback in the last seven days
 * @return bool
 */

function checkIfOneWeekOver(PDO $pdo){
    $userdata = giveFeedbackInput();
    $sql = "SELECT date FROM feedback WHERE fk_userId = :userId AND fk_projectId = :projectId";
    $sql1 = "SELECT MAX(date) FROM feedback WHERE fk_userId = :userId AND fk_projectId = :projectId";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt1 = $pdo->prepare($sql1);

        $param_userId = $_SESSION['userId'];
        $param_projectId = $userdata['projectId'];

        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);

        $stmt1->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        $stmt1->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);

        if ($stmt1->execute()) {
            $stmt->execute();
            if ($stmt->rowCount() == 0 && $stmt1->rowCount() == 1) {
                    //sendSuccess($stmt->rowCount() . " Reihen im 1. Stmt");
                    return true;
            }

            elseif ($stmt->rowCount() == 1 || ($stmt->rowCount() > 1 && $stmt1->rowCount() == 1)) {
                if ($row = $stmt->fetch()) {
                    $dateNow = strtotime(date("Y-m-d"));
                    $dateDB = strtotime($row['date']);
                    //604800 = secs to days * 7 for 7 days apart
                    //sendSuccess($dateNow . " " .  $dateDB);
                    if ($dateNow - $dateDB >= 604800) {
                        return true;
                    } else {
                        //sendError($dateDB);
                       return false;
                    }
                } else {
                    sendError("test1");
                    return false;
                }
            }
            else{
                sendError("Didnt go into if/elseif");
                return false;
            }

        } else {
            sendError("didnt execute");
            return false;
        }
    }
    else{
        sendError("didnt prepare");
        return false;
    }
}

/**
 * @param PDO $pdo
 * takes input and writes it into the db
 */
function insertIntoFeedback(PDO $pdo){
    $userdata = giveFeedbackInput();

    $sql = "INSERT INTO feedback (fk_userId, fk_projectId, sliderValue_stress, sliderValue_motivation, work_performance_satisfied, technicalSkills, date) VALUES (:userId,:projectId,:stress,:motivation,:satisfied,:technicalSkills, CURRENT_DATE)";

    if ($stmt = $pdo->prepare($sql)) {
        $param_userId = $_SESSION['userId'];
        $param_projectId = $userdata['projectId'];
        $param_stress = $userdata['sliderValue_stress'];
        $param_satisfied = $userdata['work_performance_satisfied'];
        $param_technicalSkills = $userdata['technicalSkills'];
        $param_motivation = $userdata['sliderValue_motivation'];

        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':stress', $param_stress, PDO::PARAM_STR);
        $stmt->bindParam(':motivation', $param_motivation, PDO::PARAM_STR);
        $stmt->bindParam(':satisfied', $param_satisfied, PDO::PARAM_STR);
        $stmt->bindParam(':technicalSkills', $param_technicalSkills, PDO::PARAM_STR);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            sendSuccess("Feedback Submitted");
        } else {
            sendError("Something went wrong. Please try again later.");
        }
        unset($stmt);

    }
}