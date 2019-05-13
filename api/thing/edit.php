<?php
// include database and object files
session_start();
require '../config/vendor/autoload.php';
include_once '../config/HTTP_ORIGIN.php';
include_once '../config/core.php';
include_once '../objects/thing.php';
include_once '../objects/user.php';
include_once '../objects/accessControl.php';

//Firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
$serviceAccount = ServiceAccount::fromJsonFile(firebase_secret);
$firebase = (new Factory)->withServiceAccount($serviceAccount)->create();
$db = $firebase->getDatabase();
 
// initialize object
$thing = new Thing($db);
$user = new User($db);
$core = new Core;

// get posted data
$data = json_decode(file_get_contents("php://input"));

// check if the user is logged in
if (!$user -> authenticate())
{
	$core -> output(401,"User is not logged in");
}

// validate data input
if (!isset($data -> name) || empty($data -> name) || !isset($data -> id)) {
	$core -> output(400,"Please fill in all fields");
}

// validate name
if (!$thing -> validateName($data -> name)) {
	$core -> output(400,"Please use a name between 3 and 20 characters");
}

// check for sufficiant permissions
$accessControl = new AccessControl($db, $data -> id);
if ($accessControl -> checkPermissions($user -> id) != 2) {
	$core -> output(403,"Insufficient permissions");
}

// update data
if ($thing -> update(["id" => $data -> id, "name" => $data -> name])) {
	$core -> output(200,"Thing edited successfully!");
}
else {
	$core -> output(500,"An unknown error has occurred");
}