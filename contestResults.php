<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

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
    <div id="flashArea"><span class='flashNotify'><?php echo $_SESSION['flashMessage']; $_SESSION['flashMessage'] = ""; ?></span></div>
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="btn-toolbar pagination-centered" role="toolbar" aria-label="admin_button_toolbar">
          <div class="btn-group" role="group" aria-label="contest_management">
            <a id="backToIndexBtn" type="button" class="btn btn-xs btn-default" href="index.php">Back to Contest Management</a>
          </div>
        </div>
      </div>
    </div>
    <div id="contest">
      <div class="row clearfix">
        <div class="col-md-12">
          <h5 class="text-muted">Select a contest that you want to view (Contests in <span class="text-success bg-success">green</span> are currently open for submissions,
            Contests in <span class="text-info bg-info">blue</span> are set to open for submissions in the future)</h5>
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <?php
            //query for existing contests and populate the panels
            $sqlContestSelect = <<<SQL
            SELECT
            `tbl_contest`.`id` AS ContestId,
            `tbl_contest`.`date_open`,
            `tbl_contest`.`date_closed`,
            `tbl_contest`.`notes` AS ContestNotes,
            `tbl_contest`.`created_by`,
            `lk_contests`.`name`,
            `lk_contests`.`shortName`,
            `lk_contests`.`freshmanEligible`,
            `lk_contests`.`sophmoreEligible`,
            `lk_contests`.`juniorEligible`,
            `lk_contests`.`seniorEligible`,
            `lk_contests`.`graduateEligible`
            FROM tbl_contest
            JOIN `lk_contests` ON (`tbl_contest`.`contestsID` = `lk_contests`.`id`)
            WHERE `tbl_contest`.`status` IN (0,2,4)
            ORDER BY date_closed DESC, name
SQL;
            $results = $db->query($sqlContestSelect);
            if (!$results) {
            echo "There is no contest information available";
            } else {
            $count = $i = 0;
            while ($instance = $results->fetch_assoc()) {
            $count = $i++;
            $panelColor = ($instance['date_closed'] >= date("Y-m-d H:i:s")) ? 'panel-success' : 'panel-default';
            $panelColor = ($instance['date_open'] > date("Y-m-d H:i:s")) ? 'panel-info' : $panelColor;

            ?>
            <div class="panel <?php echo $panelColor; ?> ">
              <div class="panel-heading" role="tab" id="heading<?php echo $count ?>">
                <h6 class="panel-title">
                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count ?>" aria-expanded="false" aria-controls="collapse<?php echo $count ?>">
                  <?php echo $instance['name'] . "  -->  <span class='text-muted'>open:</span> " . date("F jS, Y - g:i A", (strtotime($instance['date_open']))) . " - " . "<span class='text-muted'>close:</span> " . date("F jS, Y - g:i A", (strtotime($instance['date_closed']))) ?>
                </a>
                </h6>
              </div>
              <div id="collapse<?php echo $count ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $count ?>">
                <div class="panel-body">
                  <div class="well well-sm">Eligibility:
                    <?php
                    echo($instance['freshmanEligible'])? "Fr " : "";
                    echo($instance['sophmoreEligible'])? "So " : "";
                    echo($instance['juniorEligible'])? "Jr " : "";
                    echo($instance['seniorEligible'])? "Sr " : "";
                    echo($instance['graduateEligible'])? "Grd " : "";
                    ?>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                      <tr>
                        <th>AppID</th><th>File</th><th>Applicant Name</th><th>uniqname</th><th>Title</th><th>Pen Name</th><th>Date Entered</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sqlIndEntry = <<<SQL
                      SELECT *
                      FROM vw_entrydetail
                      WHERE ContestInstance = {$instance['ContestId']}  AND (vw_entrydetail.status IN (0,2))
                      ORDER BY uniqname
SQL;
                      $resultsInd = $db->query($sqlIndEntry);
                      if (!$resultsInd) {
                      echo "There are no applicants available";
                      } else {
                      $entryCount = 0;
                      while ($entry = $resultsInd->fetch_assoc()) {
                        $entryCount++;
                        echo '<tr><td>' . $entry['EntryId'] . '</td><td><a class="btn btn-xs btn-info" href="fileholder.php?file=' . $entry['document'] .
               '" target="_blank"><i class="fa fa-book"></i></a></td><td>' . $entry['firstname'] . " " . $entry['lastname'] . '</td><td>' . $entry['uniqname'] . '</td><td>' . $entry['title'] . '</td><td>' . $entry['penName'] . '</td><td>' . $entry['datesubmitted'] . '</td></tr>';
                      }
                      echo '<small>' . $entryCount . '</small>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <?php
          }
          }
          ?>
        </div>
      </div>
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
