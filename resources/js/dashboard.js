$(document).ready(function(){


  $("#submitDashboard").live("click", function() {
        submitDashboard();
    });

  $("#submitDashboardYearlyCosts").live("click", function() {
        submitDashboardYearlyCosts();
    });

});

function submitDashboard() {
	$.ajax({
		type:       "POST",
		url:        "ajax_htmldata.php?action=getDashboard",
		cache:      false,
		data:       {
                        "resourceTypeID": $("#resourceTypeID").val(),
                        "year":$("#year").val(),
                        "acquisitionTypeID": $("#acquisitionTypeID").val(),
                        "orderTypeID": $("#orderTypeID").val(),
                        "subjectID": $("#subjectID").val(),
                        "costDetailsID": $("#costDetailsID").val(),
                        "fundID": $("#fundID").val(),
                        "organizationID": $("#organizationID").val(),
                        "roleID": $("#roleID").val(),
                        "groupBy": $("#groupBy").val(),
                    },
		success:    function(html) {
            $("#dashboardTable").html(html);
            var dashtable = $('#dashboard_table').DataTable($.extend(true, {}, dataTablesDefaults, {
            aLengthMenu: [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "All"],
            ],
            aaSorting: []
            }));
            new $.fn.dataTable.FixedHeader(dashtable, { } );
		}
	});
}

function submitDashboardYearlyCosts() {
	$.ajax({
		type:       "POST",
		url:        "ajax_htmldata.php?action=getDashboardYearlyCosts",
		cache:      false,
		data:       {
                        "resourceTypeID": $("#resourceTypeID").val(),
                        "startYear":$("#startYear").val(),
                        "endYear":$("#endYear").val(),
                        "acquisitionTypeID": $("#acquisitionTypeID").val(),
                        "orderTypeID": $("#orderTypeID").val(),
                        "subjectID": $("#subjectID").val(),
                        "costDetailsID": $("#costDetailsID").val(),
                        "fundID": $("#fundID").val(),
                        "organizationID": $("#organizationID").val(),
                        "roleID": $("#roleID").val(),
                        "groupBy": $("#groupBy").val(),
                    },
		success:    function(html) {
            $("#dashboardTable").html(html);
            var dashtable = $('#dashboard_table').DataTable({aLengthMenu: [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "All"]
            ],
            aaSorting: []
            });
            new $.fn.dataTable.FixedHeader(dashtable, { } );
		}
	});
}


