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
	$("#submitWorkflowForm").click(function () {
		submitWorkflow();
		return false;
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

	$(".removeStep").on('click', removeStep);
	$(".addStep").on('click', addStep);
	disableSameRowSteps();
	setArrows();
});

//Functions related to adding and removing steps.
function addStep(){
	let newStep = $('#inputRow');
	let existingStepTable = $('.stepTable');
	let stepName = newStep.find('#newStepName').val();
	let userGroup = newStep.find('#newUserGroup').val();
	let priorStep = newStep.find('#newPriorStepID').val();

	let noNameListed = (stepName == '' || stepName == null);
	if(noNameListed){
		$('#div_errorStep').html(_("Error - Step name is required"));
		newStep.find('#newStepName').trigger('focus');
		return false;
	}
	//Clear any errors and clone the row.
	$('#div_errorStep').html('');
	let stepRow = newStep.clone();

	//Set the select values since options are set as values in javascript, not the html that's cloned.
	stepRow.find('#newUserGroup').val(userGroup);
	stepRow.find('#newPriorStepID').val(priorStep);

	//Set the data-id attribute.
	stepRow.find('#newStepName').attr('data-id', 'new'+stepName);

	//Update the table row
	stepRow.removeClass('newStepTR');
	stepRow.addClass('stepTR');
	stepRow.removeAttr('id');

	//We need to change the "Add" button to a "Remove" button.
	let addRemoveButton = stepRow.find('#newAddButton');
		addRemoveButton.removeClass();
		addRemoveButton.addClass('btn');
		addRemoveButton.addClass('removeStep');
		addRemoveButton.html('');
		addRemoveButton.attr('title', _("remove this step"));
		addRemoveButton.removeAttr('id');
	let deleteImage = $('<img src="images/cross.gif" class="removeStep" alt="' + _("remove this step") + '" title="' + _("remove this step") + '" />');
		addRemoveButton.append(deleteImage);
		addRemoveButton.on('click', removeStep);
	existingStepTable.append(stepRow);

	//Clear out the existing input row.
	newStep.find('#newStepName').val('');
	newStep.find('#newUserGroup :nth-child(1)').prop('selected', true);
	newStep.find('#newPriorStepID :nth-child(1)').prop('selected', true);

	//Add the step to the prior Steps.
	priorStepsAdd(stepName);
	setArrows();
	return false;
}

function removeStep(){
		//Get this parent row.
		let parentRow = $(this).parents('tr.stepTR');
		let value = $(parentRow).find('.stepName').data('id');
		priorStepsRemove(value)
		parentRow.fadeTo(400, 0, function () {
			$(this).remove();
			setArrows();
			$(this).off('click');
	    });
		return false;
}

//Functions related to the Prior Steps Dropdowns
function priorStepsAdd(value){
	$('.priorStepID').each(function(){
		let newOption = $('<option value="new'+value+'">'+value+'</option>');
		$(this).append(newOption);
	});
	disableSameRowSteps();
}

function priorStepsRemove(value){
	$('.priorStepID').each(function(){
		let oldStep = $(this).find('option[value="'+value+'"]');
		oldStep.remove();
	});
	disableSameRowSteps();
}

function priorStepsUpdate(value, direction){
	$('.priorStepID').each(function(){
		let step = $(this).find('option[value="'+value+'"]');
		if(direction == 'up'){
			step.insertBefore(step.prev());
		}
		if(direction == 'down'){
			step.insertAfter(step.next());
		}
	});
	disableSameRowSteps();
}

function disableSameRowSteps(){
	let allOptions = $('.priorStepID>option');
	allOptions.removeAttr('disabled');
	$('.stepTR').each(function(){
		let currentRowID = $(this).find('.stepName').data('id');
		let currentRowOption = $(this).find(".priorStepID>option[value='"+currentRowID+"']");
		$(currentRowOption).attr('disabled', 'disabled');
	});
}

