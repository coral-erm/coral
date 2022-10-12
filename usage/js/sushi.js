/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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

	updateOutstandingSushiImports();

  updateFailedSushiImports();

  updateAllSushiServices();


});



 function updateOutstandingSushiImports(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getOutstandingSushiImports",
          success:    function(html) { $('#div_OutstandingSushiImports').html(html);
          }
      });

 }

 function updateFailedSushiImports(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getFailedSushiImports",
          success:    function(html) { $('#div_FailedSushiImports').html(html);
          }
      });

 }

 function updateAllSushiServices(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllSushiServices",
          success:    function(html) { $('#div_AllSushiServices').html(html);
          }
      });

 }



 function deleteImportLog(importLogID){

  if (confirm(_("Do you really want to delete this import?")) == true) {

         $('#span_outstanding_feedback').html("<img src = 'images/circle.gif'>&nbsp;&nbsp;"+_("Processing..."));
         $.ajax({
      type:       "GET",
      url:        "ajax_processing.php",
      cache:      false,
      data:       "action=deleteImportLog&importLogID=" + importLogID,
      success:    function(html) {
      $('#span_outstanding_feedback').html(html);

      // close the span in 5 secs
      setTimeout("emptyResponse('span_outstanding_feedback');",5000);

      updateOutstandingSushiImports();
      }
        });

  }
 }


function runService(sushiServiceID, el){
  //$("html, body").scrollTop($('#div_run_feedback').offset().top);

  $(el).parent().html("<img src = 'images/circle.gif'>&nbsp;&nbsp;"+_("Running...")+"<br />");

	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=runSushiService&sushiServiceID=" + sushiServiceID,
          success:    function(html) {

            if (html.indexOf("Queue") !=-1){
              $("html, body").scrollTop($('#div_run_feedback').offset().top);
              $('#div_run_feedback').html(html);
            }

      			updateOutstandingSushiImports();
      			updateAllSushiServices();
          }
       });


}


