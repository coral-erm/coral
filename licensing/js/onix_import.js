$("#organizationName").keyup(function() {
	  $.ajax({
		 type:       "GET",
		 url:        "ajax_processing.php",
		 cache:      false,
		 async:	     true,
		 data:       "action=getExistingOrganizationName&shortName=" + $("#organizationName").val(),
		 success:    function(exists) {
			if (exists == "0"){
				$("#licenseOrganizationID").val("");
				$("#span_error_organizationNameResult").html("<br />"+_("Warning!  This organization will be added new."));

			}else{
				$("#licenseOrganizationID").val(exists);
				$("#span_error_organizationNameResult").html("");

			}
		 }
	  });
});

//used for autocomplete formatting
formatItem = function (row){
    return "<span>" + row[1] + "</span>";
}

formatResult = function (row){
    return row[1].replace(/(<.+?>)/gi, '');
}

$("#organizationName").autocomplete('ajax_processing.php?action=getOrganizations', {
	minChars: 2,
	max: 50,
	mustMatch: false,
	width: 233,
	delay: 20,
	cacheLength: 10,
	matchSubset: true,
	matchContains: true,
	formatItem: formatItem,
	formatResult: formatResult,
	parse: function(data){
	    var parsed = [];
	    var rows = data.split("\n");
	    for (var i=0; i < rows.length; i++) {
	      var row = $.trim(rows[i]);
	      if (row) {
		row = row.split("|");
		parsed[parsed.length] = {
		  data: row,
		  value: row[0],
		  result: formatResult(row, row[0]) || row[0]
		};
	      }
	    }

	    if (parsed.length == 0) {

		  $.ajax({
			 type:       "GET",
			 url:        "ajax_processing.php",
			 cache:      false,
			 async:	     true,
			 data:       "action=getExistingOrganizationName&shortName=" + $("#organizationName").val(),
			 success:    function(exists) {
				if (exists == "0"){
				        $("#licenseOrganizationID").val("");
				        $("#span_error_organizationNameResult").html("<br />"+_("Warning!  This organization will be added new."));

				}else{
					$("#licenseOrganizationID").val(exists);
					$("#span_error_organizationNameResult").html("");

				}
			 }
		  });

	    }
	}
 });


//once something has been selected, change the hidden input value
$("#organizationName").result(function(event, data, formatted) {
	if (data[0]){
		$("#licenseOrganizationID").val(data[0]);
		$("#span_error_organizationNameResult").html("");
	}
});

//Attach form pre-submission event for form validation
$('#importForm').submit(function() {
	return validateOnixImportForm();
});

function validateOnixImportForm() {
	var errorString = "";
	if($('#uploadFile').val() === "") {
		errorString += _("Please select a file to upload.");
	}
	if($('input:checkbox:checked').length === 0) {
		if(errorString.length > 0) {
			errorString += "\n\n";
		}
		errorString += _("At least one term type must be selected.");
	}
	if($('#organizationName').val() === "") {
		if(errorString.length > 0) {
			errorString += "\n\n";
		}
		errorString += _("The Publisher / Provider field must contain a value.");
	}
	if(errorString !== "") {
		alert(_("Please correct the following form error(s):\n\n") + errorString);
		return false;
	}
}
