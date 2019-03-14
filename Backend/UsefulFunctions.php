<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'JSONToPHP.php';
require_once 'PHPToJSON.php';

/**
 * A simple script to write the current date into users table as lastLogin
 * @param $pdo
 */
function setLatestDate(PDO $pdo)
{
    $sql = "UPDATE users 
              SET lastLogin = now()
              WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $_SESSION['email']);
    $stmt->execute();
    $_SESSION['date'] = date("Y-m-d");
}


/**
 * sets $_SESSION array with current relevant userdata
 * @param $row
 */
function saveIntoSession($row)
{
    $_SESSION['email'] = $row['email'];
    $_SESSION['firstname'] = $row['firstname'];
    $_SESSION['surname'] = $row['surname'];
    $_SESSION['userId'] = $row['pk_userId'];
}

/**
 * Inserts into "worksat" table, so we can know which user works on which project
 * @param PDO $pdo
 * @param $userId
 * @param $projectId
 */
function writeIntoWorksAt(PDO $pdo, $userId, $projectId)
{
    //sendSuccess($projectId . " " . $userId);
    if ($projectId == 0) {
        $param_projectId = projectnameToIds($pdo, createProjectInput());
    } else {
        $param_projectId = $projectId;
    }
    if (checkIfUserWorksAt($pdo, $userId, $param_projectId) == true) {
        $param_userId = $userId;
        // Prepare an insert statement
        $sql = "INSERT INTO worksat(pk_fk_userId, pk_fk_projectId) VALUES (:userId, :projectId)";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                //sendSuccess("writeIntoWorksAt worked");
            } else {
                //sendError("writeIntoWorksAt didnt work.");
            }
        }
        // Close statement
        unset($stmt);
    } else {
        sendError("User arbeitet bereits in diesem Projekt");
    }
}

/**
 * This function returns true if $userId does not work in $projectId yet
 * @param PDO $pdo
 * @param $userId
 * @param $projectId
 * @return bool
 */
function checkIfUserWorksAt(PDO $pdo, $userId, $projectId)
{
    $param_userId = $userId;
    $param_projectId = $projectId;
    $sql = "SELECT pk_fk_userId, pk_fk_projectId FROM worksat WHERE pk_fk_userId = :userId AND pk_fk_projectId = :projectId";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 0) {
                return true;
            } elseif ($stmt->rowCount() == 1) {
                return false;
            } else {
                sendError("checkIfUserWorksAt returns more than 1 row");
            }
        } else {
            sendError("checkIfUserWorksAt didnt work.");
        }
    }
    // Close statement
    unset($stmt);
    return false;
}

/**
 * reads all projects which a user is involved in
 * @param PDO $pdo
 * @return string
 */
function readProjectsForUser(PDO $pdo)
{
    $toProjectNames = "";
    $sql = "SELECT pk_fk_projectId FROM worksat WHERE pk_fk_userId = :userId";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_userId = $_SESSION['userId'];
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            foreach ($stmt as $row) {
                $toProjectNames .= $row['pk_fk_projectId'] .= ";";
            }
            $toProjectNames = substr($toProjectNames, 0, -1);

            return $toProjectNames;
        } else {
            sendError("A loop failed in readProjectsForUser().");

        }
    }
    sendError("You shouldnt be here");
    return $toProjectNames;
}

/**
 * converts a string with ProjectsIds ,";" inbetween names
 * @param PDO $pdo
 * @return string
 */

function projectIdsToNames(PDO $pdo)
{
    //deletes last char since its an ";"
    $projectIds = readProjectsForUser($pdo);
    $idsInArray = explode(';', $projectIds);
    $projectNames = "";

    for ($x = 0; $x < count($idsInArray); $x++) {
        $sql = "SELECT projectname FROM project WHERE pk_projectId = :projectId";
        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $param_projectId = $idsInArray[$x];
            $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
            if ($stmt->execute()) {
                if ($row = $stmt->fetch()) {
                    $projectNames .= $row['projectname'] .= ";";
                }
                //sendSuccess("Erfolgreich von projektid zu namen");
            }
        }
    }
    return substr($projectNames, 0, -1);
}

