<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin){
  $resOpenContests = $db->query("SELECT * FROM vw_contestlistingfuturedated ORDER BY date_open");
  if (!$resOpenContests) {
    echo "There are no future contests set to open.";
  } else {
    while ($instance = $resOpenContests->fetch_assoc()) {
      echo '<div class="record"><strong><span class="glyphicon glyphicon-asterisk"></span>' . $instance['ContestsName'] . '</strong> OPENS: ' . date("F jS, Y - g:i A", (strtotime($instance['date_open']))) . ' - CLOSES: ' . date("F jS, Y - g:i A", (strtotime($instance['date_closed']))) . '    <button class="btn btn-danger btn-xs contestdeletebtn" data-contestid="' . $instance["contestid"] . '"><span class="glyphicon glyphicon-remove-sign"></span></button>';
      if(strlen($instance['notes']) > 0){
        echo '<br><blockquote><em>NOTES: ' . $instance['notes'] . '</em></blockquote>';
      }
      echo '</div>';
    }
  }

  $db->close();
} else {
  echo "unauthorized";
}