<?php
	/*
	**************************************************************************************************************************
	** CORAL Resources Module v. 1.2
	** Copyright (c) 2010 University of Notre Dame
	** This file is part of CORAL.
	** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
	** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
	** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
	**************************************************************************************************************************
	*/
	session_start();
	// CSV configuration
	$required_columns = array('fundCode' => 0, 'shortName' => 0);
	if ($_POST['submit']) {
		include_once 'directory.php';
		$pageTitle='Funds import';
		include 'templates/header.php';
		$uploaddir = 'attachments/';
		$uploadfile = $_FILES['uploadFile']['tmp_name'];
		print '<p>' . _("The file has been successfully uploaded.") . '</p>';

		// Let's analyze this file
		if (($handle = fopen($uploadfile, "r")) !== FALSE) {
			if (($data = fgetcsv($handle)) !== FALSE) {
				$columns_ok = true;
				foreach ($data as $key => $value) {
					$available_columns[$value] = $key;
				}
			} else {
				$error = _("Unable to get columns headers from the file");
			}
		} else {
			$error = _("Unable to open the uploaded file");
		}
		if ($error) {
			print "<p>" . _("Error: ") . $error . "</p>";
		} else {
		// Let's analyze this file
			if (($handle = fopen($uploadfile, "r")) !== FALSE) {
				$row = 0;
				while (($data = fgetcsv($handle)) !== FALSE) {
					$Fund = new Fund();
					$funds = $Fund -> allAsArray();
					// Convert to UTF-8
					$data = array_map(function($row) { return mb_convert_encoding($row, 'UTF-8'); }, $data);
					$Fund->fundCode = array_values($data)['0'];
					$Fund->shortName = array_values($data)['1'];
					$Fund->save();
					$row++;
				}
				print "<h2>" . _("Results") . "</h2>";
				print "<p> $row " . _("rows have been processed.") . "</p>";
			}
		}
	} else {
		?>
			<form enctype="multipart/form-data" action="importFunds.php" method="post" id="importForm">
				<div id='div_updateForm'>
					<div class='formTitle' style='width:245px;'><b><?php echo _("Import Funds");?></b></div><br/>
					<label for="uploadFile"><?php echo _("Select File");?></label>
					<input type="file" name="uploadFile" id="uploadFile"/><br/><br/>
					<input type="submit" name="submit" value='<?php echo _("import");?>' id="import-fund-button" class='submit-button' />
					<input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove(); return false;" id="cancel-fund-button" class='cancel-button' />
				</div>
			</form>
		<?php
	}
?>
