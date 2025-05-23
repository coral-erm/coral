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

$.urlParam = function (name) {
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  return (results) ? results[1] : 0;
}

var currentTab = 'Product';

$(document).ready(function () {
  $('.resource_tab_content').hide();

  $params = new URLSearchParams(document.location.search);
	$currentTab = $params.get('showTab') || "product";

  switch ($currentTab) {
    case 'product':
      $('#div_product').show();
      $('#div_fullRightPanel').show();
      updateProduct();
      break;
    case 'orders':
      $('#div_orders').show();
      $('#div_fullRightPanel').show();
      updateOrders();
      break;
    case 'acquisitions':
      $('#div_acquisitions').show();
      $('#div_fullRightPanel').show();
      updateAcquisitions();
      break;
    case 'access':
      $('#div_access').show();
      $('#div_fullRightPanel').show();
      updateAccess();
      break;
    case 'contacts':
      $('#div_contacts').show();
      $('#div_fullRightPanel').show();
      updateContacts();
      updateArchivedContacts(0);
      break;
    case 'issues':
      $('#div_issues').show();
      $('#div_fullRightPanel').show();
      updateIssues();
      break;
    case 'attachments':
      $('#div_attachments').show();
      $('#div_fullRightPanel').show();
      updateAttachments();
      break;
    case 'workflow':
      $('#div_workflow').show();
      $('#div_fullRightPanel').hide(); // HIDE
      updateWorkflow();
      $('#restartWorkflowDiv').hide();
      break;
    case 'cataloging':
      $('#div_cataloging').show();
      $('#div_fullRightPanel').show();
      updateCataloging();
      break;
  }

  $("#resourceAcquisitionSelect").change(function () {
    var newLoc = location.search;
    if (newLoc.includes('resourceAcquisitionID')) {
      newLoc = newLoc.replace(/resourceAcquisitionID=[^&$]*/i, 'resourceAcquisitionID=' + $(this).val());
    } else {
      newLoc += "&resourceAcquisitionID=" + $(this).val();
    }
    if (newLoc.includes('showTab')) {
      newLoc = newLoc.replace(/showTab=[^&$]*/i, 'showTab=' + currentTab);
    } else {
      newLoc += "&showTab=" + currentTab;
    }
    location.search = newLoc;
  });

  currentTab = $.urlParam('showTab') || "Product";
  $(".show" + currentTab).click();

  updateRightPanel();
  updateAttachmentsNumber();


  $(document).on('click', '.issuesBtn', function (e) {
    e.preventDefault();
    getIssues($(this));
  });

  $(document).on('click', '.downtimeBtn', function (e) {
    e.preventDefault();
    getDowntime($(this));
  });

  $(document).on('click', '#submitCloseIssue', function (e) {
    submitCloseIssue();
  });

  $(document).on('click', '#submitNewIssue', function (e) {
    e.preventDefault();
    submitNewIssue();
  });

  $(document).on('click', '#submitNewDowntime', function (e) {
    e.preventDefault();

    var errors = [];

    if ($("#startDate").val() == "") {
      errors.push({
        message: _("Must set a date."),
        target: '#span_error_startDate'
      });
    }

    if (errors.length == 0) {
      submitNewDowntime();
    } else {

      $(".addDowntimeError").html("");

      for (var index in errors) {
        error = errors[index];
        $(error.target).html(error.message);
      }
    }

  });

  $(document).on('click', '#submitUpdatedDowntime', function (e) {
    e.preventDefault();

    var errors = [];

    if ($("#endDate").val() === "") {
      errors.push({
        message: _("Must set an end date."),
        target: '#span_error_endDate'
      });
    }

    if (errors.length === 0) {
      submitUpdatedDowntime();
    } else {

      $(".updateDowntimeError").html("");

      for (var index in errors) {
        error = errors[index];
        $(error.target).html(error.message);
      }
    }

  });

  $(document).on('click', '.issueResources', function (e) {
    $(".issueResources").attr("checked", false);
    $(this).attr("checked", true);

    if ($(this).attr("id") === "otherResources") {
      $("#resourceIDs").fadeIn(250)
    } else {
      $("#resourceIDs").fadeOut(250)
    }

  });

  $(document).on('click', '#createIssueBtn', function () {
    $(".issueList").slideUp(250);
  });

  $(document).on('click', '#createDowntimeBtn', function () {
    $(".downtimeList").slideUp(250);
  });

  $("#getCreateContactForm").on("click", function (e) {
    e.preventDefault();
    $(this).fadeOut(250, function () {
      getInlineContactForm();
    });
  });

  $(document).on('click', '#createContact', function (e) {
    e.preventDefault();

    var errors = [];

    if ($("#contactAddName").val() == "") {
      errors.push({
        message: _("New contact must have a name."),
        target: '#span_error_contactAddName'
      });
    }

    if (!validateEmail($("#emailAddress").val())) {
      errors.push({
        message: _("CC must be a valid email."),
        target: '#span_error_contactEmailAddress'
      });
    }

    if (errors.length == 0) {
      var roles = new Array();
      $(".check_roles:checked").each(function () {
        roles.push($(this).val());
      });
      //create the contact and update the contact list
      createOrganizationContact({"organizationID": $("#organizationID").val(), "name": $("#contactAddName").val(), "emailAddress": $("#emailAddress").val(), "contactRoles": roles});
    } else {

      $(".addContactError").html("");

      for (var index in errors) {
        error = errors[index];
        $(error.target).html(error.message);
      }
    }

  });

  $("#addEmail").on("click", function (e) {
    e.preventDefault();
    var inputEmail = $("#inputEmail").val();
    var valid = validateEmail(inputEmail);
    if (valid) {
      var currentVal = $("#ccEmails").val();

      $("#currentEmails").append(inputEmail + ", ");

      if (!currentVal) {
        $("#ccEmails").val(inputEmail);
      } else {
        $("#ccEmails").val(currentVal + ',' + inputEmail);
      }

      $("#inputEmail").val('');
      $('#span_error_contactIDs').html('');
    } else {
      $('#span_error_contactIDs').html(_('CC must be a valid email.'));
    }
  });

  $(".showAccounts").click(function () {
    $('.resource_tab_content').hide();
    $('#div_accounts').show();
    $('#div_fullRightPanel').show();
    updateAccounts();
    return false;
  });

  $("#showAllChildResources").on('click', function () {
    $('.helpfulLink__hidden').removeClass('helpfulLink__hidden');
    $(this).hide();
  });


  $(function () {
    $('.date-pick').datePicker({startDate: '01/01/1996'});
  });


  // empty the new message span in 10 seconds
  setTimeout("emptyNewMessage();", 10000);


});


