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



$(function(){


    //the following is to fix a bug in IE6 and IE7 that prevents the entire line from being shown in a dropdown
    $("#documentID")
	.mouseover(function(){
	//   if($.browser.msie){
	//	    var cssObj = {
	//	      'width' : 'auto',
	//	      'position' : 'absolute'
	//	    }

	//	    $(this).css(cssObj);
	 //  }
	})

	.change(function(){
//	    if($.browser.msie){
//		    var cssObj = {
//		      'width' : '280px',
//		      'position' : ''
//		    }
//		$(this).css(cssObj);
//	    }
	})


});




$("#submitExpression").click(function () {
	$("#submitExpression").attr("disabled","disabled");

	qualifierList ='';
	$(".check_Qualifiers:checked").each(function(id) {
	      qualifierList += $(this).val() + ",";
	});

	$.post("ajax_processing.php?action=submitExpression", { expressionTypeID: $("#expressionTypeID").val(), documentText: $("#documentText").val(), documentID: $("#documentID").val(), expressionID: $("#expressionID").val(), qualifiers: qualifierList  } ,
		function(html){
			if (html){
				$("#span_errors").html(html);
			}else{
				window.parent.updateExpressions();
				window.parent.updateDocuments();
				window.parent.updateArchivedDocuments();
                myCloseDialog();
				return false;
			}

		});
	return false;
});


function newExpressionType(){
  $('#span_newExpressionType').html("<input type='text' name='newExpressionType' id='newExpressionType' aria-label='"+_('New expression type')+"' />  <button type='button' class='btn' onclick='addExpressionType();'>"+_("add")+"</button>");
}


function addExpressionType(){
  //add expressionType to db and returns updated select box
  $.ajax({
	 type:       "POST",
	 url:        "ajax_processing.php?action=addExpressionType",
	 cache:      false,
	 data:       { shortName: $("#newExpressionType").val() },
	 success:    function(html) { $('#span_expressionType').html(html); $('#span_newExpressionType').html("<span class='error'>"+_("ExpressionType has been added")+"</span>"); }
 });
}




$("#expressionTypeID").change(function () {
	updateQualifier();
});




function updateQualifier(){
      $("#div_Qualifiers").html('');
      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getQualifierCheckboxHTML&expressionTypeID=" + $("#expressionTypeID").val(),
         success:    function(html) {
         	if (html != ''){
         		$("#tr_Qualifiers").show();
         		$("#div_Qualifiers").html(html);
         	}else{
         		$("#tr_Qualifiers").hide();
         		$("#div_Qualifiers").html("<input type='hidden' id='qualifierID' value='' />");
         	}
         }


     });


}
