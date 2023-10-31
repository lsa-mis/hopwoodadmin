<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin) {
// This is gets all the entries and national evals
$nat_entries_evaluation_details = <<< _SQLNATENTRIESDETAILS
SELECT 
id
,entry_id
,contestName
,ContestInstance
,title
,contestantcomment
,MAX(cn.evaluator) AS njudge1 
,CASE WHEN MAX(cn.evaluator) <> MIN(cn.evaluator) THEN MIN(cn.evaluator) ELSE NULL END AS njudge2
,GROUP_CONCAT(rating) AS ratings
,SUM(rating) AS ratingsTTL
,penName

FROM vw_current_national_evaluations AS cn
LEFT OUTER JOIN vw_entrydetail_with_classlevel AS ed ON cn.entry_id = ed.EntryId
GROUP BY entry_id
ORDER BY contestName, rating

_SQLNATENTRIESDETAILS;

$nat_Contests_count = <<< _SQLNATCONTESTCOUNT
SELECT 
CASE WHEN MAX(natjudge.uniqname) <> MIN(natjudge.uniqname) THEN CAST(COUNT(EntryID)/4 AS DECIMAL(3,0)) ELSE COUNT(EntryID) END AS count_of_entries, 
ed.contestName, 
ed.ContestInstance, ed.contestsID,
MIN(CONCAT(natjudge.uniqname,':',natjudge.firstname,' ',natjudge.lastname)) AS Nat_judge1,
CASE WHEN MAX(natjudge.uniqname) <> MIN(natjudge.uniqname) THEN MAX(CONCAT(natjudge.uniqname,':',natjudge.firstname,' ',natjudge.lastname))  ELSE NULL END AS Nat_judge2,
MIN(CONCAT(locjudge.uniqname,':',locjudge.firstname,' ',locjudge.lastname)) AS Loc_judge1,
CASE WHEN MAX(locjudge.uniqname) <> MIN(locjudge.uniqname) THEN MAX(CONCAT(locjudge.uniqname,':',locjudge.firstname,' ',locjudge.lastname))  ELSE NULL END AS Loc_judge2
FROM vw_entrydetail_with_classlevel_currated AS ed
LEFT OUTER JOIN tbl_nationalcontestjudge AS natjudge ON ed.contestsID = natjudge.contestsID
LEFT OUTER JOIN tbl_contestjudge AS locjudge ON ed.contestsID = locjudge.contestsID

WHERE status = 0 AND contestName IN ( SELECT DISTINCT contestName FROM vw_entrydetail_with_classlevel_currated WHERE fwdToNational = 1)

GROUP BY contestName

_SQLNATCONTESTCOUNT;

$nat_rating_ttl = <<< _SQLNATRATINGTTL
SELECT 
entry_id
,SUM(rating) AS ratingTTL

FROM quilleng_ContestManager.vw_current_national_evaluations AS cn
LEFT OUTER JOIN vw_entrydetail_with_classlevel AS ed ON cn.entry_id = ed.EntryId
-- WHERE ContestInstance = 35
GROUP BY entry_id

_SQLNATRATINGTTL;

  $resNatEntryEvalDetail = $db->query($nat_entries_evaluation_details);
  $resultNatEntryEvalDetail = array();
  
  if ($db->error) {
      try {    
          throw new Exception("MySQL error $db->error <br> Query:<br> $nat_entries_evaluation_details", $db->errno);    
      } catch(Exception $e ) {
          echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
          echo nl2br($e->getTraceAsString());
      }
  }
  while($item= $resNatEntryEvalDetail->fetch_assoc()){
    array_push($resultNatEntryEvalDetail, 
      array(
        'entry_id' =>$item["entry_id"]
        ,'contestName' =>$item["contestName"]
        ,'ContestInstance' =>$item["ContestInstance"]
        ,'title' =>$item["title"]
        ,'ratings' =>$item["ratings"]
        ,'ratingsTTL' => $item["ratingsTTL"]
        ,'contestantcomment' =>$item["contestantcomment"]
        ,'njudge1' =>$item["njudge1"]
        ,'njudge2' =>$item["njudge2"]
        ,'penName' =>$item["penName"]
      )
    );
  }

//   print_r2 ($resultNatEntryEvalDetail);

// echo "=======================================================<br />";
// echo "=======================================================";


  $resNatContestscount = $db->query($nat_Contests_count);
  $resultNatContestscount = array();
  
  if ($db->error) {
      try {    
          throw new Exception("MySQL error $db->error <br> Query:<br> $nat_Contests_count", $db->errno);    
      } catch(Exception $e ) {
          echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
          echo nl2br($e->getTraceAsString());
      }
  }
  while($item= $resNatContestscount->fetch_assoc()){
    array_push($resultNatContestscount, 
      array(
        'count_of_entries' =>$item["count_of_entries"]
        ,'contestName' =>$item["contestName"]
        ,'ContestInstance' =>$item["ContestInstance"]
        ,'contestsID' =>$item["contestsID"]
        ,'Nat_judge1' =>explode(':',$item["Nat_judge1"])[1]
        ,'Nat_judge2' =>$item["Nat_judge2"]? explode(':',$item["Nat_judge2"])[1] : ''
        ,'Loc_judge1' =>explode(':',$item["Loc_judge1"])[1]
        ,'Loc_judge2' =>$item["Loc_judge2"]? explode(':',$item["Loc_judge2"])[1] : ''
      )
    );
  }

// Gets the current list of contests, count of entries, national and local judge names
 //print_r2 ($resultNatContestscount);

  // echo "=======================================================<br />";
  // echo "======================================================="; 


  $resNatRatingTtl = $db->query($nat_rating_ttl);
  $resultNatRatingTtl = array();
  
  if ($db->error) {
      try {    
          throw new Exception("MySQL error $db->error <br> Query:<br> $nat_rating_ttl", $db->errno);    
      } catch(Exception $e ) {
          echo "Error No: ".$e->getCode(). " - ". $e->getMessage() . "<br >";
          echo nl2br($e->getTraceAsString());
      }
  }
  while($item= $resNatRatingTtl->fetch_assoc()){
    array_push($resultNatRatingTtl, 
      array(
        'entry_id' =>$item["entry_id"]
        ,'ratingTTL' =>$item["ratingTTL"]
      )
    );
  }

  // print_r2 ($resultNatRatingTtl);

  // echo "=======================================================<br />";
  // echo "=======================================================<br />";   
  // echo "=======================================================<br />";
  // echo "=======================================================";
}
  ?>
  <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>LSA-<?php echo "$contestTitle";?> Writing Contests</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="LSA-English Writing Contests">
    <meta name="keywords" content="LSA-English, Hopwood, Writing, UniversityofMichigan">
    <meta name="author" content="LSA-MIS_rsmoke">
    <link rel="icon" href="img/favicon.ico">
    <script type='text/javascript' src='js/webforms2.js'></script>
    <link rel="stylesheet" href="css/bootstrap.min.css"><!-- 3.3.1 -->
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/bootstrap-formhelpers.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="css/normalize.css" media="all">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/default.css" media="all">
    <style type="text/css">
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
    input[type=number] {
    -moz-appearance:textfield;
    }
    </style>
    <base href=<?php echo URL ?>>
  </head>
  <body>
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button> <a class="navbar-brand" href="index.php"><?php echo "$contestTitle";?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Signed in as <?php echo $login_name;?><strong class="caret"></strong></a>
              <ul class="dropdown-menu">
                <li>
                  <a href="index.php"><?php echo "$contestTitle";?> main</a>
                </li>
                <li>
                  <a href="../logout_all.php">logout</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <?php if ($isAdmin) {
    ?>
    <div class="container"><!-- container of all things -->
    <?php
    
    echo "<header><h1>Hopwood Writing Contest Final Judging Report</h1></header>";
    $summarySection = "";
    foreach($resultNatContestscount as $contest){
      $summarySection .= "<div class='contest'>";
      $summarySection .= "<hr>";
      $summarySection .= "<h2>" . $contest["contestName"] . "</h1>";
      $summarySection .= "<h4>Total number of submissions: " . $contest["count_of_entries"] . "</h4>";
      $summarySection .= "<h4>National judges: ";
      $summarySection .= strlen($contest["Nat_judge1"]) > 1 ? $contest["Nat_judge1"] : " -- ";
      $summarySection .= strlen($contest["Nat_judge2"]) > 1 ? " and " . $contest["Nat_judge2"] : "";
      $summarySection .= "</h4>"; 
      $summarySection .= "<h4>Local judges: ";
      $summarySection .= strlen($contest["Loc_judge1"]) > 1 ? $contest["Loc_judge1"] : " -- ";
      $summarySection .= strlen($contest["Loc_judge2"]) > 1 ? " and " . $contest["Loc_judge2"] : ""; 
      $summarySection .= "</h4>"; 
      $summarySection .= "<br />";
      $summarySection .= "<table class='table table-hover table-condensed'><thead><tr>";
      $summarySection .= "<th>Pen name, Title</th>";
      $summarySection .= "<th class='text-center'>";
      $summarySection .= strlen($contest["Nat_judge1"]) > 1 ? $contest["Nat_judge1"] : " -- " ;
      $summarySection .=  "</th>";
      $summarySection .= "<th class='text-center'>";
      $summarySection .= strlen($contest["Nat_judge2"]) > 1 ? $contest["Nat_judge2"] : "";
      $summarySection .= "</th>";
      $summarySection .= "<th class='text-center'>Total</th><th class='text-center'>Local Judges</th>"; 
      $summarySection .= "</tr></thead>";
      $summarySection .= "<tbody>";

      foreach($resultNatEntryEvalDetail as $entry){
        $contestEntries = array();
        if ($entry["ContestInstance"] == $contest["ContestInstance"]){
          array_push($contestEntries, $entry);
          }
          foreach($contestEntries as $item){
            $summarySection .= "<tr>";
            $summarySection .= "<td>" . $item["penName"] . ", <em>" . $item["title"] . "</em></td>";
            $judgerating = explode(',',$item["ratings"]); 
            $judge2rating = sizeof($judgerating) > 1? $judgerating[1] : '';
            $summarySection .= "<td class='text-center'>" . $judgerating[0] . "</td>";
            $summarySection .= "<td class='text-center'>" . $judge2rating . "</td>";
            $summarySection .= "<td class='text-center'>" . $item["ratingsTTL"] . "</td>";
            $summarySection .= "<td class='text-center'> -- </td>";
            $summarySection .= "</tr>";

          }

      };
      $summarySection .= "</tbody>";
      $summarySection .= "</table>";
      $summarySection .= "</div>";

     };
     echo $summarySection;
     ?> 
    </div>
      <?php
      } else {
      ?>
      <!-- if there is not a record for $login_name display the basic information form. Upon submitting this data display the contest available section -->
      <div id="notAdmin">
        <div class="row clearfix">
          <div class="col-md-12">
            <div id="instructions" style="color:sienna;">
              <h1 class="text-center" >You are not authorized to this space!!!</h1>
              <h4>University of Michigan - LSA Computer System Usage Policy</h4>
              <p>This is the University of Michigan information technology environment. You
                MUST be authorized to use these resources. As an authorized user, by your use
                of these resources, you have implicitly agreed to abide by the highest
                standards of responsibility to your colleagues, -- the students, faculty,
                staff, and external users who share this environment. You are required to
                comply with ALL University policies, state, and federal laws concerning
                appropriate use of information technology. Non-compliance is considered a
                serious breach of community standards and may result in disciplinary and/or
              legal action.</p>
              <div style="postion:fixed;margin:10px 0px 0px 250px;height:280px;width:280px;"><a href="http://www.umich.edu"><img alt="University of Michigan" src="img/michigan.png" /> </a></div>
              </div><!-- #instructions -->
            </div>
          </div>
        </div>
        <?php
        }
        include("footer.php");?>
        <!-- //additional script specific to this page -->
        <script src="js/admMyScript.js"></script>
        </div><!-- End Container of all things -->
      </body>
    </html>
    <?php
    $db->close();








