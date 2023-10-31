<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

$selectApplicant = $_GET['id'];

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
    <div class="container">
      <div class="row clearfix">
        <div class="col-md-12">
          <div class="btn-toolbar pagination-centered" role="toolbar" aria-label="admin_button_toolbar">
            <div class="btn-group" role="group" aria-label="contest_management">
              <a id="backToIndexBtn" type="button" class="btn btn-xs btn-default" href="index.php"><i class="fa fa-home" aria-hidden="true"></i></a>
              <a id="backToApplicants" type="button" data-toggle="tooltip" data-placement="right" title="go back to applicants listing" class="btn btn-xs btn-success" href="allApplicants.php"><i class="fa fa-user" aria-hidden="true"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="row clearfix">
            <?php
            $allDetails = "";
            $query = "SELECT `id`,`userFname`,`userLname`,`umid`,`uniqname`,`streetL`,`cityL`,`stateL`,`zipL`,`usrtelL`,`streetH`,`cityH`,`stateH`,`countryH`,`zipH`,`usrtelH`,`classLevel`,`school`, `campusLocation`,`major`,`department`,`gradYearMonth`,`degree`,CASE WHEN `finAid` = 1 THEN 'Y' ELSE '' END,CASE WHEN `finAidNotice` = 1 THEN 'Y' ELSE '' END,`finAidDesc`,`namePub`,`homeNewspaper`,`penName`,`created_on` FROM tbl_applicant WHERE id = ? ";
            if ($stmt = $db->prepare($query)) {
              $stmt->bind_param("i", $selectApplicant);
                $stmt->execute();
                $stmt->bind_result($id,$userFname,$userLname,$umid,$uniqname,$streetL,$cityL,$stateL,$zipL,$usrtelL,$streetH,$cityH,$stateH,$countryH,$zipH,$usrtelH,$classLevel,$school,$campusLocation,$major,$department,$gradYearMonth,$degree,$finAid,$finAidNotice,$finAidDesc,$namePub,$homeNewspaper,$penName,$created_on);
                while ($stmt->fetch()) {
                    echo '<div class="col-md-12"><h4> Details for: </h4><h4><span class="bg-success">&nbsp;' .  $userFname . ' ' . $userLname . ' - ' . $uniqname . '&nbsp;</span></h4>';
                    $allDetails .= 'UMID: <a href="https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=' . $umid . '" target=_"blank">' . $umid . '</a>';
                    $allDetails .= '<br>Pen Name: <strong>' . $penName . '</strong>';
                    $allDetails .= '</div>';
                    $allDetails .= '<div class="col-md-12">';
                    $allDetails .= '<div class="col-md-3 col-offset-md-1 bg-warning">';
                    $allDetails .= '<h5>Campus Address:</h5>' . $streetL . '<br>' . $cityL . ', ' . $stateL . ' ' . $zipL;
                    $allDetails .= '<br>&nbsp;';
                    // $allDetails .= '<br>Campus Phone: ' . $usrtelL;
                    $allDetails .= '</div>';
                    $allDetails .= '<div class="col-md-1"></div>';
                    $allDetails .= '<div class="col-md-3 col-offset-md-1 bg-info">';
                    $allDetails .= '<h5>Home Address:</h5>' . $streetH . '<br>' . $cityH . ', ' . $stateH . ' ' . $zipH;
                    $allDetails .= '<br>Country: ' . $countryH;
                    $allDetails .= '</div></div>';
                    // $allDetails .= '<br>Home Phone: ' . $usrtelH;
                    $allDetails .= '<div class="col-md-12">';
                    $allDetails .= '<br>Grade Level: <strong>' . $classLevel . '</strong>';
                    $allDetails .= '<br>School: <strong>' . $school . '</strong>';
                    $allDetails .= '<br>Campus: <strong>' . ucwords($campusLocation) . '</strong>';
                    $allDetails .= '<br>Concentration: <strong>' . $major . '</strong>';
                    $allDetails .= '<br>Department: <strong>' . $department . '</strong>';
                    $allDetails .= '<br>Graduation Date: <strong>' . $gradYearMonth . '</strong>';
                    $allDetails .= '<br>Degree: <strong>' . $degree . '</strong>';
                    $allDetails .= '<br>Financial Aid: <strong>' . $finAid . '</strong>';
                    $allDetails .= '<br>Accepted Financial Aid Notice: <strong>' . $finAidNotice . '</strong>';
                    $allDetails .= '<br>Financial Aid Description: <strong>' . $finAidDesc . '</strong>';
                    $allDetails .= '<br><br>Name for Publication: <strong>' . $namePub . '</strong>';
                    $allDetails .= '<br>Hometown News Outlet: <strong>' . $homeNewspaper . '</strong>';
                    $allDetails .= '<br><br><small>Date this record was last updated: ' . $created_on . '</small><br>&nbsp;</div>';
                    echo $allDetails;
                }
                $stmt->close();
            }
            ?>
      </div>
      <hr>
      <div class="row clearfix">
        <div class="col-md-12">
          <h5>Contests Entered</h5>
            <table class="table table-hover table-condensed">
              <thead><th><small>EntryID</small></th><th>Document</th><th>Title</th><th>Contest => Year</th></thead>
              <tbody>
          <?php
          $contestsEntered = '';
          $queryContests = <<<CESQL
          SELECT te.id, te.title, te.documentName, te.created_on, concat(lc.name,' => ',date_format(tc.date_open,'%Y')) AS contestName
          FROM tbl_entry te
          JOIN tbl_contest tc ON te.contestID = tc.id
          JOIN lk_contests lc ON tc.contestsID = lc.id
          WHERE applicantID = ? AND te.status IN(0,2)
          ORDER BY -te.id
CESQL;
          if ($stmtContests = $db->prepare($queryContests)) {
            $stmtContests->bind_param("i", $selectApplicant);
              $stmtContests->execute();
              $stmtContests->bind_result($entry_id, $title, $document, $created, $contest_name);
              while ($stmtContests->fetch()) {
                  $contestsEntered .= '<tr><td>' . $entry_id . '</td><td><a class="btn btn-xs btn-info" href="fileholder.php?file=' . $document . '" target="_blank"><i class="fa fa-book"></i></a></td></td><td>' . $title . '</td><td>' . $contest_name . '</td></tr>';
              }
              echo $contestsEntered;
              $stmtContests->close();
          }
          ?>
          </tbody></table>
        </div>
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
