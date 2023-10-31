<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');
if ($isAdmin) {
  $queryFinAid = <<<SQL
  SELECT DISTINCT tbl_applicant.id, uniqname, umid, finAidDesc, finAidNotice, userFname, userLname
  FROM
    tbl_entry
        JOIN
    tbl_applicant ON (tbl_entry.applicantID = tbl_applicant.id)
  WHERE
    finAid = 1 AND status = 0
    ORDER BY userLname
SQL;

  $resSelect = $db->query($queryFinAid);
  if (!$resSelect) {
    echo "There is no information available";
  } else {
    $result = array();
    while($item = $resSelect->fetch_assoc()){
    array_push($result, array(
        'applicant_id' => $item["id"],
        'uniqname' =>$item["uniqname"],
        'umid' =>$item["umid"],
        'fname' =>$item["userFname"],
        'lname' =>$item["userLname"],
        'desc' =>$item["finAidDesc"],
        'finaidnotice' =>$item["finAidNotice"]
        )

      );
    }
  }
  echo (json_encode(array("result" => $result)));

  $resSelect->free();

} else {
  echo "unauthorized";
}
