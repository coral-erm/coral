<?php
	//performs attachment upload
	$attachmentName = basename($_FILES['myfile']['name']);

	$attachment = new Attachment();

	$exists = 0;

	//verify the name isn't already being used
	foreach ($attachment->allAsArray() as $attachmentTestArray) {
		if (strtoupper($attachmentTestArray['attachmentURL']) == strtoupper($attachmentName)) {
			$exists++;
		}
	}

	//if match was found
	if ($exists == 0){
		$target_path = "attachments/" . basename($_FILES['myfile']['name']);

        if ($_FILES['myfile']['error'] === UPLOAD_ERR_OK) {

            //note, echos are meant for debugging only - only file name gets sent back
            if(move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
                //set to web rwx, everyone else rw
                //this way we can edit the attachment directly on the server
                chmod ($target_path, 0766);
                echo "success uploading!";
            } else {
              header('HTTP/1.1 500 Internal Server Error');
              echo _("There was a problem saving your file to ").$target_path._(".  Please ensure your attachments directory is writable.");
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo uploadErrorMessage($_FILES['myfile']['error']);
        }
	}

?>
