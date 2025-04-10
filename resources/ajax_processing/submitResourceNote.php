<?php
		//if this is an existing resourceNote
		$resourceNoteID=$_POST['resourceNoteID'];

		if ($resourceNoteID){
			$resourceNote = new ResourceNote(new NamedArguments(array('primaryKey' => $resourceNoteID)));
		}else{
			//set up new resourceNote
			$resourceNote = new ResourceNote();
			$resourceNote->resourceNoteID = '';
		}

		$resourceNote->updateLoginID 		= $loginID;
		$resourceNote->updateDate			= date( 'Y-m-d H:i:s' );
		$resourceNote->noteTypeID 			= $_POST['noteTypeID'];
		$resourceNote->tabName 				= $_POST['tabName'];
		$resourceNote->entityID 			= $_POST['entityID'];
		$resourceNote->noteText 			= $_POST['noteText'];

		try {
			$resourceNote->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