/**
 * converts a string with ProjectsIds ,";" inbetween names
 * @param PDO $pdo
 * @param $userdata
 * @return int
 */

function projectnameToIds(PDO $pdo, $userdata)
{
    $projectId = "";
    if (!isset($userdata)) {
        $userdata = createProjectInput();
    }
    $sql = "SELECT pk_projectId FROM project WHERE projectname = :projectName";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectName = $userdata['projectName'];
        $stmt->bindParam(':projectName', $param_projectName, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $count = $stmt->rowCount();
            if ($count == 1) {
                if ($row = $stmt->fetch()) {
                    $projectId = $row['pk_projectId'];
                }
                sendSuccess("Erfolgreich von namen zu ids");
                return $projectId;
            } else {
                sendError($count);
            }
        }
    }
    return $projectId;
}

/**
 * returns the leaderId of the projectId provided by the Frontend.
 * Frontend needs these informations to display the project the
 * user clicked on
 * @param PDO $pdo
 * @return string
 */
function getLeaderIdFromProjectId(PDO $pdo)
{
    //deletes last char since its an ";"
    $projectId = getProjectId();
    $leaderId = "";

    $sql = "SELECT fk_leaderId FROM project WHERE pk_projectId = :projectId";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $projectId['projectId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                if ($row = $stmt->fetch()) {
                    $leaderId = $row['fk_leaderId'];
                }
                //sendSuccess("Erfolgreich von projektid zu namen");
                return $leaderId;
            } else {
                sendError("wait what");
                return $leaderId;
            }
        }
    }
    return $leaderId;

}

/**
 * gets all Stress values of one user and one project and returns them
 * @param PDO $pdo
 * @return bool|string
 */
function getStressForUserIdAndProjectId(PDO $pdo)
{
    $stress = "";
    $userdata = getUserIdProjectId();
    $sql = "SELECT sliderValue_stress FROM feedback WHERE (fk_projectId = :projectId AND fk_userId = :userId)";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $userdata['projectId'];
        $param_userId = $userdata['userId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            foreach ($stmt as $row) {
                $stress .= $row['sliderValue_stress'] .= ";";
            }
            $stress = substr($stress, 0, -1);
        }
    }
    return $stress;
}

/**
 * gets all Motivation values of one user and one project and returns them
 * @param PDO $pdo
 * @return bool|string
 */
function getMotivationForUserIdAndProjectId(PDO $pdo)
{
    $motivation = "";
    $userdata = getUserIdProjectId();
    $sql = "SELECT sliderValue_motivation FROM feedback WHERE (fk_projectId = :projectId AND fk_userId = :userId)";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $userdata['projectId'];
        $param_userId = $userdata['userId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            foreach ($stmt as $row) {
                $motivation .= $row['sliderValue_motivation'] .= ";";
            }
            $motivation = substr($motivation, 0, -1);
        }
    }
    return $motivation;
}

/**
 * gets all WorkPerformance values of one user and one project and returns them
 * @param PDO $pdo
 * @return bool|string
 */
function getWorkPerformanceForUserIdAndProjectId(PDO $pdo)
{
    $workperformance = "";
    $userdata = getUserIdProjectId();
    $sql = "SELECT work_performance_satisfied FROM feedback WHERE (fk_projectId = :projectId AND fk_userId = :userId)";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $userdata['projectId'];
        $param_userId = $userdata['userId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            foreach ($stmt as $row) {
                $workperformance .= $row['work_performance_satisfied'] .= ";";
            }
            $workperformance = substr($workperformance, 0, -1);
        }
    }
    return $workperformance;
}

/**
 * gets all technicalSkills values of one user and one project and returns them
 * @param PDO $pdo
 * @return bool|string
 */
function getTechnicalSkillsForUserIdAndProjectId(PDO $pdo)
{
    $technicalSkills = "";
    $userdata = getUserIdProjectId();
    $sql = "SELECT technicalSkills FROM feedback WHERE (fk_projectId = :projectId AND fk_userId = :userId)";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $userdata['projectId'];
        $param_userId = $userdata['userId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            foreach ($stmt as $row) {
                $technicalSkills .= $row['technicalSkills'] .= ";";
            }
            $technicalSkills = substr($technicalSkills, 0, -1);
        }
    }
    return $technicalSkills;
}


