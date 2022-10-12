/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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

 $(function(){


	 $("#submitSushiForm").click(function () {
	 	submitSushiService();
	 });



	//do submit if enter is hit
	$(':text').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitSushiService();
	      }
	});

 });






function submitSushiService(){

	$('#submitSushiForm').attr("disabled", "disabled");
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitSushiService",
		 cache:      false,
		 data:       { sushiServiceID: $("#editSushiServiceID").val(), platformID: $("#platformID").val(), serviceURL: $("#serviceURL").val(), wsdlURL: $("#wsdlURL").val(), requestorID: $("#requestorID").val(), customerID: $("#customerID").val(), platform: $("#platform").val(), security: $("#security").val(), reportLayouts: $("#reportLayouts").val(), releaseNumber: $("#releaseNumber").val(), login: $("#login").val(), password: $("#password").val(), serviceDayOfMonth: $("#serviceDayOfMonth").val(), noteText: $("#noteText").val(), apiKey: $("#apiKey").val() },
		 success:    function(html) {
				myCloseDialog();
				window.parent.updateSushiDetails();
				return false;
		 }


	 });

}
