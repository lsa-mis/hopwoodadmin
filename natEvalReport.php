<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin) {
  $queryNatEval = <<<SQL
-- SELECT `evaluator`,rating AS evaluation,comment,`entry_id`,created,
-- vc.title, vc.`document`,vc.`uniqname` AS applicantUniq,vc.`firstname`,vc.`lastname`,
-- vc.`penName`,(CASE WHEN vc.`classLevel` >= 20 THEN "G" ELSE "U" END) AS gradeLevel,
-- vc.`manuscriptType`,vc.`ContestInstance`
-- FROM vw_entrydetail_with_classlevel_currated AS vc

-- LEFT OUTER JOIN tbl_evaluations AS jev ON (vc.`EntryId` = jev.`entry_id`)

-- WHERE evaluator IN (SELECT DISTINCT uniqname FROM tbl_nationalcontestjudge) AND fwdToNational = 1

-- ORDER BY manuscriptType, evaluator, gradeLevel DESC, entry_id, created DESC
SELECT `evaluator`,rating AS evaluation,comment,`entry_id`,created,
vc.title, vc.`document`,vc.`uniqname` AS applicantUniq,vc.`firstname`,vc.`lastname`,
vc.`penName`,(CASE WHEN vc.`classLevel` >= 20 THEN "G" ELSE "U" END) AS gradeLevel,
vc.`manuscriptType`,vc.`ContestInstance`
FROM vw_entrydetail_with_classlevel_currated AS vc

LEFT OUTER JOIN tbl_evaluations AS jev ON (vc.`EntryId` = jev.`entry_id`)

WHERE evaluator IN (SELECT DISTINCT uniqname FROM tbl_nationalcontestjudge) AND fwdToNational = 1 AND jev.id =
(SELECT MAX(jev1.id)
FROM tbl_evaluations AS jev1
WHERE jev1.entry_id = jev.entry_id AND jev1.evaluator = jev.evaluator)

ORDER BY manuscriptType, evaluator, gradeLevel DESC, evaluation DESC
SQL;

  $resSelect = $db->query($queryNatEval);
  if (!$resSelect) {
    echo "There is no information available";
  } else {
    $result = array();

      while($item = $resSelect->fetch_assoc()){
      array_push($result, array(
          'evaluator' =>$item["evaluator"],
          'evaluation' =>$item["evaluation"],
          'comment' => $item["comment"],
          'entryid' => $item["entry_id"],
          'created' => $item["created"],
          'title' => $item["title"],
          'document' => $item["document"],
          'applicantUniq' => $item["applicantUniq"],
          'firstname' =>$item["firstname"],
          'lastname' =>$item["lastname"],
          'penName' =>$item["penName"],
          'gradeLevel' => $item["gradeLevel"],
          'manuscriptType' => $item["manuscriptType"],
          'ContestInstance' => $item["ContestInstance"]
          )
        );
      }
    }

  echo (json_encode(array("result" => $result)));

  $resSelect->free();

} else {
  echo "unauthorized";
}