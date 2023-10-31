<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin){
$selectContests = $_GET['id'];

// Get listing oif judges for a the selected contest
$queryJudges = <<<JSQL
  SELECT DISTINCT(uniqname)
  FROM tbl_nationalcontestjudge
  WHERE contestsID = $selectContests
JSQL;

if (!$rows = $db->query($queryJudges)){
  db_fatal_error("data SELECT issue", $db->error, $queryJudges, $login_name);
  exit;
}
// loop over the rows, outputting them
$judge_array = [];
while ($row = $rows->fetch_assoc()) {
  array_push($judge_array, $row['uniqname']);
}
//print_r2($judge_array);
//Check that there are judges assigned to the cont
if ( count($judge_array) > 0 ){
  //build pvt select statement
  $pvt_part = '';
  $pvt_count = 1;
  do {
  //  $pvt_part .= ',pvt.judge' . $pvt_count . ' AS judge_' . $pvt_count . ' ';
    $pvt_part .= ",pvt.judge" . $pvt_count . " AS '" . $judge_array[$pvt_count - 1]  . "' ";
    $pvt_part .= ",pvt.judge" . $pvt_count . "_contestantcomment AS '" . $judge_array[$pvt_count - 1]  . "_contestant-comments' ";
    $pvt_part .= ",pvt.judge" . $pvt_count . "_committeecomment AS '" . $judge_array[$pvt_count - 1]  . "_committee-comments' ";
    $pvt_count++;
  } while ($pvt_count <= count($judge_array));
//print_r2($pvt_part);
  //build ext select statement
  $ext_part = '';
  $ext_count = 1;
  do {
    $ext_part .= ',SUM(judge' . $ext_count . ') AS judge' . $ext_count . ' ';
    $ext_part .= ',MAX(judge' . $ext_count . '_committeecomment) AS judge' . $ext_count . '_committeecomment ';
    $ext_part .= ',MAX(judge' . $ext_count . '_contestantcomment) AS judge' . $ext_count . '_contestantcomment ';
    $ext_count++;
  } while ($ext_count <= count($judge_array));
//print_r2($ext_part);
  //build cn select statement
  $cn_part = '';
  $cn_count = 0;
  do {
    $cn_part .= ',CASE WHEN cn.evaluator = ' . "'" . $judge_array[$cn_count] . "'" . ' THEN cn.rating END AS judge' . ($cn_count + 1) . ' ';
    $cn_part .= ',CASE WHEN cn.evaluator = ' . "'" . $judge_array[$cn_count] . "'" . ' THEN cn.committeecomment END AS judge' . ($cn_count + 1) . '_committeecomment ';
    $cn_part .= ',CASE WHEN cn.evaluator = ' . "'" . $judge_array[$cn_count] . "'" . ' THEN cn.contestantcomment END AS judge' . ($cn_count + 1) . '_contestantcomment ';
    $cn_count++;
  } while ($cn_count < count($judge_array));
//print_r2($cn_part);
  $order_part = 'ORDER BY -pvt.judge1 DESC';
  $order_count = 2;
  while ($order_count <= count($judge_array)){
    $order_part .= ', -pvt.judge' . $order_count . ' DESC' . ' ';
    $order_count++;
  }
//print_r2($order_part);
  // fetch the data
  $queryNationalSpecificEval = <<<SQL
  SELECT
    ed.EntryId
    ,pvt.entryID AS entry_id
    ,ed.title AS title
    ,ed.firstname AS firstname
    ,ed.lastname AS lastname
    ,CONCAT(ed.uniqname, '@umich.edu') AS email
    ,ed.umid AS umid
    ,CASE WHEN app.finAid = 1 THEN 'Y' ELSE '' END AS FinAid_Status
    ,ed.penName AS penName
    ,ed.contestName AS contestName
    ,ed.document AS document
    ,ed.manuscriptType AS manuscriptType
    ,CASE WHEN ed.classLevel > 12 THEN 'G' ELSE 'U' END AS classLevel
    ,ed.fwdToNational AS nationalstatus
    $pvt_part
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
                        ,te.committeecomment AS committeecomment
                        ,te.contestantcomment AS contestantcomment
    					from tbl_evaluations AS te
    					where (te.evaluator in (
    							select distinct tbl_nationalcontestjudge.uniqname
    							from tbl_nationalcontestjudge
    							)
    						and (te.id = (
    							select max(max.id)
    							from tbl_evaluations AS max
    							where ((max.entry_id = te.entry_id) and (max.evaluator = te.evaluator))
    							))
    						) AND te.rating > 0 AND te.created > (SELECT MAX(contclose.date_closed) FROM tbl_contest AS contclose WHERE contclose.contestsID = 1)
    					) AS cn ) AS ext
    	GROUP BY entryID) AS pvt
    LEFT OUTER JOIN vw_entrydetail_with_classlevel_currated AS ed ON pvt.entryID = ed.EntryId
    LEFT OUTER JOIN tbl_applicant AS app ON ed.uniqname = app.uniqname
    WHERE ed.contestsID = $selectContests
    $order_part ;
SQL;
//print_r2($queryNationalSpecificEval);

  if (!$resSelect = $db->query($queryNationalSpecificEval)){
    db_fatal_error("data SELECT issue", $db->error, $queryNationalSpecificEval, $login_name);
    echo "There is no information available";
  } else {
    $result = array();

      if (count($judge_array) == 1) {
        while($item = $resSelect->fetch_assoc()){
        array_push($result, array(
          'entryid' =>$item["entry_id"],
          'title' =>$item["title"],
          'firstname' =>$item["firstname"],
          'lastname' =>$item["lastname"],
          'email' => $item['email'],
          'umid' => $item["umid"],
          'fin_aid' => $item["FinAid_Status"],
          'penName' =>$item["penName"],
          'contestName' => $item["contestName"],
          'document' => $item["document"],
          'manuscriptType' => $item["manuscriptType"],
          'classLevel' => $item["classLevel"],
          'judge_1' => $item["$judge_array[0]"],
          'judge1_name' => $judge_array[0],
          'judge1_contestantcomment' => $item["$judge_array[0]_contestant-comments"],
          'judge1_committeecomment' => $item["$judge_array[0]_committee-comments"]
            )
          );
        }
      } else {
        while($item = $resSelect->fetch_assoc()){
        array_push($result, array(
          'entryid' =>$item["entry_id"],
          'title' =>$item["title"],
          'firstname' =>$item["firstname"],
          'lastname' =>$item["lastname"],
          'email' => $item['email'],
          'umid' => $item["umid"],
          'fin_aid' => $item["FinAid_Status"],
          'penName' =>$item["penName"],
          'contestName' => $item["contestName"],
          'document' => $item["document"],
          'manuscriptType' => $item["manuscriptType"],
          'classLevel' => $item["classLevel"],
          'judge1_name' => $judge_array[0],
          'judge_1' => $item["$judge_array[0]"],
          'judge1_contestantcomment' => $item["$judge_array[0]_contestant-comments"],
          'judge1_committeecomment' => $item["$judge_array[0]_committee-comments"],
          'judge_2' => $item["$judge_array[1]"],
          'judge2_name' => $judge_array[1],
          'judge2_contestantcomment' => $item["$judge_array[1]_contestant-comments"],
          'judge2_committeecomment' => $item["$judge_array[1]_committee-comments"]
            )
          );
        }
      }
    }
    echo (json_encode(array("result" => $result)));

    $resSelect->free();
}
} else {
  echo "unauthorized";
}