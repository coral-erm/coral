<?php
/*
**************************************************************************************************************************
** CORAL Licensing Module Terms Tool Add-On Terms Tool Add-On v. 1.0
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

include_once 'directory.php';

function get_effective_date($documentId) {
    $document = new Document(new NamedArguments(array('primaryKey' => $documentId)));

    if ((!$document->effectiveDate) || ($document->effectiveDate == '0000-00-00')){
        $effectiveDate = format_date($document->getLastSignatureDate());
    }else{
        $effectiveDate = format_date($document->effectiveDate);;
    }

    return $effectiveDate;
}

//get the passed in ISSN or ISBN
$issn = filter_input(INPUT_GET, 'issn', FILTER_SANITIZE_STRING);
$isbn = filter_input(INPUT_GET, 'isbn', FILTER_SANITIZE_STRING);
$typeId = filter_input(INPUT_GET, 'typeID', FILTER_SANITIZE_STRING);

$error = null;

//either isbn or isn must be passed in
if (($isbn == '') && ($issn == '')){
    $error = 'You must pass in either ISSN or ISBN.';
}

//get targets from the terms tool service for this ISBN or ISSN
$targetsArray = array();
try{
    $termsServiceObj = new TermsService(new NamedArguments(array('issn' => $issn, 'isbn' => $isbn)));
    $termsToolObj = $termsServiceObj->getTermsToolObj();
    $targetsArray = $termsToolObj->getTargets();
} catch(Exception $e) {
    $error = $e->getMessage() . "  Please verify your information in the configuration.ini file and try again.";
}

// No results found
if (empty($targetsArray)){
    $error = 'Sorry, no Full Text Providers are available for this ISSN/ISBN';
}

// create an array of unique expression type ids
$expressionTypeObj = new ExpressionType();
$uniqueExpressionTypeArray = array();
foreach ($targetsArray as $i => $targetArray){
    $expressionTypeArray = $expressionTypeObj->getExpressionTypesByResource($targetArray['public_name']);
    //loop through each displayable expression type and add to final array
    foreach ($expressionTypeArray as $expressionTypeID){
        $uniqueExpressionTypeArray[] = $expressionTypeID;
    }
    //end target loop
}
$uniqueExpressionTypeArray = array_unique($uniqueExpressionTypeArray);

// create an array of expression types to render
$expressionTypes = array();
foreach($uniqueExpressionTypeArray as $expressionTypeId) {
    if(!empty($typeId) && $typeId != $expressionTypeId) { continue; }
    $expressionTypes[] = $expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeId)));
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Terms Tool - License Terms</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link rel="stylesheet" href="css/terms.css" type="text/css" />
</head>
<body>
<div style="margin:10px auto; width: 900px; text-align: left;">
    <?php if(!empty($error)): ?>
        <p><?php echo $error; ?></p>
    <?php else: ?>
        <?php foreach($expressionTypes as $expressionType): ?>
        <div class="darkShaded" id="the-terms-titlebar">
            <h1>
                <?php echo $expressionType->shortName; ?> Terms for <?php echo $termsToolObj->getTitle(); ?>
            </h1>
        </div>
        <div id="the-terms">
            <?php foreach($expressionType->reorderTargets($targetsArray) as $i => $targetArray): ?>
                <span class="titleText"><?php echo $targetArray['public_name']; ?></span>

                <?php $expressionArray = $expressionType->getExpressionsByResource($targetArray['public_name']); ?>

                <?php if (empty($expressionArray)): ?>
                    <p>No <?php echo $expressionType->shortName; ?> terms defined.</p>
                <?php else: ?>
                    <?php foreach ($expressionArray as $expression): ?>
                        <p>
                            Terms as of <?php echo format_date($expression->getLastUpdateDate); ?> — the following terms apply
                            ONLY to articles accessed via <a href="<?php echo $targetArray['target_url']; ?>" target="_blank">
                                <?php echo $targetArray['public_name']; ?>
                            </a>
                        </p>
                        <div style="margin:0 0 30px 0;">
                            <div class="shaded" style="width:850px; padding:3px;">
                                <?php
                                    $qualifierArray = array();
                                    // variables for permitted/prohibited qualifiers (this should match up with the available qualifiers in the license module)
                                    $permittedQualifier = 'Permitted';
                                    //prohibited terms will display red x

                                    $prohibitedQualifier = 'Prohibited';
                                    foreach ($expression->getQualifiers as $qualifier){
                                        $qualifierArray[] = $qualifier->shortName;
                                        //permitted terms will display green checkbox
                                        if('PERMITTED' == strtoupper($qualifier->shortname)) {
                                            $icon = 'images/icon_check.gif';
                                        } else if ('PROHIBITED' == strtoupper($qualifier->shortname)) {
                                            $icon = 'images/icon_x.gif';
                                        } else {
                                            $icon = false;
                                        }
                                    }
                                ?>
                                <strong><?php echo $expressionType->shortName; ?> Notes:</strong> <?php if($icon): ?><img src="<?php echo $icon; ?>"><?php endif; ?>
                                <ul style="margin-left: 20px;">
                                <?php if (!empty($qualifierArray)): ?>
                                    <li>Qualifier: <?php echo implode(", ", $qualifierArray); ?></li>
                                <?php endif; ?>
                                <?php foreach ($expression->getExpressionNotes as $expressionNote): ?>
                                    <li><?php echo $expressionNote->note; ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php if ($expression->documentText): ?>
                                <div style="width:850px;">
                                    <p><strong>From the license agreement <?php echo get_effective_date($expression->documentID); ?>:</strong></p>
                                    <p><em><?php echo nl2br($expression->documentText); ?></em></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