//Functions related to the Arrows.
function setArrows(){
	let stepTable = $('.stepTable');
	let validArrows = stepTable.find('.moveArrow');
	validArrows.css('visibility', 'visible');
	validArrows.off('click');

	let firstArrow = validArrows.first();
	let lastArrow = validArrows.last();
	firstArrow.css('visibility', 'hidden');
	lastArrow.css('visibility', 'hidden');
	validArrows.on('click', moveArrow);
}

function moveArrow(){
	let direction = $(this).attr('direction');
	let row = $(this).parents('tr.stepTR');
	let rowID = row.find('.stepName').data('id');
	priorStepsUpdate(rowID, direction);
	if(direction == 'up'){
		row.insertBefore(row.prev());
	}
	if(direction =='down'){
		row.insertAfter(row.next());
	}
	setArrows();
	return false;
}
//Validation Functions
function validateWorkflow (){
	if (!validateRowLength()){
 		$("#span_errors").html(_("Please add at least one step to this workflow."));
		return false;
	}
	if(!validateCircularStepLogic()){
 		$("#span_errors").html(_("Circular logic detected in parent steps. Please ensure at least parent can occur to trigger child steps."));
		return false;		
	}
	return true;
}

function validateCircularStepLogic(){
	let stepsWithParents = {};
	$('.stepTR').each(function(){
		let parentStep = $(this).find('.priorStepID').val();
		if(parentStep == ""){return true;}
		let currentID = $(this).find('.stepName').data('id');
		stepsWithParents[currentID] = parentStep;
	});
	for(var stepID in stepsWithParents){
		let parentID = stepsWithParents[stepID];
		//Does the Parent ID have a parent?
		if(!validateParentID(stepsWithParents, stepID, parentID)){
			return false;
		}
	}
	return true;
}

function validateParentID(stepArray, stepID, parentID){
	let parentCheck = stepArray[parentID];
	let noParentExists = (typeof parentCheck == 'undefined');
	if(noParentExists){return true;}
	let stepIsParent = (stepID == parentCheck);
	if(stepIsParent){return false;}
	return validateParentID(stepArray, stepID, parentCheck)
}

function validateRowLength(){
	let stepListRows = $('.stepTR');
	let stepListAmount = stepListRows.length;
	let rowsExist = (stepListAmount > 0);
	return rowsExist;
}

function submitWorkflow(){
	let stepTable = $('.stepTable');
	let stepRows = stepTable.find('tr');
	let stepValues = [];
	stepRows.each(function (index){
		let priorStepInput = $(this).find('.priorStepID ');
		let stepNameInput = $(this).find('.stepName');
		let groupIDInput = $(this).find('.userGroupID');
		let priorStepValue = (priorStepInput.val() == "") ? null : priorStepInput.val();
		stepValues.push({
			id: stepNameInput.data('id'),
			stepName: stepNameInput.val(),
			groupID: groupIDInput.val(),
			priorStep: priorStepValue,
			order: index+1,
		});
	});

	let formInput = {
		workflowID: $('#editWFID').val(),
		acquisitionTypeID: $('#acquisitionTypeID').val(),
		resourceFormatID: $('#resourceFormatID').val(),
		resourceTypeID: $('#resourceTypeID').val(),
		steps: stepValues,
	}
	console.log(stepValues);
	if (validateWorkflow()) {
		$('.submitWorkflowForm').attr("disabled", "disabled");
		$.ajax({
			type:       "POST",
			url:        "ajax_processing.php?action=submitWorkflow",
			cache:      false,
			data:       {formInput},
			success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitWorkflowForm").removeAttr("disabled");
					return false;
				}else{
					myDialogPOST();
					window.parent.updateWorkflowTable();
					return false;
				}
			}
		});
	}
	return false;
}

//kill all binds done by jquery live
function kill(){
	$('.addStep').off('click');
	$('.removeStep').off('click');
	$('.moveArrow').off('click');
	$('.changeDefault').off('blur');
	$('.changeDefault').off('focus');
	$('.changeInput').off('blur');
	$('.changeInput').off('focus');
	$('.select').off('blur');
	$('.select').off('focus');
}
