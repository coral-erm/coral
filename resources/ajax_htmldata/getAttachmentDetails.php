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

		if (is_array($attachmentArray) && count($attachmentArray) > 0) {
			foreach ($attachmentArray as $attachment){
			?>
			<div class="header">
				<h3><?php echo $attachment['shortName']; ?> </h3>
				<span class="addElement">
					<a href='attachments/<?php echo $attachment['attachmentURL']; ?>' <?php echo getTarget(); ?>><img src='images/arrow-up-right-blue.gif' alt='<?php echo _("view attachment");?>' title='<?php echo _("view attachment");?>'></a>
					<?php
						if ($user->canEdit()){ ?>
							<a href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getAttachmentForm&&attachmentID=<?php echo $attachment['attachmentID']; ?>",400,400)' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit attachment");?>'></a>  <a href='javascript:void(0);' class='removeAttachment' id='<?php echo $attachment['attachmentID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove this attachment");?>' title='<?php echo _("remove this attachment");?>'></a>
							<?php
						}else{
							echo "&nbsp;";
						}
					?>
					</span>
			</div>

			<dl class='dl-grid'>
					
				<?php if ($attachment['attachmentTypeShortName']) { ?>
				<dt><?php echo _("Type:");?></dt>
				<dd><?php echo $attachment['attachmentTypeShortName']; ?></dd>
				<?php
				}

				if ($attachment['descriptionText']) { ?>
				<dt><?php echo _("Details:");?></dt>
				<dd><?php echo $attachment['descriptionText']; ?></dd>
				<?php
				}
				?>

				</dl>
			<?php
			}
		} else {
			echo "<p><i>"._("No attachments available")."</i></p>";
		}

		if ($user->canEdit()){
		?>
		<p><a href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getAttachmentForm&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,400)' class='thickbox' id='newAttachment'><?php echo _("add attachment");?></a></p>
		<?php
		}
?>
