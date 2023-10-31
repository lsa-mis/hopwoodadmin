<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin){
$specificContestID = $_GET['ID'];

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=Local_Evaluations-printed_on-" . date('Y-m-d') . ".csv");

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

$queryJudges = <<<JSQL
  SELECT DISTINCT(uniqname)
  FROM tbl_contestjudge
  WHERE contestsID = $specificContestID
JSQL;

if (!$rows = $db->query($queryJudges)){
  die("Database query failed");
}
// loop over the rows, outputting them
$judge_array = [];
while ($row = $rows->fetch_assoc()) {
  array_push($judge_array, $row['uniqname']);
}

//build pvt select statement
$pvt_part = '';
$pvt_count = 1;
do {
  $pvt_part .= ',pvt.judge' . $pvt_count . ' AS Judge_' . $pvt_count . ' ';
  $pvt_count++;
} while ($pvt_count <= count($judge_array));

//build ext select statement
$ext_part = '';
$ext_count = 1;
do {
  $ext_part .= ',SUM(judge' . $ext_count . ') AS judge' . $ext_count . ' ';
  $ext_count++;
} while ($ext_count <= count($judge_array));

//build cn select statement
$cn_part = '';
$cn_count = 0;
do {
  $cn_part .= ',CASE WHEN cn.evaluator = ' . "'" . $judge_array[$cn_count] . "'" . ' THEN cn.rating END AS judge' . ($cn_count + 1) . ' ';
  $cn_count++;
} while ($cn_count < count($judge_array));

$order_part = 'ORDER BY -Judge_1 DESC';
$order_count = 2;
while ($order_count <= count($judge_array)){
  $order_part .= ', -Judge_' . $order_count . ' DESC' . ' ';
  $order_count++;
}

//Build header for output
$header_array = [];
$start_header_array = [];
$start_header_array = ['EntryId', 'Contest_Name', 'Firstname', 'Lastname'];
$header_array = array_merge($start_header_array, $judge_array);
array_push($header_array, 'Email', 'FinAid');

// output the column headings
fputcsv($output, $header_array);

// fetch the data
$queryLocalSpecificEval = <<<SQL
  SELECT pvt.entryID
  ,ed.contestName AS contest_name
  ,ed.firstname AS First_Name
  ,ed.lastname AS Last_Name
  $pvt_part
  ,CONCAT(ed.uniqname, '@umich.edu') AS email_address
  ,CASE WHEN app.finAid = 1 THEN 'Y' ELSE '' END AS FinAid_Status
  FROM (SELECT
  	entryID
      $ext_part
  	FROM (SELECT
  			cn.entry_id AS entryID
        $cn_part
  			FROM (select te.id AS id
  					,te.entry_id AS entry_id
  					,te.evaluator AS evaluator
  					,te.rating AS rating
  					,te.created AS created
  					from tbl_evaluations AS te
  					where (te.evaluator in (
  							select distinct tbl_contestjudge.uniqname
  							from tbl_contestjudge
  							)
  						and (te.id = (
  							select max(max.id)
  							from tbl_evaluations AS max
  							where ((max.entry_id = te.entry_id) and (max.evaluator = te.evaluator))
  							))
  						) AND te.rating > 0
  					) AS cn ) AS ext
  	GROUP BY entryID) AS pvt
  LEFT OUTER JOIN vw_entrydetail_with_classlevel_currated AS ed ON pvt.entryID = ed.EntryId
  LEFT OUTER JOIN tbl_applicant AS app ON ed.uniqname = app.uniqname
  WHERE ed.contestsID IN ($specificContestID)
  $order_part;
SQL;

if (!$rows = $db->query($queryLocalSpecificEval)){
  die("Database query failed");
}

// loop over the rows, outputting them
while ($row = $rows->fetch_assoc()) {
   fputcsv($output, $row);
 }
} else {
  echo "unauthorized";
}