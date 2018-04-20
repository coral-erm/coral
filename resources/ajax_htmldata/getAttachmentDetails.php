<?php
	$resourceID = $_GET['resourceID'];
    $resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
    $resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


		//get attachments
		$sanitizedInstance = array();
		$attachmentArray = array();
		foreach ($resourceAcquisition->getAttachments() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			$attachmentType = new AttachmentType(new NamedArguments(array('primaryKey' => $instance->attachmentTypeID)));
			$sanitizedInstance['attachmentTypeShortName'] = $attachmentType->shortName;

			array_push($attachmentArray, $sanitizedInstance);
		}

		if (count($attachmentArray) > 0){
        ?>
        <script>
        $('#div_fullRightPanel').hide();
        </script>
        <table class="dataTable">
            <thead>
                <tr>
                    <th><?php echo _("Name"); ?></th>
                    <th><?php echo _("Type"); ?></th>
                    <th><?php echo _("Details"); ?></th>
                    <th><?php echo _("Actions"); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
			foreach ($attachmentArray as $attachment){
            ?>
                <tr>
                    <td><?php echo $attachment['shortName']; ?></td>
                    <td><?php echo $attachment['attachmentTypeShortName']; ?></td>
                    <td><?php echo $attachment['descriptionText']; ?></td>
                    <td><a href='attachments/<?php echo $attachment['attachmentURL']; ?>' style='font-weight:normal;' target='_blank'><img src='images/arrow-up-right-blue.gif' alt='<?php echo _("view attachment");?>' title='<?php echo _("view attachment");?>'></a>&nbsp;<?php
						if ($user->canEdit()){ ?><a href='ajax_forms.php?action=getAttachmentForm&height=305&width=360&attachmentID=<?php echo $attachment['attachmentID']; ?>&modal=true' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit attachment");?>'></a>&nbsp;<a href='javascript:void(0);' class='removeAttachment' id='<?php echo $attachment['attachmentID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove this attachment");?>' title='<?php echo _("remove this attachment");?>'></a>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
            </table>
        <?php
		} else {
			echo "<i>"._("No attachments available")."</i><br /><br />";
		}

		if ($user->canEdit()){
		?>
		<a href='ajax_forms.php?action=getAttachmentForm&height=305&width=360&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>' class='thickbox' id='newAttachment'><?php echo _("add new attachment");?></a><br /><br />
		<?php
		}
?>

