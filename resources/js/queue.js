/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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
	$params = new URLSearchParams(document.location.search);
	$currentTab = $params.get('showTab') || "OutstandingTasks";
	// Tab IDs do not match action request names
	$requestAction = '';
	switch($currentTab) {
		case 'SubmittedRequests':
			$requestAction = 'getSubmittedQueue';
			break;
		case 'SavedRequests':
			$requestAction = 'getSavedQueue';
			break;
		default:
			$requestAction = 'getOutstandingQueue';
	}
	updatePage($requestAction);

	$('.deleteRequest').on('click', function () {
		deleteRequest($(this).attr("id"));
	});
});

function updatePage(requestAction) {
	$('#div_feedback').html("<img src = 'images/circle.gif' />"+_("Refreshing..."));
	$.ajax({
	  type:       "GET",
	  url:        "ajax_htmldata.php",
	  cache:      false,
	  data:       "action="+requestAction,
	  success:    function(html) {
		$('#div_QueueContent').html(html);
		completeTabUpdate();
	  }
	});
}

function updateTaskNumbers(classSuffix,requestAction) {
	taskData = [{"classSuffix":"OutstandingTasksNumber","requestAction":"getOutstandingTasksNumber"},
				{"classSuffix":"SavedRequestsNumber","requestAction":"getSavedRequestsNumber"},
				{"classSuffix":"SubmittedRequestsNumber","requestAction":"getSubmittedRequestsNumber"}];
	$.each(taskData,function(i,task) {
	   $.ajax({
	 	 type:       "GET",
	 	 url:        "ajax_htmldata.php",
	 	 cache:      false,
	 	 data:       "action="+task.requestAction,
	 	 success:    function(remaining) {
	 		if (remaining == 1){
				html = "(" + remaining + _(" record)");
	 		}else{
				html = "(" + remaining + _(" records)");
	 		}
			$(".span_"+task.classSuffix).html(html);
	 	 }
	  });
	});
}

function completeTabUpdate() {
   //make sure error is empty
   $('#div_error').html("");

   //also reset feedback div
   $('#div_feedback').html("");
	updateTaskNumbers();
}

 //currently you can only delete saved requests
 function deleteRequest(deleteID){
 	if (confirm(_("Do you really want to delete this request?")) == true) {
		$('#div_feedback').html("<img src = 'images/circle.gif' />"+_("Refreshing..."));
		$.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteResource&resourceID=" + deleteID,
		  success:    function(html) {

			showError(html);

			// close the div in 3 secs
			setTimeout("emptyError();",3000);

			$("#SavedRequests").click();

			return false;

		  }
		});

		//also reset feedback div
		$('#div_feedback').html("");
	}
}

function showError(html){
	$('#div_error').fadeTo(0, 5000, function () {
		$('#div_error').html(html);
	});
}

function emptyError(){
	$('#div_error').fadeTo(500, 0, function () {
		$('#div_error').html("");
	});
}
