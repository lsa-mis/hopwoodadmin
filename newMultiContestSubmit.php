<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin) {
  if (isset($_POST['insertMultiContest'])) {
    // $contestsID = $db->real_escape_string(htmlspecialchars($_POST['contestID']));
    
    $date_open = date("Y-m-d H:i:s", (strtotime($_POST['openDate'])));
    $fall_date_closed = date("Y-m-d H:i:s", (strtotime($_POST['closeDateFall'])));
    $fall_notes = $db->real_escape_string(htmlspecialchars($_POST['notesFallContests']));
    $winter_date_closed = date("Y-m-d H:i:s", (strtotime($_POST['closeDateWinter'])));
    $winter_notes = $db->real_escape_string(htmlspecialchars($_POST['notesWinterContests']));

    $sqlMultiInsert = <<< _SQL
    INSERT INTO `quilleng_ContestManager`.`tbl_contest`
    (
    `contestsID`,
    `date_open`,
    `date_closed`,
    `notes`,
    `edited_by`,
    `judgingOpen`,
    `status`)
    VALUES
    (1,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (5,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (6,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (7,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (8,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (9,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (10,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (15,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (16,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (17,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),
    (18,'$date_open','$fall_date_closed','$fall_notes',"srvr_scrpt",0,0),

    (2,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (11,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (12,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (19,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (20,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (21,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (22,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (23,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (24,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (25,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (26,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (31,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (32,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (33,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (34,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0),
    (35,'$date_open','$winter_date_closed','$winter_notes',"srvr_scrpt",0,0);
_SQL;

    if (!$resAdmin = $db->query($sqlMultiInsert)) {
      db_fatal_error("data insert issue", $db->error, $sqlMultiInsert, $login_name);
      $_SESSION['flashMessage'] = "You already added the contests for this academic year!";
      $_POST['insertMultiContest'] = false;
      safeRedirect('contestAdmin.php');
    } else {
      $_SESSION['flashMessage'] = "You successfully added the contests for this academic year!";
      $_POST['insertMultiContest'] = false;
      safeRedirect('contestAdmin.php');
    }
    $date_open = $fall_date_closed = $fall_notes = $winter_date_closed = $winter_notes = null;
    $_SESSION['flashMessage'] = "";
    $_POST['insertContest'] = false;
  }
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
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
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
    <h3>Create all the contests for the <?php echo date("Y") . "/" . (date("Y")+1); ?> academic year.</h3>
    <h5>Select the opening date for all the contests, the Fall contests closing date and the Winter contests closing date.</h5>
    <div id="flashArea"><span class='flashNotify'><?php echo $_SESSION['flashMessage']; $_SESSION['flashMessage'] = ""; ?></span></div>
        <div class="row clearfix">
      <div class="col-md-12">
        <div class="btn-toolbar pagination-centered" role="toolbar" aria-label="admin_button_toolbar">
          <div class="btn-group" role="group" aria-label="contest_management">
            <a id="backToIndexBtn" type="button" class="btn btn-xs btn-default" href="contestAdmin.php">Back to Contests Administration</a>
          </div>
        </div>
      </div>
    </div>
    <div class="row clearfix">
      <div class='outputContainer col-sm-8 col-md-offset-2'>
        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method='post' id='addContestForms' >
          <div class="form-group">
            <label for='openDate'>Open Date</label>
                <div class='input-group date' id='datetimepicker1'>
                    <input class="form-control" type='text' class="form-control" name='openDate' required />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group">
              <label for='openDate'>Close Date for Fall Contests</label>
                <div class='input-group date' id='datetimepicker2'>
                    <input class="form-control" type='text' class="form-control" name='closeDateFall' required />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class='form-group'>
              <label for='notes'>Notes for Fall Contest</label>
              <input class="form-control" type='text' name='notesFallContests' value='<?php echo 'Fall ' . date("Y"); ?>'>
            </div>
            <div class="form-group">
              <label for='openDate'>Close Date for Winter Contests</label>
                <div class='input-group date' id='datetimepicker3'>
                    <input class="form-control" type='text' class="form-control" name='closeDateWinter' required />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class='form-group'>
              <label for='notes'>Notes for Winter Contests</label>
              <input class="form-control" type='text' name='notesWinterContests' value='<?php echo 'Winter ' . (date("Y")+1); ?>'>
            </div>
          <input class="btn btn-success" type="submit" name="insertMultiContest" value="Insert Contests">
        </form>
      </div>
    </div>
    </div>
<?php
} else {
?>
<!-- if there is not a record for $login_name display the basic information form. Upon submitting this data display the contest available section -->
<div id="notAdmin">
<div class="row clearfix">
  <div class="col-md-8 col-md-offset-2">
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
      serious breach of community standards and may result in disciplinary and/or legal action.</p>
      </div><!-- #instructions -->
      <div class="center-block" style="width:100px;"><a href="http://www.umich.edu"><img alt="University of Michigan" src="img/michigan.png" width=80px /></a></div>
    </div>
  </div>
</div>
<?php
}
include("footer.php");?>
<!-- //additional script specific to this page -->
<script src="js/admMyScript.js"></script>
<script src="js/moment.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
 //Using Bootstrap 3 Datepicker v4 https://eonasdan.github.io/bootstrap-datetimepicker/
    $(function () {
        $('#datetimepicker1').datetimepicker();
        $('#datetimepicker2').datetimepicker({
            useCurrent: false //Important! See issue #1075
        });
        $('#datetimepicker3').datetimepicker({
            useCurrent: false //Important! See issue #1075
        });
        $("#datetimepicker1").on("dp.change", function (e) {
            $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
            $('#datetimepicker3').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker2").on("dp.change", function (e) {
            $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        });
    });
</script>
<!--       <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });
        $(function () {
            $('#datetimepicker2').datetimepicker();
        });
      </script> -->
</div><!-- End Container of all things -->
</body>
</html>
<?php
$db->close();
