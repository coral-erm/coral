<?php
//used to default to previously selected values when back button is pressed
//if the startWith is defined set it so that it will default to the first letter picked
/*
if ((CoralSession::get('res_startWith')) && ($reset != 'Y')){
  echo "startWith = '" . CoralSession::get('res_startWith') . "';";
  echo "$(\"#span_letter_" . CoralSession::get('res_startWith') . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
}

if ((CoralSession::get('res_pageStart')) && ($reset != 'Y')){
  echo "pageStart = '" . CoralSession::get('res_pageStart') . "';";
}

if ((CoralSession::get('res_recordsPerPage')) && ($reset != 'Y')){
  echo "recordsPerPage = '" . CoralSession::get('res_recordsPerPage') . "';";
}

if ((CoralSession::get('res_orderBy')) && ($reset != 'Y')){
  echo "orderBy = \"" . CoralSession::get('res_orderBy') . "\";";
}
*/
include '../templates/footer.php'; 
?>
