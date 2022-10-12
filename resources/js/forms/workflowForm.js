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

	//default lastkey variable to the last key as of when form loaded
	//for new workflows defaults to 0
	lastKey = $('#finalKey').val();

	 $("#submitWorkflowForm").click(function () {
		submitWorkflow();
	 });



	//the following are all to change the look of the inputs when they're clicked
	$('.changeDefaultWhite').on('focus', function(e) {
		if (this.value == this.defaultValue){
			this.value = '';
		}
	});

	 $('.changeDefaultWhite').on('blur', function() {
		if(this.value == ''){
			this.value = this.defaultValue;
		}
	 });


    	$('.changeInput').addClass("idleField");

	$('.changeInput').on('focus', function() {


		$(this).removeClass("idleField").addClass("focusField");

		if(this.value != this.defaultValue){
			this.select();
		}

	 });


	 $('.changeInput').on('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	 });



	$('select').addClass("idleField");
	$('select').on('focus', function() {
		$(this).removeClass("idleField").addClass("focusField");

	});

	$('select').on('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	});


	$(".moveArrow").on('click', function () {

	    var dir = $(this).attr('direction')

	    //first flip the rows
	    var movingKey = parseInt($(this).parent('.seqOrder').attr('key'));
	    var movingKeyHTML = $(this).parent().parent().html();


	    //this is the key we're switching places with
	    if (dir == 'up'){
	    	var nextKey = movingKey - 1;
	    }else{
	    	var nextKey = movingKey + 1;
	    }

	    var nextKeyHTML = $(".seqOrder[key='" + nextKey + "']").parent().html();


	    //hold the 3 fields so after the html is flipped we can reset them
	    var movingKeyStepName = $(this).parent().parent().children().children('.stepName').val();
	    var nextKeyStepName = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.stepName').val();
	    var movingKeyUserGroupID = $(this).parent().parent().children().children('.userGroupID').val();
	    var nextKeyUserGroupID = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.userGroupID').val();
	    var movingKeyPriorStepID = $(this).parent().parent().children().children('.priorStepID').val();
	    var nextKeyPriorStepID = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.priorStepID').val();

	    //flip the html
	    $(".seqOrder[key='" + nextKey + "']").parent().html(movingKeyHTML);
	    $(this).parent().parent().html(nextKeyHTML);

	    //now put those values back
	    $(".seqOrder[key='" + movingKey + "']").parent().children().children('.stepName').val(movingKeyStepName);
	    $(".seqOrder[key='" + nextKey + "']").parent().children().children('.stepName').val(nextKeyStepName);

	    $(".seqOrder[key='" + movingKey + "']").parent().children().children('.userGroupID').val(movingKeyUserGroupID);
	    $(".seqOrder[key='" + nextKey + "']").parent().children().children('.userGroupID').val(nextKeyUserGroupID);

	    $(".seqOrder[key='" + movingKey + "']").parent().children().children('.priorStepID').val(movingKeyPriorStepID);
	    $(".seqOrder[key='" + nextKey + "']").parent().children().children('.priorStepID').val(nextKeyPriorStepID);


	    //flip the key values
  	    $(".seqOrder[key='" + nextKey + "']").attr('key',  function() {
  			return 'hold';
		});
  	    $(".seqOrder[key='" + movingKey + "']").attr('key',  function() {
  			return nextKey;
		});
  	    $(".seqOrder[key='hold']").attr('key',  function() {
  			return movingKey;
		});


	    setArrows();
	    return false;
	});



	$(".removeStep").on('click', function () {

	    var removedKey = parseInt($(this).parent().parent().parent().children('.seqOrder').attr('key'));

	    $(".seqOrder[key='" + removedKey + "']").removeAttr('key');


	    //remove whole row from interface
	    $(this).parent().parent().parent().fadeTo(400, 0, function () {
		$(this).remove();
		$(this).die('click');
	    });


	    //also fix key values for each existing subsequent step - set to current key - 1
	    nextKey = removedKey+1;

	    for(var i=nextKey; i<=lastKey; i++){

		$(".seqOrder[key='" + i + "']").attr('key',  function() {
  			return i-1;
		});

	    }


	    if(removedKey == lastKey){
	    	prevKey = lastKey-1;

	    	//also add last class key to this for easier reference
	    	$(".seqOrder[key='" + prevKey + "']").addClass('lastClass');

	    }


	    lastKey--;


	    setArrows();
	    updatePriorSteps('removed');
	    return false;
	});




	$(".addStep").on('click', function () {
		var sName = $('.newStepTable').children().children().children().children('.stepName').val();

		if ((sName == '') || (sName == null)){
			$('#div_errorStep').html(_("Error - Step name is required"));
            $('.newStepTable').children().children().children().children('.stepName').focus();
			return false;
		}else{

			$('#div_errorStep').html('');

			//first copy the new step row being added
			var originalTR = $('.newStepTR').clone();

			//next append to to the existing table
			//it's too confusing to chain all of the children.
			$('.newStepTR').appendTo('.stepTable');

			$('.newStepTR').children().children().children('.addStep').replaceWith('<img src="images/cross.gif" class="removeStep" alt="' + _("remove this step") + '" title="' + _("remove this step") + '" />');
			$('.stepName').addClass('changeInput');
			$('.stepName').addClass('idleField');
			$('.userGroupID').addClass('changeSelect');
			$('.userGroupID').addClass('idleField');
			$('.userGroupID').css("background-color","");
			$('.priorStepID').addClass('changeSelect');
			$('.priorStepID').addClass('idleField');
			$('.priorStepID').css("background-color","");

			$('.newStepTR').children('.seqOrder').addClass('justAdded');
			$('.addStep').removeClass('addStep');
			$('.newStepTR').removeClass('newStepTR');

			//next put the original clone back, we just need to reset the values
			originalTR.appendTo('.newStepTable');
			$('.newStepTable').children().children().children('.seqOrder').html("<img src='images/transparent.gif' style='width:43px;height:10px;' />");
			$('.newStepTable').children().children().children().children('.stepName').val('');
			$('.newStepTable').children().children().children().children('.userGroupID').val('');
			$('.newStepTable').children().children().children().children('.priorStepID').val('');



			//need to set the key for justadded
			newKey = parseInt(lastKey) + 1;


			//set the just added key to the next one up
			$('.justAdded').attr('key',  function() {
  				return newKey;
			});

			//set just added to last class now that it's last and remove it from the previous last
			$('.lastClass').removeClass('lastClass');
			$('.justAdded').addClass('lastClass');
			$('.justAdded').removeClass('justAdded');

			lastKey = newKey;

			setArrows();
			updatePriorSteps(sName);
			return false;
		}
	});


	$('.stepName').on('change', function () {
		//don't update prior steps for the step in the 'add' section
		if ($(this).parent().parent().children('.seqOrder').attr('key') != ''){
		  	updatePriorSteps('change');
		}
	});


	updatePriorSteps('onload');
	setArrows();

 });



