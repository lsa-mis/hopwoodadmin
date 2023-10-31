<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

// This gets all the entries, national evals and judges


$nat_rating_email = <<< _SQLNATRATINGEMAIL
SELECT
cn.entry_id AS entryID
,ed.contestName AS contest_name
,ed.title AS title
,ed.uniqname AS uniqname
,CONCAT(ed.firstname, " ", ed.lastname) AS author_fullname
,MAX(CONCAT("Judge: ",CONCAT(nj.firstname, ' ',nj.lastname), " commented- ",cn.contestantcomment)) AS judge1comments
,MIN(CONCAT("Judge: ",CONCAT(nj.firstname, ' ',nj.lastname), " commented- ",cn.contestantcomment)) AS judge2comments
,MAX(cn.evaluator) AS judge1
,MIN(cn.evaluator) AS judge2
,tc.status AS contest_status
FROM vw_current_national_evaluations AS cn
LEFT OUTER JOIN vw_entrydetail_with_classlevel_currated AS ed ON cn.entry_id = ed.EntryId
LEFT OUTER JOIN tbl_nationalcontestjudge AS nj ON cn.evaluator = nj.uniqname
LEFT OUTER JOIN tbl_contest AS tc ON ed.ContestInstance = tc.id
WHERE created > (SELECT MAX(contclose.date_closed) FROM tbl_contest AS contclose WHERE contclose.contestsID = 1) AND tc.status = 0  AND tc.contestsID IN (2,9,11,19,20,21,22,23,24,25,26)

GROUP BY entry_id
ORDER BY uniqname

_SQLNATRATINGEMAIL;

  $resNatRatingEmail = $db->query($nat_rating_email);
  $resultNatRatingEmail = array();

  if ($db->error) {
      try {
          throw new Exception("MySQL error $db->error <br> Query:<br> $nat_rating_email", $db->errno);
      } catch(Exception $e ) {
          echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
          echo nl2br($e->getTraceAsString());
      }
  }
  while($item = $resNatRatingEmail->fetch_assoc()){
    array_push($resultNatRatingEmail,
      array(
        'entryID' =>$item["entryID"]
        ,'contest_name' =>$item["contest_name"]
        ,'title' =>$item["title"]
        ,'uniqname' =>$item["uniqname"]
        ,'author_fullname' =>$item["author_fullname"]
        ,'judge1' =>$item["judge1"]
        ,'judge2' =>$item["judge2"]
        ,'judge1comments' =>$item["judge1comments"]
        ,'judge2comments' =>$item["judge2comments"]

      )
    );
  }

 if ($isAdmin) {
  $_SESSION['emailsentcount'] = 0;
  $emailSentCounter = 0;
    foreach($resultNatRatingEmail as $item){
      $mailSection = ""; //Clear mail body each time

      $to = $item["uniqname"] . "@umich.edu";
      // $to = "rsmoke" . "@umich.edu";
      $from = "hopwoodcontestnotify@umich.edu";
      $subject = "Hopwood Writing Contest - Judges Comments";

      $mailSection .= "Hello " . $item["author_fullname"] . ",";
      $mailSection .= "\n";
      $mailSection .= "Here are the comments you received for your " . $item["contest_name"] ." entry titled: " . $item["title"];
      $mailSection .= "\n\n";
      $mailSection .= strlen($item["judge1"]) > 1 ? $item["judge1comments"] : "";
      $mailSection .= "\n";
      $mailSection .= $item["judge2"] <> $item["judge1"] ? $item["judge2comments"] : "";
      $mailSection .= "\n";
      $mailSection .= "-- Please do not reply to this email --\n";
      $mailSection .= "If you have any questions or comments about your entry, please contact the Hopwood Writing Contests at hopwoodcontestnotify@umich.edu";
      $mailSection .= "\nThank you";
      $mailSection = wordwrap($mailSection,70, "\n");

      $headers = "From:" . $from;
      mail($to,$subject,$mailSection,$headers);
      $emailSentCounter++;
     };
     $_SESSION['emailsentcount'] = $emailSentCounter;

     $updateContest = <<< _updateContestStatus
     UPDATE tbl_contest
     SET
     status = 4
     WHERE contestsID IN (2,9,11,19,20,21,22,23,24,25,26) AND status = 0
     LIMIT 20;
_updateContestStatus;

    if (!$updateContest = $db->query($updateContest)) {
    db_fatal_error("data query issue", $db->error, $updateContest, $login_name);
    exit;
    }

    echo '<div><h3 class="text-center">You have just sent ' . $_SESSION['emailsentcount'] . ' emails!</h3><br />';
    echo '<h4 class="text-center"><a href="index.php">Return to the home page</h4></div>';
    $db->close();
      } else {
        safeRedirect("index.php");
      }
