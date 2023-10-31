function htmlEntities(str) {
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

$( document ).ready(function(){

  $('.js-loading-bar').modal({
    backdrop: 'static',
    show: false
  });

  $("#outputEvalData").empty();

  // ========== LOCAL RESULTS
    //Set the selected region to empty then populate it with the formatted result of the JSON string that is returned
  $(".reportRatingBtn").click ( function( event ){
    var $modal = $('.js-loading-bar'),
    $bar = $modal.find('.progress');

    $modal.modal('show');
    $bar.addClass('animate');
    setTimeout(function() {
      $bar.removeClass('animate');
      $modal.modal('hide');
    }, 7000);

    var useContests = $(this).data('contest');
    var isNational = false;
    if ([9,11,20,19,2,21,22,23,24,25,26].includes(useContests)){
      isNational = true;
    }
      $("#outputSummaryEvalData").empty();
      $("#outputEvalData").empty();
    $.getJSON("ratingReport.php", {id: useContests}, function(data){
      var jdg2_name = '';
      var jdg2_rating = '';
      if (data.result.length == 0 ){
        $("#outputEvalData").html('<p>There are no evaluated entries to display.</p>');
      } else if (isNational){
        if ( typeof data.result[0].judge2_name !== 'undefined' ){
          jdg2_name = '<th class = "judge2_color"><small>Judge2</small><br>' + data.result[0].judge2_name + '</th>';
        }
        $("#outputEvalData").html('<hr><hr><strong>' + data.result[0].contestName  + ' </strong>' +
            '<a id="localEvalContestSpecificDownloadBtn" type="button" class="btn btn-xs btn-info" href="localEvalContestSpecificDownload.php?ID=' + useContests + '" data-toggle="tooltip" data-placement="right" title="Click to download this contests submitted Local evaluations"><i class="fa fa-download"></i></a>' +
            '<div class="table-responsive">' +
            '<table class="table table-hover dataout"><thead>' +
            '<th><small>Send to National</small></th>' +
            '<th><small>Entry ID</small></th><th class = "judge1_color"><small>Judge1</small><br>' + data.result[0].judge1_name + '</th>' +
            jdg2_name +
            '<th>File</th><th>Title</th><th>Type</th><th>ClassLevel</th><th>eMail</th>' +
            '<th>First-Name</th><th>Last Name</th><th>Pen Name</th><th>UMID</th></thead><tbody>');

          $.each(data.result, function(){
            var set_check = '';
            if (this.nationalstatus == 1) {
              set_check = 'checked';
            }
            if (typeof this.judge_2 !== 'undefined'){
              jdg2_rating = "<td class = 'judge2_color'>" + this.judge_2 + "</td>";
            }
              $(".dataout").append("<tr><td><input type='checkbox' class='natCheckBox' " + set_check + " data-item='" + this.entryid + "' ></td>" +
                "<td><small>" + this.entryid +
                "</small></td><td class = 'judge1_color'>" + this.judge_1 + "</td>" +
                jdg2_rating +
                "<td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
                "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.email +
                "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td>"  + this.penName + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td></tr>");
          });
        $("#outputEvalData").append('</tbody></table></div>');
      } else if (useContests == 10){
        $("#outputEvalData").html('<hr><hr><strong>' + data.result[0].contestName  + ' </strong>' +
          '<br>Judged by: ' + data.result[0].judge1_name +
          '<div class="table-responsive">' +
          '<table class="table table-hover dataout"><thead>' +
          '<th><small>Entry ID</small></th><th>Award</th><th>File</th>' +
          '<th>Title</th><th>Type</th><th>ClassLevel</th><th>eMail</th>' +
          '<th>First-Name</th><th>Last Name</th><th>Pen Name</th><th>UMID</th>' +
          '<th>Evaluator</th>' +
          '</thead><tbody>');

        $.each(data.result, function(){
            $(".dataout").append("<tr><td><small>" + this.entryid +
              "</small></td><td>" + htmlEntities(this.committeecomment) + "</td><td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
             "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.email +
              "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td>" + this.penName + "<td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td>" +
              "<td>" + this.rankedby + "</td></tr>");
        });
      $("#outputEvalData").append('</tbody></table></div>');
    } else {
      if ( typeof data.result[0].judge2_name !== 'undefined' ){
        jdg2_name = '<th class = "judge2_color"><small>Judge2</small><br>' + data.result[0].judge2_name + '</th>';
      }
          $("#outputEvalData").html('<hr><hr><strong>' + data.result[0].contestName  + ' </strong>' +
          '<a id="localEvalContestSpecificDownloadBtn" type="button" class="btn btn-xs btn-info" href="localEvalContestSpecificDownload.php?ID=' + useContests + '" data-toggle="tooltip" data-placement="right" title="Click to download this contests submitted Local evaluations"><i class="fa fa-download"></i></a>' +
            '<div class="table-responsive">' +
            '<table class="table table-hover dataout"><thead>' +
            '<th><small>Entry ID</small></th><th class = "judge1_color"><small>Judge1</small><br>' + data.result[0].judge1_name + '</th>' +
            jdg2_name +
            '<th>File</th><th>Title</th><th>Type</th><th>ClassLevel</th><th>eMail</th>' +
            '<th>First-Name</th><th>Last Name</th><th>Pen Name</th><th>UMID</th></thead><tbody>');

          $.each(data.result, function(){
            if (typeof this.judge_2 !== 'undefined'){
              jdg2_rating = "<td class = 'judge2_color'>" + this.judge_2 + "</td>";
            }
              $(".dataout").append("<tr><td><small>" + this.entryid +
                "</small></td><td class = 'judge1_color'>" + this.judge_1 + "</td>" +
                jdg2_rating +
                "<td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
               "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.email +
                "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td>"  + this.penName + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td></tr>");
          });
        $("#outputEvalData").append('</tbody></table></div>');
      }
    })
    .fail(function() {
      $("#outputEvalData").html("There are no evaluated entries to display");
      console.log( "There are no judges assigned to this contest" );
    })
    .always(function() {
      console.log( "complete" );
    });
  });

  // ========== NATIONAL RESULTS
  //Set the selected region to empty then populate it with the formatted result of the JSON string that is returned
$(".reportNatRatingBtn").click ( function( event ){
  var $modal = $('.js-loading-bar'),
  $bar = $modal.find('.progress');

  $modal.modal('show');
  $bar.addClass('animate');
  setTimeout(function() {
    $bar.removeClass('animate');
    $modal.modal('hide');
  }, 7000);

  var useContests = $(this).data('contest');
    $("#outputSummaryEvalData").empty();
    $("#outputEvalData").empty();
  $.getJSON("ratingNatReport.php", {id: useContests}, function(data){
    var jdg2_name, jdg2_rating, jdg2_contestantcomment_title, jdg2_committecomment_title, jdg2_contestantcomment, jdg2_committecomment;
    jdg2_name = jdg2_rating = jdg2_contestantcomment_title = jdg2_committecomment_title = jdg2_contestantcomment = jdg2_committecomment = '';
    if (data.result.length == 0 ){
      $("#outputEvalData").html('<p>There are no evaluated entries to display.</p>');
     } else {
      if ( typeof data.result[0].judge2_name !== 'undefined' ){
        jdg2_name = '<th class = "judge2_color"><small>Judge2</small><br>' + data.result[0].judge2_name + '</th>';
        jdg2_contestantcomment_title = '<th class = "judge2_color"><small>Judge2<br>Contestant Comments</small><br>' + data.result[0].judge2_name + '</th>';
        jdg2_committecomment_title = '<th class = "judge2_color"><small>Judge2<br>Committee Comments</small><br>' + data.result[0].judge2_name + '</th>';
      }
        // Summary Results
        $("#outputSummaryEvalData").html('<hr><h4><span class="bg-primary">&nbsp;' + data.result[0].contestName  + '&nbsp;</span><br>SUMMARY of Results </h4>' +
          '<div class="table-responsive">' +
          '<table class="table table-hover summary_dataout"><thead>' +
          '<th>Entry ID</th><th class = "judge1_color"><small>Judge1</small><br>' + data.result[0].judge1_name + '</th>' +
          jdg2_name +
          '<th>File</th><th>Title</th><th>Type</th><th>Level</th><th>eMail</th>' +
          '<th>First-Name</th><th>Last Name</th><th>Pen Name</th><th>UMID</th><th>Financial<br>Aid</th>' +
          '</thead><tbody>');

          $.each(data.result, function(){
            if (typeof this.judge_2 !== 'undefined'){
              jdg2_rating = "<td class = 'judge2_color'>" + (this.judge_2 == null ? '' :  this.judge_2) + "</td>";
            }
              $(".summary_dataout").append("<tr><td>" + this.entryid +
                "</td><td class = 'judge1_color'>" + (this.judge_1 == null ? '' :  this.judge_1) + "</td>" +
                jdg2_rating +
                "<td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
               "' target='_blank'><i class='fa fa-book'></i></a></td><td>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.email +
                "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td>"  + this.penName + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td>" +
                "<td>" + this.fin_aid + "</td>" +
                "</tr>");
          });
        $("#outputSummaryEvalData").append('</tbody></table></div>');

        // Full results
        $("#outputEvalData").html('<hr><h4>FULL Results <small>(scroll for complete table)</small></h4>' +
        // '<a id="localEvalContestSpecificDownloadBtn" type="button" class="btn btn-xs btn-info" href="localEvalContestSpecificDownload.php?ID=' + useContests + '" data-toggle="tooltip" data-placement="right" title="Click to download this contests submitted Local evaluations"><i class="fa fa-download"></i></a>' +
          '<div class="table-responsive">' +
          '<table class="table table-hover dataout"><thead>' +
          '<th>Entry ID</th><th class = "judge1_color"><small>Judge1</small><br>' + data.result[0].judge1_name + '</th>' +
          jdg2_name +
          '<th>File</th><th>Title</th><th>Type</th><th>Level</th><th>eMail</th>' +
          '<th>First-Name</th><th>Last Name</th><th>Pen Name</th><th>UMID</th><th>Financial<br>Aid</th>' +
          '</thead><tbody>');

        $.each(data.result, function(){
          if (typeof this.judge_2 !== 'undefined'){
            jdg2_rating = "<td class = 'judge2_color'>" + (this.judge_2 == null ? '' :  this.judge_2) + "</td>";
            jdg2_contestantcomment = "<td class = 'judge2_color comment_cell'><div class='commentBlock'>" + (this.judge2_contestantcomment == null ? '(no comment)' : this.judge2_contestantcomment)  + "</div></td>";
            jdg2_committecomment = "<td class = 'judge2_color comment_cell'><div class='commentBlock'>" + (this.judge2_committeecomment == null ? '(no comment)' : this.judge2_committeecomment) + "</div></td>";
          }
            $(".dataout").append("<tr><td>" + this.entryid +
              "</td><td class = 'judge1_color'>" + (this.judge_1 == null ? '' :  this.judge_1) + "</td>" +
              jdg2_rating +
              "<td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
             "' target='_blank'><i class='fa fa-book'></i></a></td><td>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.email +
              "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td>"  + this.penName + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td>" +
              "<td>" + this.fin_aid + "</td>" +
              "<tr><td></td><td colspan='11' ><table><thead><th>Contestant Comments</th><th>Committe Comments</th></thead><tbody><tr>" +
              "<td class = 'judge1_color comment_cell'><div class='commentBlock'>" + (this.judge1_contestantcomment == null  || this.judge1_contestantcomment == '' ? '(no comment)' : this.judge1_contestantcomment)  + "</div></td><td class = 'judge1_color comment_cell'><div class='commentBlock'>" + (this.judge1_committeecomment == null  || this.judge1_committeecomment == '' ? '(no comment)' : this.judge1_committeecomment) + "</div></td>" +
              "<tr>" +
              jdg2_contestantcomment + jdg2_committecomment +
              "</tr>" +
              "</tr></tbody></table></td></tr></tr>");
          });
        $("#outputEvalData").append('</tbody></table></div>');
      }
  })
  .fail(function() {
    $("#outputEvalData").html("There are no judged entries to display");
    console.log( "There are no judges assigned to this contest" );
  })
  .always(function() {
    console.log( "complete" );
  });
});

    // ========== Check for national judging check bix toggle and set status on database
  $( 'body' ).on('change', 'input.natCheckBox', function() {
    var $input = $( this );
    if ( $input.prop( "checked" ) ) {
    $.post("addToNationalJudging.php", {entry_id: $input.data('item')})
      .done(function( data ){
        alert( data );
      });
    } else {
      $.post("removeFromNationalJudging.php", {entry_id: $input.data('item')})
      .done(function( data ){
        alert( data );
      });
    }
  }).change();

});