function updatePriorSteps(fromFunction){


	var stepArray=new Array();

	//loop through each step, we will use this for the previous step list in an array
	$(".stepName").each(function(id) {
	      stepArray[$(this).parent().parent().children('.seqOrder').attr('key')] = $.trim($(this).val());

	});





	$(".priorStepID").each(function(id) {

	     var currentSelectedStep='';
	     var currentSelectedKey='';


	     //happens on page load, look at the hidden input for loaded
	     if (fromFunction == 'onload'){
	     	thisKey = $(this).parent().parent().children('.seqOrder').attr('key');
	     	currentSelectedKey = $(".priorStepKey[key='" + thisKey + "']").val();
	     }else if (fromFunction == 'change'){
	     	//hold the current prior step id selected
	     	currentSelectedKey = $(this).val();
	     }else{
	     	//otherwise we can just take the text
	     	currentSelectedStep = $.trim($("option:selected",this).text());
	     }


	     thisStepName = $(this).parent().parent().children().children('.stepName').val();

	     //clear out current priorStepID dropdown and repopulate
	     var options = "<option value=''></option>";

	     $.each(stepArray, function(key, currentStepName) {
	     	if (typeof(currentStepName) !== 'undefined'){

			if ((currentSelectedKey == key) || (currentSelectedStep == currentStepName)){
				options += "<option value='" + key + "' selected>" + currentStepName + "</option>";
			}else if (currentStepName != thisStepName){
				options += "<option value='" + key + "'>" + currentStepName + "</option>";
			}

		}


	     });

	     $(this).html(options);

	});





}



