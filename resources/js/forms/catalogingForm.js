$(document).ready(function(){
  $('#catalogingForm').submit(submitCataloging);
});

function submitCataloging(){
  $('#submitCatalogingChanges').attr("disabled", "disabled");
  var form = $('#catalogingForm');
  $.post(
    form.attr('action'),
    form.serialize(),
    function(html) {
			if (html){
				$("#span_errors").html(html);
				$("#submitCatalogingChanges").removeAttr("disabled");
			}else{
			
				myDialogPOST();
				window.parent.updateCataloging();
			}
		}
  );


  return false;
}

//kill all binds done by jquery live
function kill(){

	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');

}

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


$('.changeAutocomplete').on('focus', function() {
	if (this.value == this.defaultValue){
		this.value = '';
	}

 });


 $('.changeAutocomplete').on('blur', function() {
	if(this.value == ''){
		this.value = this.defaultValue;
	}
 });




$('select').addClass("idleField");
$('select').on('focus', function() {
	$(this).removeClass("idleField").addClass("focusField");

});

$('select').on('blur', function() {
	$(this).removeClass("focusField").addClass("idleField");
});



$('textarea').addClass("idleField");
$('textarea').focus(function() {
	$(this).removeClass("idleField").addClass("focusField");
});

$('textarea').blur(function() {
	$(this).removeClass("focusField").addClass("idleField");
});
