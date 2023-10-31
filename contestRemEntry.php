<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin){
// Setup query
if (!($stmt = $db->prepare("UPDATE tbl_contest
    SET status = 1, edited_by = ?, edited_on = ?
    WHERE id = ?"))){
  db_fatal_error("{Prepare failed", "( " . $db->errno . " )" . $db->error, "EMPTY", $login_name);
  exit($user_err_message);
}

// Setup parameters
if (!$stmt->bind_param('ssi',$login_name,$nowDate,$contestid)){
  db_fatal_error("Bind parameters failed", "( " . $stmt->errno . " )" . $stmt->error, "EMPTY", $login_name);
  exit($user_err_message);
}

//Format, sanitize, variables to be used in the query
$contestid = $db->real_escape_string(htmlspecialchars($_POST['contestid']));
$nowDate = date("Y-m-d H:i:s", (strtotime("now")));

// Perform
  if($stmt->execute()){
    $_SESSION['flashMessage'] = "<span class='text-danger'>Successfully deleted contest instance</span>";
  } else {
    db_fatal_error("Execute failed", "( " . $stmt->errno . " )" . $stmt->error, "EMPTY", $login_name);
    exit($user_err_message);
  }

$db->close();

} else {
  echo "unauthorized";
}