/**
 * gets all dates of one feedback user and one project and returns them
 * @param PDO $pdo
 * @return bool|string
 */
function getDatesForUserIdAndProjectId(PDO $pdo)
{
    $dates = "";
    $userdata = getUserIdProjectId();
    $sql = "SELECT date FROM feedback WHERE (fk_projectId = :projectId AND fk_userId = :userId)";
    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $param_projectId = $userdata['projectId'];
        $param_userId = $userdata['userId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
        if ($stmt->execute()) {
            foreach ($stmt as $row) {
                $dates .= $row['date'] .= ";";
            }
            $dates = substr($dates, 0, -1);
        }
    }
    return $dates;
}

/**
 * returns a string with all userIds, seperated by ";"
 * without the leader of the project though
 * @param PDO $pdo
 * @return string with all userIds with ";" in between
 */
function getMembersOfProjectIdWithoutLeader(PDO $pdo)
{
    $userIds = "";
    $userdata = getProjectId();

    /**
     * gets all UserIds without the leaderId
     */
    $sql = "SELECT pk_fk_userId FROM worksat 
            INNER JOIN project ON worksat.pk_fk_projectId = project.pk_projectId
            WHERE pk_projectId = :projectId
            AND pk_fk_userId NOT IN (SELECT fk_leaderId 
                                     FROM project 
                                     WHERE pk_projectId = :projectId1)";
    if ($stmt = $pdo->prepare($sql)) {
        $param_projectId = $userdata['projectId'];
        $param_projectId1 = $userdata['projectId'];
        $stmt->bindParam(':projectId', $param_projectId, PDO::PARAM_STR);
        $stmt->bindParam(':projectId1', $param_projectId1, PDO::PARAM_STR);
        if ($stmt->execute()){
            foreach ($stmt as $row) {
                $userIds .= $row['pk_fk_userId'] .= ";";
            }
            $userIds = substr($userIds, 0, -1);
        }
    }
    return $userIds;
}

/**
 * gets Array with a UserId, transforms it to UserId of Email
 * @param PDO $pdo
 * @param $userdata
 * @return int
 */
function emailToUserId(PDO $pdo, $userdata)
{
    $userId = 0;
    $sql = "SELECT pk_userId FROM users WHERE email = :email";
    if ($stmt = $pdo->prepare($sql)) {
        $param_email = $userdata['email'];
        $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);
        if ($stmt->execute()) {
            //sendSuccess($stmt->rowCount() . " Rows found in emailToUserId");
            if ($row = $stmt->fetch()) {
                $userId = $row['pk_userId'];
            }
        }

    }
    return $userId;
}

/**
 *
 * @param PDO $pdo
 * @return void
 */
function userIdsToFirstnameSurname(PDO $pdo)
{
    $userdata = getMembersOfProjectIdWithoutLeader($pdo);
    $idsInArray = explode(';', $userdata);
    $firstnames = "";
    $surnames = "";
    $userIds = "";

    for ($x = 0; $x < count($idsInArray); $x++) {
        $sql = "SELECT firstname, surname FROM users where pk_userId = :userId";
        if ($stmt = $pdo->prepare($sql)) {
            $param_userId = $idsInArray[$x];
            $userIds .= $idsInArray[$x] .= ";";
            $stmt->bindParam(':userId', $param_userId, PDO::PARAM_STR);
            if ($stmt->execute()) {
                foreach ($stmt as $row) {
                    $firstnames .= $row['firstname'] .= ";";
                    $surnames .= $row['surname'] .= ";";
                }

            }
        }
    }
    $firstnames = substr($firstnames, 0, -1);
    $surnames = substr($surnames, 0, -1);
    $userIds = substr($userIds, 0, -1);
    /**
     * sends Firstnames and Surnames of ProjectId without Leader
     */
    sendFirstnameSurnameUserIds($firstnames, $surnames, $userIds);

}













