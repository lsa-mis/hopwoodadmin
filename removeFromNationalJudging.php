<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin){
$entryToUpdate = $_POST['entry_id'];

  $updateEntry = "UPDATE tbl_entry SET sendtoNationalJudging = 0 WHERE id = $entryToUpdate";

    $resUpdate = $db->query($updateEntry);

    if ($db->error) {
        try {
            throw new Exception("MySQL error $db->error <br> Query:<br> $updateEntry", $db->errno);
        } catch(Exception $e ) {
            echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
            echo nl2br($e->getTraceAsString());
        }
    } else {

    echo ("Entry " . $entryToUpdate . " was removed from the National Judging stage!");
    }

    $db->close();
} else {
    echo "unauthorized";
}