var showArchivedContacts = 0;

function updateProduct() {
  currentTab = "Product";
  $("#icon_product").html("<img src='images/littlecircle.gif' />");

  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getProductDetails&resourceID=" + $("#resourceID").val(),
	 success:    function(html) {
		$("#div_product .div_mainContent").html(html);
		
    updateRightPanel();
		bind_removes();
		$("#icon_product").html("<img src='images/butterflyfishicon.jpg' />");
	 }


  });

}

function updateOrders() {
  currentTab = "Orders";
  $("#icon_orders").html("<img src='images/littlecircle.gif' />");

  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getOrdersDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_orders .div_mainContent").html(html);
		bind_removes();
		$("#icon_orders").html("<img src='images/orders.gif' />");
	 }


  });

}

function updateAcquisitions() {
  currentTab = "Acquisitions";
  $("#icon_acquisitions").html("<img src='images/littlecircle.gif' />");

  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getAcquisitionsDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_acquisitions .div_mainContent").html(html);
		
		bind_removes();
		$("#icon_acquisitions").html("<img src='images/acquisitions.gif' />");
	 }


  });

}


function updateAccess() {
  currentTab = "Access";
  $("#icon_access").html("<img src='images/littlecircle.gif' />");

  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getAccessDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_access .div_mainContent").html(html);
		bind_removes();
		$("#icon_access").html("<img src='images/key.gif' />");
	 }


  });

}


function updateContacts() {
  currentTab = "Contacts";
  $("#icon_contacts").html("<img src='images/littlecircle.gif' />");

  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getContactDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_contacts .div_mainContent").html(html);
		bind_removes();
		$("#icon_contacts").html("<img src='images/contacts.gif' />");
	 }


  });

}


