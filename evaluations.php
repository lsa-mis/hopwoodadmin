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
    <link rel="stylesheet" href="css/bootstrap.min.css">
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
    <div class="modal js-loading-bar">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-body">

              <div class="progress blue">
                <span class="progress-left">
                  <span class="progress-bar"></span>
                </span>
                <span class="progress-right">
                  <span class="progress-bar"></span>
                </span>
                <div class="progress-value">...gimme a sec</div>
              </div>

            </div>
          </div>
        </div>
    </div>
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
    <div class="container-fluid"><!-- container of all things -->
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="btn-toolbar pagination-centered" role="toolbar" aria-label="admin_button_toolbar">
          <div class="btn-group" role="group" aria-label="contest_management">
            <a id="backToIndexBtn" type="button" class="btn btn-xs btn-default" href="index.php"><i class="fa fa-home" aria-hidden="true"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-md-12">
        <div class="bg-warning" style="padding:5px;">
        <ul>
            <li>Select the contest button to review the judge's evaluations.</li>
            <li>For entries in contests that move forward to the National Judging
            stage you will find a check box at that entries row. Place a check
            in the box if the associated entry is to be sent to the National
            Judging stage</li>
            <li>If an entry did not receive a rating and no comments were made,
             it will not show in the results.</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-xs-6">
        <strong>Local Results</strong>
          <a id="localEvalDownloadBtn" type="button" class="btn btn-xs btn-info" href="localEvalDownload.php" data-toggle="tooltip" data-placement="right" title="Click to download all submitted Local evaluations"><i class="fa fa-download"></i> All Local Evaluations</a>
          <br><br>
          <div class="btn-group" role="group" aria-label="local_results">
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Hopwood Contests <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a id="reportRatingBtn18" class="reportRatingBtn" data-contest="18" >Hopwood Underclassmen - Fiction</a></li>
                <li><a id="reportRatingBtn17" class="reportRatingBtn" data-contest="17" >Hopwood Underclassmen - Nonfiction</a></li>
                <li><a id="reportRatingBtn1"  class="reportRatingBtn" data-contest="1" >Hopwood Underclassmen - Poetry</a></li>
                <li role="separator" class="divider"></li>
                <li><a id="reportRatingBtn20" class="reportRatingBtn" data-contest="20" >Hopwood - Drama</a></li>
                <li><a id="reportRatingBtn19" class="reportRatingBtn" data-contest="19" >Hopwood - Novel</a></li>
                <li><a id="reportRatingBtn2" class="reportRatingBtn" data-contest="2" >Hopwood - Screenplay</a></li>
                <li role="separator" class="divider"></li>
                <li><a id="reportRatingBtn21" class="reportRatingBtn" data-contest="21" >Hopwood Graduate - Nonfiction</a></li>
                <li><a id="reportRatingBtn23" class="reportRatingBtn" data-contest="23" >Hopwood Graduate - Poetry</a></li>
                <li><a id="reportRatingBtn22" class="reportRatingBtn" data-contest="22" >Hopwood Graduate - Short Fiction</a></li>
                <li role="separator" class="divider"></li>
                <li><a id="reportRatingBtn24" class="reportRatingBtn" data-contest="24" >Hopwood Undergraduate - Nonfiction</a></li>
                <li><a id="reportRatingBtn26" class="reportRatingBtn" data-contest="26" >Hopwood Undergraduate - Poetry</a></li>
                <li><a id="reportRatingBtn25" class="reportRatingBtn" data-contest="25" >Hopwood Undergraduate - Short Fiction</a></li>
                <li role="separator" class="divider"></li>
                <li><a id="reportRatingBtn9" class="reportRatingBtn" data-contest="9" >Hopwood_Award Theodore Roethke Prize</a></li>
