/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

$(document).ready(function(){

	$('#ebscoKbTitleImportForm').submit(function(e){
		e.preventDefault();
		processEbscoKbImport('save', $(this));
	});

  $('#ebscoKbPackageImportForm').submit(function(e){
    e.preventDefault();
    processEbscoKbImport('progress', $(this));
  });

  $('.change-provider-option').change(function(e){
    var val = $('input[name="providerOption"]:checked').val();
    $('[id^="providerOption-help"]').hide();
    $('#providerOption-help-' + val).show();
    if(val === 'import' && $(this)){
      $('#selectProvider').hide();
    } else {
      $('#selectProvider').show();
    }
  }).trigger('change');

  $('input[name="titleFilter"]').change(function(e){
    var val = $(this).val();
    if(val === 'none'){
      $('#workflowOptionCard').hide();
    } else {
      $('#workflowOptionCard').show();
    }
  }).trigger('change');

  $("#providerText").autocomplete('ajax_processing.php?action=getOrganizationList', {
    minChars: 2,
    max: 20,
    mustMatch: false,
    width: 223,
    delay: 10,
    matchContains: true,
    formatItem: function(row) {
      return "<span style='font-size: 80%;'>" + row[0] + "</span>";
    },
    formatResult: function(row) {
      return row[0].replace(/(<.+?>)/gi, '');
    }

  });

  //once something has been selected, change the hidden input value
  $("#providerText").result(function(event, data, formatted) {
    $('#organizationId').val(data[1]);
  });


});

function processEbscoKbImport(status,form){

  $('#importErrors').hide();
  showLoader();
  var data = $(form).serializeArray();
  data.push({name: 'resourceStatus', value: status});
  $.ajax({
    type:       "POST",
    url:        "ajax_processing.php?action=importFromEbscoKb",
    cache:      false,
    data:       $.param(data),
    success:    function(response) {
      processResponse(response);
      console.log(response);
    },
    error: function(error){
      hideLoader();
      console.log(error);
    }
  });
}

function processResponse(response){
  switch(response.type){
    case 'error':
      hideLoader();
      showErrors(response.error);
      break;
    case 'batchers':
      console.log(response);
      startBatchers(response.resourceId, response.batchers);
      break;
    case 'redirect':
      resourceRedirect(response.status, response.resourceId);
      break;
    default:
      break;
  }
}

function startBatchers(resourceId, batchers){
  $('#packageSuccessfullyImported').show();
  var totalBatches = batchers.length;
  batchers.forEach(function(batch){
    var barId = 'batch' + batch.batchNumber;
    var html ='<p class="mt-1">Batch '+batch.batchNumber+' of ' + totalBatches + '. (Titles ' +batch.batchStart+ ' to ' + batch.batchEnd + ')</p>' +
        '<div class="progress">' +
          '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="' + barId + '">0%</div>' +
        '</div>' +
        '<ul id="' + barId +'-errors" class="text-danger"></ul>';
    $('#importLog').append(html);


    var completedOffsetCount = 0;
    var totalOffests = batch.offsets.length;
    batch.offsets.forEach(function(offset){
      batch.offset = offset;
      $.ajax({
        type:       "POST",
        url:        "ajax_processing.php?action=importFromEbscoKb",
        cache:      false,
        data:       batch,
        success: function(response) {

          if(response.error){
            response.error.forEach(function(err){
              $('#'+barId+'-errors').append('<li>' + err.text + '</li>')
              console.log(err);
            });
          }

          if (response.complete === true) {
            completedOffsetCount++;
            var newPercent = Math.ceil(completedOffsetCount / totalOffests * 100);
            $('#'+barId).attr('aria-valuenow', newPercent).css('width',newPercent+'%').html(newPercent+'%');
            if(response.titleErrors){
              response.titleErrors.forEach(function(err){
                $('#'+barId+'-errors').append('<li>' + err.text + '</li>')
              });
            }
          }
        },

        error: function(error){
          console.log(error);
        }
      });
    });

  });

  $(document).ajaxStop(function () {
    var importCompleteHtml = '<h1>Import Complete</h1>' +
        '<p class="mt-1"><i class="fa fa-check-circle-o text-success fa-5x"></i></p>' +
        '<a href="resource.php?ref=new&resourceID=' + resourceId + '">Continue</a>';
    $('#importingMessage').html(importCompleteHtml);
  });
}

function resourceRedirect(status, resourceId){
  if (status === 'progress') {
    window.parent.location=("resource.php?ref=new&resourceID=" + resourceId);
    myDialogPOST();
    return false;
  } else {
    window.parent.location=("queue.php?ref=new");
    myDialogPOST();
    return false;
  }
}

function showErrors(errors){
  var generalErrors = [];
  errors.forEach(function(err){
    if(err.target === 'general'){
      generalErrors.push('<li>'+err.text+'</li>');
    } else {
      $('#span_error_'+err.target).html(err.text);
    }
    if(generalErrors.length){
      $('#importErrorText').html('<ul>' + generalErrors.join('') + '</ul>').show();
      $('#importError').show();
    }
  });
}

function showLoader(){
  $('#importOverlay').show();
  $('#div_ebscoKbPackageImportForm').hide();
}

function hideLoader() {
  $('#importOverlay').hide();
  $('#div_ebscoKbPackageImportForm').show();
}