function updateArchivedContacts(showArchivedPassed) {
  if (typeof (showArchivedPassed) != 'undefined') {
    showArchivedContacts = showArchivedPassed;
  }


  $("#div_archivedContactDetails").append("<img src='images/circle.gif' />  " + _("Refreshing Contents..."));
  $.ajax({
    type: "GET",
    url: "ajax_htmldata.php",
    cache: false,
    data: "action=getContactDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val() + "&archiveInd=1&showArchivesInd=" + showArchivedContacts,
    success: function (html) {
      $("#div_archivedContactDetails").html(html);
      bind_removes();
    }


  });

}

function createOrganizationContact(contact) {
  var baseUrl = $("#orgModuleUrl").val();
  contact.contactRoles = contact.contactRoles.join();
  $.ajax({
    type: "POST",
    url: baseUrl + "ajax_processing.php?action=submitContact",
    cache: false,
    data: contact,
    success: function (res) {

      var data = {};
      data.contactIDs = [];

      $("#contactIDs option:selected").each(function () {
        data.contactIDs.push($(this).val());
      });

      data.action = "getOrganizationContacts";
      data.organizationID = contact.organizationID;
      data.contactIDs.push(res);

      $.ajax({
        type: "GET",
        url: baseUrl + "ajax_htmldata.php",
        cache: false,
        data: $.param(data),
        success: function (html) {
          $("#inlineContact").html(html).slideUp(250, function () {
            $("#getCreateContactForm").fadeIn(250);
          });
          $("#contactIDs").html(html);
        }
      });
    }
  });
}

function getInlineContactForm() {
  var baseUrl = $("#orgModuleUrl").val();
  $.ajax({
    type: "GET",
    url: baseUrl + "ajax_forms.php",
    cache: false,
    data: "action=getInlineContactForm",
    success: function (html) {
      $("#inlineContact").html(html).slideDown(250);
    }
  });
}

function updateIssues() {
  currentTab = "Issues";
  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getIssues&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_issues .div_mainContent").html(html);
		bind_removes();
	 }
  });
}

function validateNewIssue() {
  $(".error").html("");

  var errorFlag = 0;
  var organization = $('#sourceOrganizationID').val();
  var contact = $('#contactIDs').val();
  var subject = $('#subjectText').val();
  var body = $('#bodyText').val();
  var appliesTo = false;

  if (organization == '' || organization == null) {
    $('#span_error_organizationId').html(_('Opening an issue requires a resource to be associated with an organization. Please contact your IT department.'));
    errorFlag = 1;
  }

  if (contact == null || contact.length == 0) {
    $('#span_error_contactName').html(_('A contact must be selected to continue.'));
    errorFlag = 1;
  }

  if (subject == '' || subject == null) {
    $('#span_error_subjectText').html(_('A subject must be entered to continue.'));
    errorFlag = 1;
  }

  if (body == '' || body == null) {
    $('#span_error_bodyText').html(_('A body must be entered to continue.'));
    errorFlag = 1;
  }

  $('.entityArray').each(function () {
    if ($(this).is(':checked') || $(this).is(':selected')) {
      appliesTo = true;
      return false;
    }
  });

  if (!appliesTo) {
    errorFlag = 1;
    $('#span_error_entities').html(_('An issue must be associated with an organization or resource(s).'));
  }

  if (errorFlag == 0) {
    return true;
  }
  return false;
}

function submitNewIssue() {

  if (validateNewIssue()) {
    $.ajax({
      type: "POST",
      url: "ajax_processing.php?action=insertIssue",
      cache: false,
      data: $("#newIssueForm").serialize(),
      success: function (res) {
        updateIssues();
        myDialogPOST();
      }
    });
  }
}

function submitNewDowntime() {

  var data = $("#newDowntimeForm").serialize();

  $.ajax({
    type: "POST",
    url: "ajax_processing.php?action=insertDowntime",
    cache: false,
    data: data,
    success: function (res) {
      updateIssues();
      myDialogPOST();
    }


  });
}

