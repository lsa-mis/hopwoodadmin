<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

$selectContests = $_GET['id'];

$where = ($selectContests != 10)? "rating > 0 AND " : "";

  $queryRating = <<<SQL
    SELECT
    vw.EntryId
    ,eval.entry_id AS entry_id
    ,vw.title AS title
    ,vw.firstname AS firstname
    ,vw.lastname AS lastname
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
    WHERE $where created > '2017-12-06' AND contestsID = $selectContests
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
          'nationalstatus' => $item["nationalstatus"]
          )

        );
      }
    }

  echo (json_encode(array("result" => $result)));

  $resSelect->free();
