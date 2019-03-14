<?php
require "config.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
/**
 * Sends all common data that the client-side may needs for visualisation
 */
function SendUserData(){
    echo json_encode(array(
        'email' => $_SESSION['email'],
        'surname' => $_SESSION['surname'],
        'firstname' => $_SESSION['firstname'],
        'userId' => $_SESSION['userId'],
        'lastLogin' => $_SESSION['date']
    ));
}

/**
 * Gives back JSON, has an "infotext"
 * @param $errorText
 */
function sendError($errorText){
    echo json_encode(array(
        'status' => '50x',
        'infotext' => $errorText
    ));
    die();
}

/**
 * Gives back regular JSON if a success occurs, has an "infotext"
 * @param $successText
 */
function sendSuccess($successText){
    echo json_encode(array(
        'status' => '201',
        'infotext' => $successText
    ));
}

/**
 * send all projects for one user
 * @param $pdo
 */
function sendProjects($pdo){
        echo json_encode(array(
            'projectIds' => readProjectsForUser($pdo),
            'projectNames' => projectIdsToNames($pdo)
        ));
}

/**
 * sends all information for one project to display
 * @param $pdo
 */
function sendProjectInformation($pdo){
    echo json_encode((array(
        'projectname' => projectIdsToNames($pdo),
        'fk_leaderId' =>  getLeaderIdFromProjectId($pdo)
     )));
}

/**
 * sends all information for one project to display for Leader(including Feedbacks)
 * @param $pdo
 */
function sendProjectInformationLeader($pdo){
    echo json_encode((array(
        'projectname' => projectIdsToNames($pdo),
        'fk_leaderId' =>  getLeaderIdFromProjectId($pdo)
    )));
}

/**
 * sends all information for one project to display for Leader(including Feedbacks)
 * @param $pdo
 */
function sendFeedbacksForId($pdo){
    echo json_encode((array(
        'stress' => getStressForUserIdAndProjectId($pdo),
        'motivation' =>  getMotivationForUserIdAndProjectId($pdo),
        'work_performance_satisfied' =>  getWorkPerformanceForUserIdAndProjectId($pdo),
        'technicalSkills' =>  getTechnicalSkillsForUserIdAndProjectId($pdo),
        'dates' => getDatesForUserIdAndProjectId($pdo)
    )));
}

/**
 * returns an array of userIds which are working at this project (without its leader)
 * @param $pdo
 */
function sendAllMembersOfProjectWithoutLeader($pdo){
    echo json_encode((array(
        'members' => getMembersofProjectIdWithoutLeader($pdo)
    )));
}

/**
 * returns to frontend if user is logged in or not
 * @param $loginBoolean
 */
function isLoggedIn($loginBoolean){
    echo json_encode((array(
        'isLoggedIn' => $loginBoolean
    )));
}

/**
 * @param $firstnames
 * @param $surnames
 * @param $userIds
 */

function sendFirstnameSurnameUserIds($firstnames, $surnames, $userIds){
    echo json_encode((array(
        'firstnames' => $firstnames,
        'surnames' =>  $surnames,
        'userIds' =>  $userIds
    )));
}