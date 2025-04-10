/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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

    $("#submitProductChanges").click(function (e) {
	e.preventDefault();
        submitProductForm();
    });


    //do submit if enter is hit
    $('#titleText').keyup(function(e) {
        if(e.keyCode == 13) {
            submitProductForm();
        }
    });

    //do submit if enter is hit
    $('#parentResourceName').keyup(function(e) {
        if(e.keyCode == 13) {
            submitProductForm();
        }
    });


    $('#isbnOrISSN').keyup(function(e) {
        if(e.keyCode == 13) {
            submitProductForm();
        }
    });


    $('#resourceFormatID').keyup(function(e) {
        if(e.keyCode == 13) {
            submitProductForm();
        }
    });

    $('#resourceTypeID').keyup(function(e) {
        if(e.keyCode == 13) {
            submitProductForm();
        }
    });



    $("input[name='parentResourceName']").autocomplete('ajax_processing.php?action=getResourceList', {
        minChars: 2,
        max: 20,
        mustMatch: false,
        width: 179,
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
    $("input[name='parentResourceName']").result(function(event, data, formatted) {
        inputid = $(this).next();

        if (data[1] != $("#editResourceID").val()){
            inputid.val(data[1]);
            $(this).next().next().html('');
        }else {
            $(this).next().next().html("<br />"+_("Error - Parent cannot be the same as the child"));
        }
    });



    $(".organizationName").autocomplete('ajax_processing.php?action=getOrganizationList', {
        minChars: 2,
        max: 20,
        mustMatch: false,
        width: 164,
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
    $(".organizationName").result(function(event, data, formatted) {
        $(this).parent().children('.organizationID').val(data[1]);
    });



    //the following are all to change the look of the inputs when they're clicked
    $('.changeDefault').on('focus', function(e) {
        if (this.value == this.defaultValue){
            this.value = '';
        }
    });

    $('.changeDefault').on('blur', function() {
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




    $('textarea').addClass("idleField");
    $('textarea').focus(function() {
        $(this).removeClass("idleField").addClass("focusField");
    });

    $('textarea').blur(function() {
        $(this).removeClass("focusField").addClass("idleField");
    });


    $(".removeParent").on('click', function () {
        $(this).parent().fadeTo(400, 0, function () {
            $(this).parent().remove();
        });
        return false;
    });

    $(".removeIssnIsbn").on('click', function () {
        $(this).parent().fadeTo(400, 0, function() {
            $(this).parent().remove();
        });
    });



    $(".remove").on('click', function () {
        $(this).parent().parent().parent().fadeTo(400, 0, function () {
            $(this).remove();
        });
        return false;
    });



    $(".addAlias").on('click', function () {

        var typeID = $('.newAliasTable .newAliasTR .aliasTypeID').val();
        var aName = $('.newAliasTable .newAliasTR .aliasName').val();

        if ((aName == '') || (aName == null) || (typeID == '') || (typeID == null)){
            $('#div_errorAlias').html(_("Error - Both fields are required"));
            return false;

        }else{
            $('#div_errorAlias').html('');

            //first copy the new alias being added
            var originalTR = $('.newAliasTR').clone();

            //next append to to the existing alias table body
            $('.newAliasTR').appendTo('.aliasTable');

            $('.aliasTable .newAliasTR .addAlias').replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove this alias") + "' title='" + _("remove this alias") + "'/>");
            $('.aliasTable .newAliasTR').removeClass('newAliasTR');

            //next put the original clone back, we just need to reset the values
            originalTR.appendTo('.newAliasTable');
            $('.newAliasTable .aliasTypeID option:selected').removeAttr('selected');
            $('.newAliasTable .aliasTypeID').val('');
            $('.newAliasTable .aliasName').val('');


            return false;
        }
    });


    $(".addIsbn").on('click', function() {
        var newIsbn = $('.isbnOrISSN_new').clone();
        newIsbn.removeClass('isbnOrISSN_new');
        var newIssnIsbnStr = "<div class='oneIssnIsbn'></div>";
        var newIssnIsbnObj = $(newIssnIsbnStr);
        var newIssnIsbnEnd = "<button type='button' class='btn'><img src='images/cross.gif' alt='"+_('remove Issn/Isbn')+"' title='"+_('remove Issn/Isbn')+"' class='removeIssnIsbn' /></button>"
        newIssnIsbnObj.append(newIsbn);
        newIssnIsbnObj.append(newIssnIsbnEnd);
        $('#existingIsbn').append(newIssnIsbnObj);
        $('#newIsbn input[type="text"]').val('');
    });

});

$(".addParent").on('click', function() {

    var parentID = $("#newParent .oneParent input[name='parentResourceNewID']").val();
    var parentName = $("#newParent .oneParent input[name='parentResourceName']").val();

    if (parentName == '') {
        return false;
    }

    if (parentID == '' || parentID == null) {
        $('#span_error_parentResourceName').html(_("Error - Parent is not found.  Please use the Autocomplete."));
        return false;
    }

    var newParentValue = $('.parentResource_new').clone();
    newParentValue.filter("input[name='parentResourceNewID']").attr("name","parentResourceID");
    newParentValue.removeClass('parentResource_new').css({"width": "180px"});
    newParentValue.attr('disabled', 'disabled');
    var newParentStr = "<div class='oneParent'></div>";
    var newParentObj = $(newParentStr);
    var newParentEnd = "<button type='button' class='btn'><img src='images/cross.gif' alt='"+_("remove parent")+"' title='"+_("remove parent")+"' class='removeParent' /></button></div>";
    newParentObj.append(newParentValue);
    newParentObj.append(newParentEnd);
    $('#existingParent').append(newParentObj);
    $('#newParent input[type="text"]').val('');
});


$(".addOrganization").on('click', function () {

    var typeID = $('.newOrganizationTable .organizationRoleID').val();
    var orgID = $('.newOrganizationTable .organizationID').val();
    var orgName = $('.newOrganizationTable .organizationName').val();

    if ((orgID == '') || (orgID == null) || (typeID == '') || (typeID == null)){
        if ((orgName== '') || (orgName == null) || (typeID == '') || (typeID == null)){
            $('#div_errorOrganization').html(_("Error - Both fields are required"));
        }else{
            $('#div_errorOrganization').html(_("Error - Organization is not found.  Please use the Autocomplete."));
        }

        return false;

    }else{
        $('#div_errorOrganization').html('');

        //first copy the new organization being added
        var originalTR = $('.newOrganizationTR').clone();

        //next append to to the existing organization table body
        $('.newOrganizationTR').appendTo('.organizationTable');

        $('.newOrganizationTR .addOrganization').replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove this organization") + "' title='" + _("remove this organization") + "'/>");

        $('.addOrganization').removeClass('addOrganization');
        $('.newOrganizationTR').removeClass('newOrganizationTR');

        //next put the original clone back, we just need to reset the values
        originalTR.appendTo('.newOrganizationTable');
        $('.newOrganizationTable .organizationRoleID').val('');
        $('.newOrganizationTable .organizationRoleID option:selected').removeAttr('selected');
       
        $('.newOrganizationTable .organizationName').val('');
        $('.newOrganizationTable .organizationID').val('');

        //put autocomplete back
        $('.newOrganizationTable .organizationName').autocomplete('ajax_processing.php?action=getOrganizationList', {
            minChars: 2,
            max: 20,
            mustMatch: false,
            width: 164,
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
        $('.newOrganizationTable .organizationName').result(function(event, data, formatted) {
            $(this).parent().children('.organizationID').val(data[1]);
        });



        return false;

    }
});


function validateForm (){
    myReturn=0;
    if (!validateRequired('titleText',"<br />"+_("Name must be entered to continue."))) myReturn="1";


    //for verifying org and aliases
    var typeID = $('.newAliasTable .newAliasTR .aliasTypeID').val();
        var aName = $('.newAliasTable .newAliasTR .aliasName').val();

    var roleID = $('.newOrganizationTable .organizationRoleID').val();
    var orgID = $('.newOrganizationTable .organizationID').val();
    var orgName = $('.newOrganizationTable .organizationName').val();

    //check organizations fields
    if (((orgID == '') || (orgID == null) || (roleID == '') || (roleID == null)) && ((roleID != '') || (orgID != ''))){
        if ((orgName== '') || (orgName == null) || (typeID == '') || (typeID == null)){
            $('#div_errorOrganization').html(_("Error - Both fields are required"));
        }else{
            $('#div_errorOrganization').html(_("Error - Organization is not found.  Please use Autocomplete."));
        }

        myReturn="1";

    }

    //check aliases
    if (((aName == '') || (aName == null) || (typeID == '') || (typeID == null)) && ((aName != '') || (typeID != ''))){
        $('#div_errorAlias').html(_("Error - Both fields are required"));
        myReturn="1";

    }
    if (myReturn == "1"){
        return false;
    }else{
        return true;
    }
}






function submitProductForm(){

    aliasTypeList ='';
    $(".aliasTypeID").each(function(id) {
        aliasTypeList += $(this).val() + ":::";
    });

    aliasNameList ='';
    $(".aliasName").each(function(id) {
        aliasNameList += $(this).val() + ":::";
    });


    organizationList ='';
    $(".organizationID").each(function(id) {
        organizationList += $(this).val() + ":::";
    });

    organizationRoleList ='';
    $(".organizationRoleID").each(function(id) {
        organizationRoleList += $(this).val() + ":::";
    });



    if (validateForm() === true) {
        var arrayisbn = Array();
        var isbnOrIssn = '';
        $("input[name='isbnOrISSN']").each(function() {
            arrayisbn.push($(this).val());
        });
        isbnOrIssn = arrayisbn.join();

        var arrayparents = Array();
        var parents = '';
        $("input[name='parentResourceID']").each(function() {
            if ($(this).val() != null && $(this).val() != '') {
                arrayparents.push($(this).val());
            }
        });


        $('#submitProductChanges').attr("disabled", "disabled");
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=submitProductUpdate",
            cache:      false,
            data:       { resourceID: $("#editResourceID").val(), titleText: $("#titleText").val(), parentResourcesID: JSON.stringify(arrayparents), descriptionText: $("#descriptionText").val(), resourceURL: $("#resourceURL").val(), resourceAltURL: $("#resourceAltURL").val(), resourceFormatID: $("#resourceFormatID").val(), resourceTypeID: $("#resourceTypeID").val(), archiveInd: getCheckboxValue('archiveInd'), aliasTypes: aliasTypeList, aliasNames: aliasNameList, organizationRoles: organizationRoleList, organizations: organizationList, isbnOrISSN: JSON.stringify(arrayisbn) },

            success:    function(html) {
                if (html){
                    $("#span_errors").html(html);
                    $("#submitProductChanges").removeAttr("disabled");
                }else{
                    myDialogPOST();
		            window.parent.updateProduct();
                    window.parent.updateRightPanel();
                    window.parent.updateTitle();
                    return false;
                }

            }


        });

    }


}


//kill all binds done by jquery live
function kill(){

    $('.addAlias').die('click');
    $('.addOrganization').die('click');
    $(".addParent").die('click');
    $(".addIsbn").die('click');
    $('.changeDefault').die('blur');
    $('.changeDefault').die('focus');
    $('.changeInput').die('blur');
    $('.changeInput').die('focus');
    $('.changeAutocomplete').die('blur');
    $('.changeAutocomplete').die('focus');
    $('.select').die('blur');
    $('.select').die('focus');
    $('.organizationName').die('focus');
    $('.remove').die('click');

}
