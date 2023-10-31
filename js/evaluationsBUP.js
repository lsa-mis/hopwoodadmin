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
      $("#outputEvalData").empty();

    $.getJSON("ratingReport.php", {id: useContests}, function(data){
      if (data.result.length == 0 ){
        $("#outputEvalData").html('<p>There are no evaluated entries to display.</p>');
      } else if (isNational){
        $("#outputEvalData").html('<strong>' + data.result[0].contestName  + ' </strong>' +
            '<a id="localEvalContestSpecificDownloadBtn" type="button" class="btn btn-xs btn-info" href="localEvalContestSpecificDownload.php?ID=' + useContests + '" data-toggle="tooltip" data-placement="right" title="Click to download this contests submitted Local evaluations"><i class="fa fa-download"></i></a>' +
            '<div class="table-responsive">' +
            '<table class="table table-hover dataout"><thead>' +
            '<th><small>Send to National</small></th>' +
            '<th><small>Entry ID</small></th><th>File</th>' +
            '<th>Title</th><th>Type</th><th>ClassLevel</th><th>Pen Name</th>' +
            '<th>First-Name</th><th>Last Name</th><th>UMID</th><th>Rating</th>' +
            '<th>Evaluator</th><th>Contestant comment</th>' +
            '<th>Committee comment</th></thead><tbody>');

          $.each(data.result, function(){
            var set_check = '';
            if (this.nationalstatus == 1) {
              set_check = 'checked';
            }
              $(".dataout").append("<tr><td><input type='checkbox' class='natCheckBox' " + set_check + " data-item='" + this.entryid + "' ></td>" +
                "<td><small>" + this.entryid + "</small></td>" +
                "<td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
               "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.penName +
                "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td><td>" + this.rank +
                "</td><td>" + this.rankedby + "</td><td class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.contestantcomment) + "'><div class='commentBlock'>" + htmlEntities(this.contestantcomment) + "</div></td><td class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.committeecomment) + "'><div class='commentBlock'>" + htmlEntities(this.committeecomment) + "</div></td></tr>");
          });
        $("#outputEvalData").append('</tbody></table></div>');
      } else if (useContests == 10){
        $("#outputEvalData").html('<strong>' + data.result[0].contestName  + ' </strong>' +
          '<div class="table-responsive">' +
          '<table class="table table-hover dataout"><thead>' +
          '<th><small>Entry ID</small></th><th>File</th>' +
          '<th>Title</th><th>Type</th><th>ClassLevel</th><th>Pen Name</th>' +
          '<th>First-Name</th><th>Last Name</th><th>UMID</th><th>Rating</th>' +
          '<th>Evaluator</th><th>Contestant comment</th>' +
          '<th>Committee comment</th></thead><tbody>');

        $.each(data.result, function(){
            $(".dataout").append("<tr><td><small>" + this.entryid +
              "</small></td><td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
             "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.penName +
              "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td><td>" + this.rank +
              "</td><td>" + this.rankedby + "</td><td class='comment_cell' class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.contestantcomment) + "'><div class='commentBlock'>" + htmlEntities(this.contestantcomment) + "</div></td><td class='comment_cell' class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.committeecomment) + "'><div class='commentBlock'>" + htmlEntities(this.committeecomment) + "</div></td></tr>");
        });
      $("#outputEvalData").append('</tbody></table></div>');
      } else {
          $("#outputEvalData").html('<strong>' + data.result[0].contestName  + ' </strong>' +
          '<a id="localEvalContestSpecificDownloadBtn" type="button" class="btn btn-xs btn-info" href="localEvalContestSpecificDownload.php?ID=' + useContests + '" data-toggle="tooltip" data-placement="right" title="Click to download this contests submitted Local evaluations"><i class="fa fa-download"></i></a>' +
            '<div class="table-responsive">' +
            '<table class="table table-hover dataout"><thead>' +
            '<th><small>Entry ID</small></th><th>File</th>' +
            '<th>Title</th><th>Type</th><th>ClassLevel</th><th>Pen Name</th>' +
            '<th>First-Name</th><th>Last Name</th><th>UMID</th><th>Rating</th>' +
            '<th>Evaluator</th><th>Contestant comment</th>' +
            '<th>Committee comment</th></thead><tbody>');

          $.each(data.result, function(){
              $(".dataout").append("<tr><td><small>" + this.entryid +
                "</small></td><td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
               "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.penName +
                "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td><td>" + this.rank +
                "</td><td>" + this.rankedby + "</td><td class='comment_cell' class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.contestantcomment) + "'><div class='commentBlock'>" + htmlEntities(this.contestantcomment) + "</div></td><td class='comment_cell' class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.committeecomment) + "'><div class='commentBlock'>" + htmlEntities(this.committeecomment) + "</div></td></tr>");
          });
        $("#outputEvalData").append('</tbody></table></div>');
      }
    });
  });

  // ========== NATIONAL RESULTS
    //Set the selected region to empty then populate it with the formatted result of the JSON string that is returned
  $(".reportNatRatingBtn").click ( function( event ){
    var useContests = $(this).data('contest');
      $("#outputEvalData").empty();

    $.getJSON("ratingNatReport.php", {id: useContests}, function(data){
      if (data.result.length == 0 ){
        $("#outputEvalData").html("<p>There are no evaluated entries to display.</p>");
      } else {
        $("#outputEvalData").html(
          '<h4>' + data.result[0].contestName  + '</h4>' +
          '<div class="table-responsive">' +
          '<table class="table table-hover dataout">'+
          '<thead><th><small>Entry ID</small></th><th>File</th><th>Title</th><th>Type</th><th>ClassLevel</th><th>Pen Name</th><th>First-Name</th><th>Last Name</th><th>UMID</th><th>Rating</th><th>Evaluator</th><th>Contestant comment</th><th>Committee comment</th></thead>'+
          '<tbody>');

          $.each(data.result, function(){
              $(".dataout").append("<tr><td><small>" + this.entryid +
                "</small></td><td><a class='btn btn-xs btn-info' href='fileholder.php?file=" + this.document +
               "' target='_blank'><i class='fa fa-book'></i></a></td><td class='comment_cell'><div class='commentBlock'>" + this.title + "</div></td><td>" + this.manuscriptType + "</td><td><small>" + this.classLevel + "</small></td><td>" + this.penName +
                "</td><td>" + this.firstname + "</a></td><td>" + this.lastname + "</td><td><a href='https://webapps.lsa.umich.edu/UGStuFileV2/App/Cover/Cover.aspx?ID=" + this.umid + "' target='_blank'>" + this.umid + "</a></td><td>" + this.rank +
                "</td><td>" + this.rankedby + "</td><td class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.contestantcomment) + "'><div class='commentBlock'>" + htmlEntities(this.contestantcomment) + "</div></td><td class='comment_cell' data-toggle='tooltip' data-placement='right' title='"+ htmlEntities(this.committeecomment) + "'><div class='commentBlock'>" + htmlEntities(this.committeecomment) + "</div></td></tr>");
          });
        $("#outputEvalData").append('</tbody></table></div>');
      }
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
