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
			foreach ($attachmentArray as $attachment){
			?>
				<table class='linedFormTable'>
				<tr>
				<th colspan='2'>
					<span style='float:left; vertical-align:bottom;'>
						<?php echo $attachment['shortName']; ?>&nbsp;&nbsp;
						<a href='attachments/<?php echo $attachment['attachmentURL']; ?>' style='font-weight:normal;' target='_blank'><img src='images/arrow-up-right-blue.gif' alt='<?php echo _("view attachment");?>' title='<?php echo _("view attachment");?>' style='vertical-align:top;'></a></a>
					</span>
					<span style='float:right;'>
					<?php
						if ($user->canEdit()){ ?>
							<a href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getAttachmentForm&&attachmentID=<?php echo $attachment['attachmentID']; ?>",400,400)' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit attachment");?>'></a>  <a href='javascript:void(0);' class='removeAttachment' id='<?php echo $attachment['attachmentID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove this attachment");?>' title='<?php echo _("remove this attachment");?>'></a>
							<?php
						}else{
							echo "&nbsp;";
						}
					?>
					</span>
				</th>
				</tr>

				<?php if ($attachment['attachmentTypeShortName']) { ?>
				<tr>
				<td style='vertical-align:top; width:110px;'><?php echo _("Type:");?></td>
				<td style='vertical-align:top; width:350px;'><?php echo $attachment['attachmentTypeShortName']; ?></td>
				</tr>
				<?php
				}

				if ($attachment['descriptionText']) { ?>
				<tr>
				<td style='vertical-align:top; width:110px;'><?php echo _("Details:");?></td>
				<td style='vertical-align:top; width:350px;'><?php echo $attachment['descriptionText']; ?></td>
				</tr>
				<?php
				}
				?>

				</table>
				<br /><br />
			<?php
			}
		} else {
			echo "<i>"._("No attachments available")."</i><br /><br />";
		}

		if ($user->canEdit()){
		?>
		<a href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getAttachmentForm&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,400)' class='thickbox' id='newAttachment'><?php echo _("add attachment");?></a><br /><br />
		<?php
		}
?>
