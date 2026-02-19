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

 $(function(){
    //bind all of the inputs
    $("#submitLicense").click(function (e) {
        e.preventDefault();
        submitLicenseForm();
    });

    //do submit if enter is hit
    $('#licenseStatusID').keyup(function(e) {
        if(e.keyCode == 13) {
            submitLicenseForm();
        }
    });

    $("#licenseName").autocomplete('ajax_processing.php?action=getLicenseList', {
        minChars: 2,
        max: 20,
        mustMatch: false,
        width: 265,
        delay: 10,
        matchContains: true,
        formatItem: function(row) {
            return "<span>" + row[0] + "</span>";
        },
        formatResult: function(row) {
            return row[0].replace(/(<.+?>)/gi, '');
        }
    });

    //once something has been selected, change the hidden input value
    $("#licenseName").result(function(event, data, formatted) {
        $("#licenseID").val(data[1]);
        $('#div_errorLicense').html('');
    });

    $('select').addClass("idleField");
    $('select').on('focus', function() {
        $(this).removeClass("idleField").addClass("focusField");
    });

    $('select').on('blur', function() {
        $(this).removeClass("focusField").addClass("idleField");
    });

    $("#licenseGroups").on("click", '.remove', function () {
        $(this).parents(".license-group").remove();
        return false;
    });

    $(".addLicense").on('click', function () {
        let lID = $('#licenseID').val();
        let isUnique = true;

        $("#licenseGroups .licenseID").each(function() {
            if ($(this).val() == lID) {
                isUnique = false;
            }
        });

        if ((lID == '') || (lID == null)){
            $('#div_errorLicense').html(_("Error - Please choose a valid license"));
            return false;
        } else if (!isUnique) {
            $('#div_errorLicense').html(_("Error - This license has already been selected"));
            return false;
        }else{
            $('#div_errorLicense').html('');
            //clone add license input and add to the existing license group section
            let $addableLicenseGroup = $("#newLicenseGroup").clone();
            let $addableLicenseNameEl = $addableLicenseGroup.find('#licenseName');

            $addableLicenseGroup.find('.addLicense').replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove this license") + "' title='" + _("remove this license") + "'/>");
            $addableLicenseNameEl.removeClass('changeAutocomplete');
            $addableLicenseNameEl.removeAttr('id').addClass('licenseName').prop("readonly", true);
            $addableLicenseGroup.find('#licenseID').removeAttr('id').addClass('licenseID');
            $addableLicenseGroup.find('.addLicense').removeClass('addLicense');
            $addableLicenseGroup.removeAttr('id');
            $addableLicenseGroup.addClass('license-group');
            $addableLicenseGroup.appendTo("#licenseGroups");

            //reset original add license inputs
            $("#licenseName").val('');
            $("#licenseID").val('');

            return false;
        }
    });
});

function submitLicenseForm(){
    let licenseList ='';
    let isValid = true;
    $(".licenseID").each(function(id) {
        let licenseIDValue = $(this).val();
        if (licenseIDValue) {
          licenseList += $(this).val() + ":::";
        } else {
            isValid = false;
        }
    });

    if (isValid) {
        $('#submitLicense').attr("disabled", "disabled");
            $.ajax({
                type:       "POST",
                url:        "ajax_processing.php?action=submitLicenseUpdate",
                cache:      false,
                data:       { resourceAcquisitionID: $("#editResourceAcquisitionID").val(), licenseStatusID: $("#licenseStatusID").val(), licenseList: licenseList  },
                success:    function(html) {
                if (html){
                    $("#span_errors").html(html);
                    $("#submitLicense").removeAttr("disabled");
                }else{
                    myDialogPOST();
                    window.parent.updateAcquisitions();
                    window.parent.updateRightPanel();
                    return false;
                }
            }
        });
    } else {
        $('#div_errorLicense').html(_("Error - Please choose a valid license"));
        return false;
    }
}

//kill all binds done by jquery live
function kill(){
    $('.addLicense').die('click');
    $('.select').die('blur');
    $('.select').die('focus');
    $('.remove').die('click');
}
