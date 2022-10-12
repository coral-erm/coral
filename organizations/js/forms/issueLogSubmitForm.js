/*
**************************************************************************************************************************
** CORAL Organizations Module
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

	$('.date-pick').datePicker({startDate:'01/01/1996'});
    $("#submitIssueLogForm").click(function () {
        if(validateIssueForm() === true){
            $.ajax({
                type:       "POST",
                url:        "ajax_processing.php?action=submitIssueLog",
                cache:      false,
                data:       { issueLogID: $("#editIssueLogID").val(), organizationID: $("#editOrganizationID").val(), issueLogTypeID: $("#issueLogTypeID").val(), issueStartDate: $("#issueStartDate").val(), issueEndDate: $("#issueEndDate").val(), noteText: $("#noteText").val() },
                success:    function(html) {
                    if (html.length > 1){
                        $("#span_errors").html(html);
                    }else{
                        window.parent.updateIssues();
                        myCloseDialog();
                        return false;
                    }
                }
            });
        }
     });
 });

function validateIssueForm(){
    if($("#issueLogTypeID").val() == ''){
        $("#span_errors").html('<br />Please select an issue type');
        $("#issueLogTypeID").focus();
        return false;
    }else if($("#issueStartDate").val() == ''){
        $("#span_errors").html('<br />Please choose a start date');
        $("#issueStartDate").focus();
        return false;
    }else if($("#issueEndDate").val() == ''){
        $("#span_errors").html('<br />Please choose an end date');
        $("#issueEndDate").focus();
        return false;
    }else{
        return true;
    }
}