<!--                 <li role="separator" class="divider"></li>
                <li><a id="reportRatingBtn30" class="reportRatingBtn" data-contest="30" >Summer Hopwood - Drama or Screenplay</a></li>
                <li><a id="reportRatingBtn27" class="reportRatingBtn" data-contest="27" >Summer Hopwood - Nonfiction</a></li>
                <li><a id="reportRatingBtn28" class="reportRatingBtn" data-contest="28" >Summer Hopwood - Short Fiction</a></li>
                <li><a id="reportRatingBtn29" class="reportRatingBtn" data-contest="29" >Summer Hopwood - Poetry</a></li> -->
              </ul>
            </div>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Other Contests <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a id="reportRatingBtn16" class="reportRatingBtn" data-contest="16" >The Academy of American Poets (Graduate)</a></li>
                <li><a id="reportRatingBtn5" class="reportRatingBtn" data-contest="5" >The Academy of American Poets (Undergraduate)</a></li>
                <li><a id="reportRatingBtn12" class="reportRatingBtn" data-contest="12" >The Arthur Miller Award</a></li>
                <li><a id="reportRatingBtn6" class="reportRatingBtn" data-contest="6" >The Bain-Swiggett Poetry Prize</a></li>
                <li><a id="reportRatingBtn31" class="reportRatingBtn" data-contest="31" >The Cora Duncan Award in Fiction</a></li>
                <li><a id="reportRatingBtn34" class="reportRatingBtn" data-contest="34" >David Porter Award for Excellence in Journalism Prize</a></li>
                <li><a id="reportRatingBtn35" class="reportRatingBtn" data-contest="35" >Text-Image Composition Prize</a></li>
                <li><a id="reportRatingBtn8" class="reportRatingBtn" data-contest="8" >The Jeffrey L. Weisberg Memorial Prize in Poetry</a></li>
                <li><a id="reportRatingBtn11" class="reportRatingBtn" data-contest="11" >The Kasdan Scholarship in Creative Writing</a></li>
                <li><a id="reportRatingBtn33" class="reportRatingBtn" data-contest="33" >Keith Taylor Award for Excellence in Poetry</a></li>
                <li><a id="reportRatingBtn15" class="reportRatingBtn" data-contest="15" >The Marjorie Rapaport Award in Poetry</a></li>
                <li><a id="reportRatingBtn7" class="reportRatingBtn" data-contest="7" >The Michael R. Gutterman Award in Poetry</a></li>
                <li><a id="reportRatingBtn32" class="reportRatingBtn" data-contest="32" >Peter Philip Pratt Award in Fiction</a></li>
                <li><a id="reportRatingBtn10" class="reportRatingBtn" data-contest="10" >The Roy W. Cowden Memorial Fellowship</a></li>
              </ul>
            </div>
          </div>
      </div>
      <div class="col-xs-6">
        <strong>National Results</strong>
          <a id="natEvalDownloadBtn" type="button" class="btn btn-xs btn-info" href="natEvalDownload.php" data-toggle="tooltip" data-placement="right" title="Click to download all submitted National evaluations"><i class="fa fa-download"></i> All National Evaluations</a>
          <br><br>
          <div class="btn-group">
            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Contest Results <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a id="reportNationalEvalBtn9" class="reportNatRatingBtn" data-contest="9" >Hopwood_Award Theodore Roethke Prize</a></li>
              <li role="separator" class="divider"></li>
              <li><a id="reportNationalEvalBtn11" class="reportNatRatingBtn" data-contest=11 >The Kasdan Scholarship in Creative Writing</a></li>
              <li role="separator" class="divider"></li>
              <li><a id="reportNationalEvalBtn20" class="reportNatRatingBtn" data-contest="20" >Hopwood - Drama</a></li>
              <li><a id="reportNationalEvalBtn19" class="reportNatRatingBtn" data-contest="19" >Hopwood - Novel</a></li>
              <li><a id="reportNationalEvalBtn2" class="reportNatRatingBtn" data-contest="2" >Hopwood - Screenplay</a></li>
              <li role="separator" class="divider"></li>
              <li><a id="reportNationalEvalBtn21" class="reportNatRatingBtn" data-contest="21" >Hopwood Graduate - Nonfiction</a></li>
              <li><a id="reportNationalEvalBtn23" class="reportNatRatingBtn" data-contest="23" >Hopwood Graduate - Poetry</a></li>
              <li><a id="reportNationalEvalBtn22" class="reportNatRatingBtn" data-contest="22" >Hopwood Graduate - Short Fiction</a></li>
              <li role="separator" class="divider"></li>
              <li><a id="reportNationalEvalBtn24" class="reportNatRatingBtn" data-contest="24" >Hopwood Undergraduate - Nonfiction</a></li>
              <li><a id="reportNationalEvalBtn26" class="reportNatRatingBtn" data-contest="26" >Hopwood Undergraduate - Poetry</a></li>
              <li><a id="reportNationalEvalBtn25" class="reportNatRatingBtn" data-contest="25" >Hopwood Undergraduate - Short Fiction</a></li>
            </ul>
          </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-md-12">
        <div id="outputSummaryReports">
          <span id="outputSummaryEvalData"></span>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class="col-md-12">
        <div id="outputReports">
          <span id="outputEvalData"></span>
        </div>
      </div>
    </div>

      <?php
      } else {
      ?>
      <!-- if there is not a record for $login_name display the basic
      information form. Upon submitting this data display the contest available
       section -->
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
        <script src="js/evaluations.js"></script>
        </div><!-- End Container of all things -->
      </body>
    </html>
    <?php
    $db->close();
