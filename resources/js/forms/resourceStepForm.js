/*
 **************************************************************************************************************************
 ** CORAL Resources Module v. 1.2
 **
 ** Copyright (c) 2015 North Carolina State University
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



    $("#submitResourceStepForm").click(function () {
        updateResourceStep();

        //# sourceURL=js/forms/resourceStepForm.js
    });
    //# sourceURL=js/forms/resourceStepForm.js


    //do submit if enter is hit
    $('#userGroupID').keyup(function(e) {
        if(e.keyCode == 13) {
            $('#submitResourceStepForm').click();
        }
    });


    //the following are all to change the look of the inputs when they're clicked
    $('.changeDefaultWhite').live('focus', function(e) {
        if (this.value == this.defaultValue){
            this.value = '';
        }
    });

    $('.changeDefaultWhite').live('blur', function() {
        if(this.value == ''){
            this.value = this.defaultValue;
        }
    });


    $('.changeInput').addClass("idleField");

    $('.changeInput').live('focus', function() {


        $(this).removeClass("idleField").addClass("focusField");

        if(this.value != this.defaultValue){
            this.select();
        }

    });


    $('.changeInput').live('blur', function() {
        $(this).removeClass("focusField").addClass("idleField");
    });


    $('.changeAutocomplete').live('focus', function() {
        if (this.value == this.defaultValue){
            this.value = '';
        }

    });


    $('.changeAutocomplete').live('blur', function() {
        if(this.value == ''){
            this.value = this.defaultValue;
        }
    });




    $('select').addClass("idleField");
    $('select').live('focus', function() {
        $(this).removeClass("idleField").addClass("focusField");

    });

    $('select').live('blur', function() {
        $(this).removeClass("focusField").addClass("idleField");
    });

});


function updateResourceStep(){
        var reassigned = $("#userGroupID").val() == $("#currentGroupID").val() ? 0 : 1;
        $('#submitResourceStepForm').attr("disabled", "disabled");
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateResourceStep",
            cache:      false,
            data:       { resourceStepID: $("#editRSID").val(), userGroupID: $("#userGroupID").val(), applyToAll: $('#applyToAll').is(':checked'), orderNum: $('#orderNum').val(), note: $('#note').val(), userGroupIDChanged: reassigned },
            success:    function(html) {
                if (html){
                    $("#span_errors").html(html);
                }else{
                    tb_remove();
                    window.parent.updateRouting();
                    //eval("window.parent.update" + $("#tab").val() + "();");
                    return false;
                }

            }


        });

}

//kill all binds done by jquery live
function kill(){

    $('.addPayment').die('click');
    $('.remove').die('click');
    $('.changeAutocomplete').die('blur');
    $('.changeAutocomplete').die('focus');
    $('.changeDefault').die('blur');
    $('.changeDefault').die('focus');
    $('.changeInput').die('blur');
    $('.changeInput').die('focus');
    $('.select').die('blur');
    $('.select').die('focus');

}

//# sourceURL=js/forms/resourceStepForm.js