function setArrows(){

	$(".seqOrder").each(function(id) {
	      thisKey = $(this).attr('key');

		if (thisKey != ''){
			//this is the only row so it shuld be transparent
			if(lastKey == 1){
				$('.seqOrder[key="1"]').html("<img src='images/transparent.gif' style='width:43px;height:20px;' />");
			}else{
				//first gets down arrow only
				if (thisKey == 1){
					$('.seqOrder[key="1"]').html("<img src='images/transparent.gif' style='width:20px;height:20px;' />&nbsp;<a href='javascript:void(0);' class='moveArrow' direction='down'><img src='images/arrow_down.gif' /></a>");

				//if this is the last one it gets up arrow only
				}else if (thisKey == lastKey){
					$(".seqOrder[key='" + thisKey + "']").html("<a href='javascript:void(0);' class='moveArrow' direction='up'><img src='images/arrow_up.gif' /></a>&nbsp;<img src='images/transparent.gif' style='width:20px;height:20px;' />");

				//otherwise display both arrows
				}else{
					$(".seqOrder[key='" + thisKey + "']").html("<a href='javascript:void(0);' class='moveArrow' direction='up'><img src='images/arrow_up.gif' /></a>&nbsp;<a href='javascript:void(0);' class='moveArrow' direction='down'><img src='images/arrow_down.gif' /></a>");


				}

			}
		}

	});


}






 function validateWorkflow (){
 	myReturn=0;

 	//if no steps are added
 	if (stepNameList == ':::'){
 		$("#span_errors").html(_("Please add at least one step to this workflow."));
 		myReturn = 1;
 	}


 	if (stepNameList.indexOf(':::') != '0'){
 		$("#span_errors").html(_("Please click the Add button to the first step before submitting this workflow."));
 		myReturn = 1;
 	}



 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}






function submitWorkflow(){
	updatePriorSteps('');

	stepNameList ='';
	$(".stepName").each(function(id) {
	      stepNameList += $(this).val() + ":::";
	});

	userGroupList ='';
	$(".userGroupID").each(function(id) {
	      userGroupList += $(this).val() + ":::";
	});


	priorStepList ='';
	$(".priorStepID").each(function(id) {
	      priorStepList += $(this).val() + ":::";
	});


	seqOrderList ='';
	$(".seqOrder").each(function(id) {
	      seqOrderList += $(this).attr('key') + ":::";
	});


	if (validateWorkflow() === true) {
		$('.submitWorkflowForm').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitWorkflow",
			 cache:      false,
			 data:       { workflowID: $("#editWFID").val(), resourceTypeID: $("#resourceTypeID").val(), resourceFormatID: $("#resourceFormatID").val(), acquisitionTypeID: $("#acquisitionTypeID").val(), stepNames: stepNameList, userGroups: userGroupList, priorSteps: priorStepList, seqOrders: seqOrderList },
			 success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitWorkflowForm").removeAttr("disabled");
				}else{
					myDialogPOST();
					window.parent.updateWorkflowTable();
					return false;
				}

			 }


		 });
	}


}





//kill all binds done by jquery live
function kill(){

	$('.addStep').die('click');
	$('.removeStep').die('click');
	$('.moveArrow').die('click');
	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');

}
