<?php
		//if this is an existing contact
		$externalLoginID=$_POST['externalLoginID'];

		if ($externalLoginID){
			$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $externalLoginID)));
		}else{
			//set up new external login
			$externalLogin = new ExternalLogin();
			$externalLogin->externalLoginID =  '';
		}

		$externalLogin->updateDate				= date( 'Y-m-d H:i:s' );
		$externalLogin->externalLoginTypeID 	= $_POST['externalLoginTypeID'];
		$externalLogin->resourceID 				= $_POST['resourceID'];
		$externalLogin->loginURL				= $_POST['loginURL'];
		$externalLogin->username				= $_POST['username'];
		$externalLogin->emailAddress			= $_POST['emailAddress'];
		$externalLogin->password				= $_POST['password'];
		$externalLogin->noteText				= $_POST['noteText'];

		try {
			$externalLogin->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