function submitUpdatedDowntime() {

  var data = $("#resolveDowntimeForm").serialize();

  $.ajax({
    type: "POST",
    url: "ajax_processing.php?action=updateDowntime",
    cache: false,
    data: data,
    success: function (res) {
      updateIssues();
      myDialogPOST();
    }


  });
}

function getIssues(element) {
  var data = element.attr("href");
  $.ajax({
    url: "ajax_htmldata.php",
    data: data,
    cache: false,
    success: function (html) {
      element.siblings(".issueList").html(html).slideToggle(250);
    }
  });

}

function getDowntime(element) {
  var data = element.attr("href");
  $.ajax({
    url: "ajax_htmldata.php",
    data: data,
    cache: false,
    success: function (html) {
      element.siblings(".downtimeList").html(html).slideToggle(250);
    }
  });

}

function submitCloseIssue() {
  $('#submitCloseIssue').attr("disabled", "disabled");
  $.ajax({
    type: "POST",
    url: "ajax_processing.php?action=submitCloseIssue",
    cache: false,
    data: {"issueID": $("#issueID").val(), "resolutionText": $("#resolutionText").val()},
    success: function (html) {
      if (html.length > 1) {
        $("#submitCloseIssue").removeAttr("disabled");
      } else {
        myDialogPOST();
        updateIssues();
        return false;
      }
    }
  });
}

function updateAccounts() {
  currentTab = "Accounts";
  $("#icon_accounts").html("<img src='images/littlecircle.gif' />");
  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getAccountDetails&resourceID=" + $("#resourceID").val(),
	 success:    function(html) {
		$("#div_accounts .div_mainContent").html(html);
		bind_removes();
		$("#icon_accounts").html("<img src='images/lock.gif' />");
	 }


  });

}


function updateAttachments() {
  currentTab = "Attachments";
  $("#icon_attachments").html("<img src='images/littlecircle.gif' />");
  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getAttachmentDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_attachments .div_mainContent").html(html);
		bind_removes();
		$("#icon_attachments").html("<img src='images/attachment.gif' />");
	 }


  });

}


function updateAttachmentsNumber() {
  $.ajax({
    type: "GET",
    url: "ajax_htmldata.php",
    cache: false,
    data: "action=getAttachmentsNumber&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
    success: function (remaining) {
      if (remaining == "1") {
        $(".span_AttachmentNumber").html("(" + remaining + _(" record)"));
      } else {
        $(".span_AttachmentNumber").html("(" + remaining + _(" records)"));
      }
    }
  });
}


function updateWorkflow() {
  $("#icon_workflow").html("<img src='images/littlecircle.gif' />");
  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getWorkflowDetails&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_workflow .div_mainContent").html(html);
		bind_workflow();
		$("#icon_workflow").html("<img src='images/workflow.gif' />");
	 }


  });

}

function updateCataloging() {
  currentTab = "Cataloging";
  $("#icon_accounts").html("<img src='images/littlecircle.gif' />");
  $.ajax({
	 type:       "GET",
	 url:        "resources/cataloging.php",
	 cache:      false,
	 data:       "resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
	 success:    function(html) {
		$("#div_cataloging .div_mainContent").html(html);
		bind_removes();
		$("#icon_cataloging").html("<img src='images/cataloging.gif' />");
	 }

  });

}


function updateRightPanel() {
  $("#div_rightPanel").append("<img src='images/circle.gif' />  " + _("Refreshing Contents..."));
  $.ajax({
    type: "GET",
    url: "ajax_htmldata.php",
    cache: false,
    data: "action=getRightPanel&resourceID=" + $("#resourceID").val() + "&resourceAcquisitionID=" + $("#resourceAcquisitionSelect").val(),
    success: function (html) {
      $("#div_rightPanel").html(html + "&nbsp;");

    }


  });

}


function updateTitle() {
  $.ajax({
    type: "GET",
    url: "ajax_htmldata.php",
    cache: false,
    data: "action=getTitle&resourceID=" + $("#resourceID").val(),
    success: function (html) {
      $("#span_resourceName").html(html);
    }


  });

}

