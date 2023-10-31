<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');
if ($isAdmin){
$selectContests = $_GET['id'];

// $where = ($selectContests != 10)? "rating > 0 AND " : "";
// Get listing oif judges for a the selected contest
$queryJudges = <<<JSQL
  SELECT DISTINCT(uniqname)
  FROM tbl_contestjudge
  WHERE contestsID = $selectContests
JSQL;

if (!$rows = $db->query($queryJudges)){
  die("Database query failed");
}
// loop over the rows, outputting them
$judge_array = [];
while ($row = $rows->fetch_assoc()) {
  array_push($judge_array, $row['uniqname']);
}

//Check that there are judges assigned to the cont
if ( count($judge_array) > 0 ){
  // For all contests other than Cowden (10)
  if ( $selectContests <> 10 ){
  //build pvt select statement
  $pvt_part = '';
  $pvt_count = 1;
  do {
    $pvt_part .= ',pvt.judge' . $pvt_count . ' AS judge_' . $pvt_count . ' ';
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

  // fetch the data
  $queryLocalSpecificEval = <<<SQL
    SELECT
    ed.EntryId
    ,pvt.entryID AS entry_id
    ,ed.title AS title
    ,ed.firstname AS firstname
    ,ed.lastname AS lastname
    ,CONCAT(ed.uniqname, '@umich.edu') AS email
    ,ed.umid AS umid
    ,ed.penName AS penName
    ,ed.contestName AS contestName
    ,ed.document AS document
    ,ed.manuscriptType AS manuscriptType
    ,CASE WHEN ed.classLevel > 12 THEN 'G' ELSE 'U' END AS classLevel
    ,ed.fwdToNational AS nationalstatus
    $pvt_part
    ,CONCAT(ed.uniqname, '@umich.edu') AS email
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
              ,te.contestantcomment AS contestantcommen
              ,te.committeecomment AS committeecomment
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
    						) AND te.rating > 0 AND te.created > '$contest_closed_date'
    					) AS cn ) AS ext
    	GROUP BY entryID) AS pvt
    LEFT OUTER JOIN vw_entrydetail_with_classlevel_currated AS ed ON pvt.entryID = ed.EntryId
    LEFT OUTER JOIN tbl_applicant AS app ON ed.uniqname = app.uniqname
    WHERE ed.contestsID IN ($selectContests)
    $order_part;
SQL;

  $resSelect = $db->query($queryLocalSpecificEval);
  if (!$resSelect) {
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
            'penName' =>$item["penName"],
            'contestName' => $item["contestName"],
            'judge_1' => $item["judge_1"],
            'document' => $item["document"],
            'manuscriptType' => $item["manuscriptType"],
            'classLevel' => $item["classLevel"],
            'nationalstatus' => $item["nationalstatus"],
            'judge1_name' => $judge_array[0],
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
            'penName' =>$item["penName"],
            'contestName' => $item["contestName"],
            'judge_1' => $item["judge_1"],
            'judge_2' => $item["judge_2"],
            'document' => $item["document"],
            'manuscriptType' => $item["manuscriptType"],
            'classLevel' => $item["classLevel"],
            'nationalstatus' => $item["nationalstatus"],
            'judge1_name' => $judge_array[0],
            'judge2_name' => $judge_array[1]
            )
          );
        }
      }
    }
  } else {
  // For Cowden (10) results
  $queryRating = <<<SQL
    SELECT
    vw.EntryId
    ,eval.entry_id AS entry_id
    ,vw.title AS title
    ,vw.firstname AS firstname
    ,vw.lastname AS lastname
    ,CONCAT(vw.uniqname, '@umich.edu') AS email
    ,vw.umid AS umid
    ,vw.penName AS penName
    ,vw.contestName As contestName
    ,eval.rating AS rating
    ,eval.evaluator AS evaluator
    ,vw.document AS document
    ,eval.contestantcomment AS contestantcomment
    ,eval.committeecomment AS committeecomment
    ,vw.manuscriptType AS manuscriptType
    ,CASE WHEN vw.classLevel > 12 THEN 'G' ELSE 'U' END AS classLevel
    ,vw.fwdToNational AS nationalstatus

    FROM `vw_entrydetail_with_classlevel_currated` AS vw
    JOIN vw_current_evaluations AS eval ON(vw.EntryID = eval.entry_id)
    WHERE created > '$contest_closed_date' AND contestsID = $selectContests
    ORDER BY evaluator, rating
SQL;


  $resSelect = $db->query($queryRating);
  if (!$resSelect) {
    echo "There is no information available";
  } else {
    $result = array();

      while($item = $resSelect->fetch_assoc()){
      array_push($result, array(
          'entryid' =>$item["entry_id"],
          'title' =>$item["title"],
          'firstname' =>$item["firstname"],
          'lastname' =>$item["lastname"],
          'email' => $item['email'],
          'umid' => $item["umid"],
          'penName' =>$item["penName"],
          'contestName' => $item["contestName"],
          'rank' => $item["rating"],
          'rankedby' => $item["evaluator"],
          'document' => $item["document"],
          'contestantcomment' => $item["contestantcomment"],
          'committeecomment' => $item["committeecomment"],
          'manuscriptType' => $item["manuscriptType"],
          'classLevel' => $item["classLevel"],
          'nationalstatus' => $item["nationalstatus"],
          'judge1_name' => $judge_array[0]
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
