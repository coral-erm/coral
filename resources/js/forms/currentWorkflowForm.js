$(document).ready(function(){

	lastKey = $('#finalKey').val();

    $(".addStep").live('click', function() {
        var originalTR = $('.newStepTR').clone();
        $('.newStepTR').appendTo('.stepTable');
        $('.newStepTR').find('.addStep').attr({
          src: 'images/cross.gif',
          alt: _("remove this step"),
          title: _("remove this step")
        });
        $('.newStepTR').find('.addStep').removeClass('addStep').addClass('removeStep');
        $('.newStepTR').children('.seqOrder').addClass('justAdded');
        $('.newStepTR').removeClass('newStepTR').addClass('stepTR');

        //next put the original clone back, we just need to reset the values
        originalTR.appendTo('.newStepTable');
        $('.newStepTable').children().children().children('.seqOrder').html("<img src='images/transparent.gif' style='width:43px;height:10px;' />");
        $('.newStepTable').find('.stepName').val('');
        $('.newStepTable').find('.userGroupID').val('');
        $('.newStepTable').find('.mailReminderDelay').val('');
        $('.newStepTable').find('.priorStepID').val('option:first');

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
	

    });


    $(".removeStep").live('click', function () {
	    var removedKey = parseInt($(this).parent().parent().parent().children('.seqOrder').attr('key'));

	    $(".seqOrder[key='" + removedKey + "']").removeAttr('key');


        //remove whole row from interface
        $(this).parent().parent().parent().fadeTo(400, 0, function () {
            $(this).find(".action").val("delete");
            $(this).hide();
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

	$(".moveArrow").live('click', function () {
	
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

	    var movingKeyMailReminderDelay = $(this).parent().parent().children().children('.mailReminderDelay').val();
	    var nextKeyMailReminderDelay = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.mailReminderDelay').val();

	    var movingKeyUserGroupID = $(this).parent().parent().children().children('.userGroupID').val();
	    var nextKeyUserGroupID = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.userGroupID').val();
	    var movingKeyPriorStepID = $(this).parent().parent().children().children('.priorStepID').val();
	    var movingKeyPriorStepText = $(this).parent().parent().children().children('.priorStepID').find(':selected').text();
	    var nextKeyPriorStepID = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.priorStepID').val();
	    var nextKeyPriorStepText = $(".seqOrder[key='" + nextKey + "']").parent().children().children('.priorStepID').find(':selected').text();

	    //flip the html
	    $(".seqOrder[key='" + nextKey + "']").parent().html(movingKeyHTML);
	    $(this).parent().parent().html(nextKeyHTML);
	    
	    //now put those values back
	    $(".seqOrder[key='" + movingKey + "']").parent().children().children('.stepName').val(movingKeyStepName);
	    $(".seqOrder[key='" + nextKey + "']").parent().children().children('.stepName').val(nextKeyStepName);

	    $(".seqOrder[key='" + movingKey + "']").parent().children().children('.mailReminderDelay').val(movingKeyMailReminderDelay);
	    $(".seqOrder[key='" + nextKey + "']").parent().children().children('.mailReminderDelay').val(nextKeyMailReminderDelay);

	    $(".seqOrder[key='" + movingKey + "']").parent().children().children('.userGroupID').val(movingKeyUserGroupID);
	    $(".seqOrder[key='" + nextKey + "']").parent().children().children('.userGroupID').val(nextKeyUserGroupID);

        movingKeyPriorStepID = movingKeyPriorStepText != '' ? movingKeyPriorStepID : 'option:first';
        nextKeyPriorStepID = nextKeyPriorStepText != '' ? nextKeyPriorStepID : 'option:first';
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

    $('.stepName').live('change', function () {
        //don't update prior steps for the step in the 'add' section
        if ($(this).parent().parent().children('.seqOrder').attr('key') != ''){
            updatePriorSteps('change');
        }
    });


   
    $("#submitCurrentWorkflowForm").click(function () {
       submitCurrentWorkflow();
    });

    setArrows();

    
});

//kill all binds done by jquery live
function kill() {
    $('.addStep').die('click'); 
    $('.removeStep').die('click'); 
	$('.moveArrow').die('click');
}


function validateWorkflow() {
    return true;
}


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



function submitCurrentWorkflow() {
    stepNameList ='';
    $(".stepTR .stepName").each(function(id) {
          stepNameList += $(this).val() + ":::";
    });

    userGroupList ='';
    $(".stepTR .userGroupID").each(function(id) {
          userGroupList += $(this).val() + ":::";
    });

    priorStepList ='';
    $(".stepTR .priorStepID").each(function(id) {
          priorStepList += $(this).val() + ":::";
    });

    stepIDList ='';
    $(".stepTR .stepID").each(function(id) {
          stepIDList += $(this).val() + ":::";
    });

    actionList = '';
    $(".stepTR .action").each(function(id) {
          actionList += $(this).val() + ":::";
    });

	seqOrderList ='';
	$(".stepTR .seqOrder").each(function(id) {
	      seqOrderList += $(this).attr('key') + ":::";
	}); 

	mailReminderDelayList ='';
	$(".stepTR .mailReminderDelay").each(function(id) {
	      mailReminderDelayList += $(this).val() + ":::";
	}); 
	


    if (validateWorkflow() === true) {
        $('.submitCurrentWorkflowForm').attr("disabled", "disabled");
          $.ajax({
             type:       "POST",
             url:        "ajax_processing.php?action=submitCurrentWorkflow",
             cache:      false,
             data:       { resourceID: $("#editRID").val(), stepNames: stepNameList, userGroups: userGroupList, priorSteps: priorStepList, stepIDs: stepIDList, actions: actionList, seqOrders: seqOrderList, mailReminderDelays: mailReminderDelayList },
             success:    function(html) {
                if (html){
                    $("#span_errors").html(html);
                    $("#submitCurrentWorkflowForm").removeAttr("disabled");
                }else{  
                    kill();
                    window.parent.tb_remove();
                    window.parent.updateRouting();
                    return false;
                }

             }
         });
    }
}
