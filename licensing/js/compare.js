/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
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

        updateQualifier();
        updateSearch();

});


function updateSearch(){

      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getComparisonList&expressionTypeID=" + $('#expressionTypeID').val() + "&qualifierID=" + $('#qualifierID').val(),
         success:    function(html) {
         	$('#div_list').html(html);
         	}


     });

}



$("#expressionTypeID").change(function () {
	$('#qualifierID').val('');
	updateQualifier();
	updateSearch();
});



$("#qualifierID").change(function () {
	updateSearch();
});




function updateQualifier(){
	// update qualifier dropdown
      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getQualifierDropdownHTML&expressionTypeID=" + $("#expressionTypeID").val(),
         success:    function(html) {
         	if (html != ''){
         		$("#div_Qualifiers").show();
         		$("#div_Qualifiers").html(html);
         	}else{
         		$("#div_Qualifiers").hide();
         		$("#div_Qualifiers").html("<input type='hidden' id='qualifierID' value='' />");
         	}
         }


     });


}