function bind_removes() {


  $(".removeContact").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this contact?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteInstance&class=Contact&id=" + $(this).attr("id"),
        success: function (html) {
          updateContacts();
          updateArchivedContacts();
        }


      });
    }

  });

  $(".removeAccount").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this account?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteInstance&class=ExternalLogin&id=" + $(this).attr("id"),
        success: function (html) {
          updateAccounts();
        }


      });
    }

  });


  $(".removeAttachment").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this attachment?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteInstance&class=Attachment&id=" + $(this).attr("id"),
        success: function (html) {
          updateAttachments();
        }


      });
    }

  });


  $(".removeResource").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this resource?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteResource&resourceID=" + $(this).attr("id"),
        success: function (html) {
          //post return message to index
          postwith('index.php', {message: html});
        }


      });
    }
  });

  $(".removeOrder").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this order?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteOrder&resourceAcquisitionID=" + $(this).attr("id"),
        success: function (html) {
          //post return message to index
          postwith('resource.php?resourceID=' + $("#resourceID").val(), {message: html});
        }
      });
    }
  });

  $(".removeResourceAndChildren").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this resource and all its children?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteResourceAndChildren&resourceID=" + $(this).attr("id"),
        success: function (html) {
          //post return message to index
          postwith('index.php', {message: html});
        }


      });
    }
  });

  $(".removeResourceSubjectRelationship").unbind('click').click(function () {

    tabName = $(this).attr("tab");

    if (confirm(_("Do you really want to remove this Subject?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=removeResourceSubjectRelationship&generalDetailSubjectID=" + $(this).attr("generalDetailSubjectID") + "&resourceID=" + $(this).attr("resourceID"),
        success: function (html) {
          eval("update" + tabName + "();");
        }

      });
    }
  });

  $(".removeNote").unbind('click').click(function () {

    tabName = $(this).attr("tab");

    if (confirm(_("Do you really want to delete this note?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteResourceNote&resourceNoteID=" + $(this).attr("id"),
        success: function (html) {
          eval("update" + tabName + "();");
        }


      });
    }

  });


}


function bind_workflow() {


  $(".markComplete").unbind('click').click(function () {
    $.ajax({
      type: "GET",
      url: "ajax_processing.php",
      cache: false,
      data: "action=markComplete&resourceStepID=" + $(this).attr("id"),
      success: function (html) {
        updateWorkflow();
      }


    });
  });


  $(".restartWorkflow").unbind('click').click(function () {
    $('#restartWorkflowDiv').show();
  });


  $(".restartWorkflowSubmit").unbind('click').click(function () {
    if (confirm(_("Warning!  You are about to remove any steps that have been started and completed.  Are you sure you wish to continue?")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=restartWorkflow&resourceAcquisitionID=" + $(this).attr("id") + "&deleteWorkflow=" + $("#deleteWorkflow").is(':checked') + "&workflow=" + $("#workflowArchivingDate").val(),
        success: function (html) {
          updateWorkflow();
        }


      });
    }
  });

  $(".displayArchivedWorkflows").unbind('click').click(function () {
    $(".archivedWorkflow").toggle();
    if ($(".archivedWorkflow").is(":visible")) {
      $(this).html(_("hide archived workflows"));
      $("#displayArchivedWorkflowsIcon").attr("src", "images/minus_12.gif");
    } else {
      $(this).html(_("display archived workflows"));
      $("#displayArchivedWorkflowsIcon").attr("src", "images/plus_12.gif");
    }
  });

  $(".markResourceComplete").unbind('click').click(function () {
    if (confirm(_("Do you really want to mark this resource complete?  This action cannot be undone.")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=markResourceComplete&resourceAcquisitionID=" + $(this).attr("id"),
        success: function (html) {
          updateWorkflow();
        }
      });
    }
  });

  $(".removeResourceStep").unbind('click').click(function () {
    if (confirm(_("Do you really want to delete this step? If other steps depended on this one, they will be started upon deletion. This action cannot be undone")) == true) {
      $.ajax({
        type: "GET",
        url: "ajax_processing.php",
        cache: false,
        data: "action=deleteResourceStep&resourceStepID=" + $(this).attr("id"),
        success: function (html) {
          updateWorkflow();
        }


      });
    }

  });

}


function emptyDiv(divName) {
  $('#' + divName).html("");
}


function emptyNewMessage() {

  $('#div_new').fadeTo(1000, 0, function () {
    $('#div_new').html("");
  });

}
