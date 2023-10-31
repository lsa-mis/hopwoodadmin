<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');
if ($isAdmin){
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=Local_Evaluations-printed_on-" . date('Y-m-d') . ".csv");

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('EntryId', 'Title', 'Contest_Name', 'Rating', 'Firstname', 'Lastname', 'umid', 'ClassLevel', 'Pen_Name', 'Evaluator', 'Document', 'Contestant_comment', 'Committee_comment', 'Manuscript_Type', 'National_status'));

// fetch the data
$queryLocalEval = <<<SQL
    SELECT
    vw.EntryId
    ,vw.title AS title
    ,vw.contestName As contestName
    ,CASE WHEN eval.rating >= 1 THEN eval.rating ELSE '' END AS rating
    ,vw.firstname AS firstname
    ,vw.lastname AS lastname
    ,vw.umid AS umid
    ,CASE WHEN vw.classLevel > 12 THEN 'G' ELSE 'U' END AS classLevel
    ,vw.penName AS penName
    ,eval.evaluator AS evaluator
    ,vw.document AS document
    ,eval.contestantcomment AS contestantcomment
    ,eval.committeecomment AS committeecomment
    ,vw.manuscriptType AS manuscriptType
    ,CASE WHEN vw.fwdToNational = 1 THEN 'Sent to Nationals' ELSE '' END AS nationalstatus

    FROM `vw_entrydetail_with_classlevel_currated` AS vw
    JOIN vw_current_evaluations AS eval ON(vw.EntryID = eval.entry_id)
    WHERE created > '$contest_closed_date' AND (contestsID != 10 AND rating > 0) OR (contestsID = 10 AND committeecomment IS NOT NULL)
    ORDER BY contestName, evaluator, -rating DESC
SQL;

if (!$rows = $db->query($queryLocalEval)){
  die("Database query failed");
}

// loop over the rows, outputting them
while ($row = $rows->fetch_assoc()) {
  if (($row['contestName'] == "The Roy W. Cowden Memorial Fellowship") || ($row['rating'] > 0)) {
   fputcsv($output, $row);
  }
 }
} else {
  echo "unauthorized";
}