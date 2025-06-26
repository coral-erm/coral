<?php
	Resource::setSearch($_POST['search']);

	$queryDetails = Resource::getSearchDetails();
	$whereAdd = $queryDetails["where"];
	$page = $queryDetails["page"];
	$orderBy = $queryDetails["order"];
	$recordsPerPage = $queryDetails["perPage"];

	//numbers to be displayed in records per page dropdown
		$recordsPerPageDD = array(10,25,50,100);

		//determine starting rec - keeping this based on 0 to make the math easier, we'll add 1 to the display only
		//page will remain based at 1
		if ($page == '1'){
			$startingRecNumber = 0;
		}else{
			$startingRecNumber = ($page * $recordsPerPage) - $recordsPerPage;
		}


		//get total number of records to print out and calculate page selectors
		$resourceObj = new Resource();
		$totalRecords = $resourceObj->searchCount($whereAdd);

		//reset pagestart to 1 - happens when a new search is run but it kept the old page start
		if ($totalRecords < $startingRecNumber){
			$page = 1;
			$startingRecNumber = 1;
		}

		$limit = $startingRecNumber . ", " . $recordsPerPage;

		$resourceArray = array();
		$resourceArray = $resourceObj->search($whereAdd, $orderBy, $limit);

		if (count($resourceArray) == 0){
			echo "<br /><br /><i>"._("Sorry, no requests fit your query")."</i>";
			$i=0;
		}else{
			//maximum number of pages to display on screen at one time
			$maxDisplay = 25;

			$displayStartingRecNumber = $startingRecNumber + 1;
			$displayEndingRecNumber = $startingRecNumber + $recordsPerPage;

			if ($displayEndingRecNumber > $totalRecords){
				$displayEndingRecNumber = $totalRecords;
			}

			//div for displaying record count
			echo "<div class='header'><h2>";
				echo sprintf(ngettext('Displaying %d to %d of %d Resource Record', 'Displaying %d to %d of %d Resource Records', $totalRecords), $displayStartingRecNumber, $displayEndingRecNumber, $totalRecords);
			echo "</h2><span class='export addElement'><a href='javascript:void(0);'><img src='images/xls.gif' id='export' alt='"._('Export')."'></a></span>";
			echo "</div>";

			//print out page selectors as long as there are more records than the number that should be displayed
			if ($totalRecords > $recordsPerPage){
				echo "<nav class='pagination' aria-label='"._('Records per page')."'><ul>";

				//print starting <<
				if ($page == 1){
					echo "<li class='first'><span class='small'><i class='fa fa-backward'></i></span></li>";
				}else{
					$prevPage = $page - 1;
					echo "<li class='first'><a href='javascript:void(0);' id='" . $prevPage . "' class='setPage smallLink' aria-label='" . sprintf(_('First page, page %d'), $i ? $i : 1) . "'><i class='fa fa-backward'></i></a></li>";
				}


				//now determine the starting page - we will display 3 prior to the currently selected page
				if ($page > 3){
					$startDisplayPage = $page - 3;
				}else{
					$startDisplayPage = 1;
				}

				$maxPages = ($totalRecords / $recordsPerPage) + 1;

				//now determine last page we will go to - can't be more than maxDisplay
				$lastDisplayPage = $startDisplayPage + $maxDisplay;
				if ($lastDisplayPage > $maxPages){
					$lastDisplayPage = ceil($maxPages);
				}

				for ($i=$startDisplayPage; $i<$lastDisplayPage;$i++){

					if ($i == $page){
						echo "<li aria-current='page'><span>" . $i . "</span></li>";
					}else{
						echo "<li><a href='javascript:void(0);' id='" . $i . "' aria-label='" . sprintf(_('Page %d'), $i) . "' class='setPage smallLink'>" . $i . "</a></li>";
					}

				}

				$nextPage = $page + 1;
				//print last >> arrows
				if ($nextPage >= $maxPages){
					echo "<li class='last'><span class='smallerText'><i class='fa fa-forward'></i></span></li>";
				}else{
					echo "<li class='last'><a href='javascript:void(0);' id='" . $nextPage . "' class='setPage smallLink' aria-label='" . sprintf(_('Last page, page %d'), $i - 1) . "'><i class='fa fa-forward'></i></a></li>";
				}

				echo "</ul></nav>";


			}
		?>

			<table id='resource_table' class='dataTable table-border table-striped'>
			<thead>
				<tr>
			<th scope="col"><span class="sortable"><?php echo _("Name");?><span class="arrows"><a href='javascript:setOrder("R.titleText","asc");'><img src='images/arrowup.png' alt="<?php echo _("Sort by name, ascending"); ?>"></a><span class="arrows"><a href='javascript:setOrder("R.titleText","desc");'><img src='images/arrowdown.png' alt="<?php echo _("sort by name, descending"); ?>"></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("ID");?><span class="arrows"><a href='javascript:setOrder("R.resourceID + 0","asc");'><img src='images/arrowup.png' alt="<?php echo _("sort by ID, ascending"); ?>"></a><span class="arrows"><a href='javascript:setOrder("R.resourceID + 0","desc");'><img src='images/arrowdown.png' alt="<?php echo _("sort by ID, descending"); ?>"></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Creator");?><span class="arrows"><a href='javascript:setOrder("CU.loginID","asc");'><img src='images/arrowup.png' alt="<?php echo _("sort by creator, ascending"); ?>"></a><span class="arrows"><a href='javascript:setOrder("CU.loginID","desc");'><img src='images/arrowdown.png' alt="<?php echo _("sort by creator, descending"); ?>"></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Date Created");?><span class="arrows"><a href='javascript:setOrder("R.createDate","asc");'><img src='images/arrowup.png' alt="<?php echo _("sort by date created, ascending"); ?>"></a><a href='javascript:setOrder("R.createDate","desc");'><img src='images/arrowdown.png' alt="<?php echo _("sort by date created, descending"); ?>"></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Acquisition Type");?><span class="arrows"><a href='javascript:setOrder("acquisitionType","asc");'><img src='images/arrowup.png' alt="<?php echo _("sort by acquisition type, ascending"); ?>"></a><a href='javascript:setOrder("acquisitionType","desc");'><img src='images/arrowdown.png' alt="<?php echo _("sort by acquisition type, descending"); ?>"></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Status");?><span class="arrows"><a href='javascript:setOrder("S.shortName","asc");'><img src='images/arrowup.png' alt="<?php echo _("sort by status, ascending"); ?>"></a><span class="arrows"><a href='javascript:setOrder("S.shortName","desc");'><img src='images/arrowdown.png' alt="<?php echo _("sort by status, descending"); ?>"></a></span></span></th>
			</tr>
			</thead>

			<tbody>
			<?php
			
			foreach ($resourceArray as $resource){
				$archived = ($resource['status'] == _('Archived'));
				$class = ($archived) ? "class = 'archived'" : "";
				echo "<tr>";
				echo "<th scope='row'>"
					. "<a {$class} href='resource.php?resourceID=" . $resource['resourceID'] . "' title=\"" . $resource['titleText'] . "\">"
					. $resource['titleText']
					. "</a></th>";

				echo "<td>";
				$isbnOrIssns = $resource['isbnOrIssns'];
				foreach ($isbnOrIssns as $isbnOrIssn) {
					echo $isbnOrIssn . "<br />";
				}
				echo"</td>";

				if ($resource['firstName'] || $resource['lastName']){
					echo "<td>" . $resource['firstName'] . " " . $resource['lastName'] ."</td>";
				}else{
					echo "<td>" . $resource['createLoginID'] . "</td>";
				}
				echo "<td>" . format_date($resource['createDate']) . "</td>";

				echo "<td>" . $resource['acquisitionType'] . "</td>";
				echo "<td>" . $resource['status'] . "</td>";
				echo "</tr>";
			}

			?>

			</tbody></table>
			
			
			<?php
			//print out page selectors
			if ($totalRecords > $recordsPerPage){
				echo "<nav class='pagination' id='pagination-div' aria-label='"._('Records per page')."'><ul>";
				//print starting <<
				if ($pageStart == 1){
					$pagination .= "<li class='first' aria-hidden='true'><span class='smallText'><i class='fa fa-backward'></i></span></li>";
				}else{
					$pagination .= "<li class='first'><a href='javascript:setPageStart(1);' class='smallLink' aria-label='" . sprintf(_('First page, page %d'), $i ? $i : 1) . "'><i class='fa fa-backward'></i></a></li>";
				}


				//now determine the starting page - we will display 3 prior to the currently selected page
				if ($page > 3){
					$startDisplayPage = $page - 3;
				}else{
					$startDisplayPage = 1;
				}

				$maxPages = ($totalRecords / $recordsPerPage) + 1;

				//now determine last page we will go to - can't be more than maxDisplay
				$lastDisplayPage = $startDisplayPage + $maxDisplay;
				if ($lastDisplayPage > $maxPages){
					$lastDisplayPage = ceil($maxPages);
				}

				for ($i=$startDisplayPage; $i<$lastDisplayPage;$i++){

					if ($i == $page){
						echo "<li aria-current='page'><span class='smallText'>" . $i . "</span></li>";
					}else{
						echo "<li><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Page %d'), $i) . "'>" . $i . "</a></li>";
					}

				}

				$nextPage = $page + 1;
				//print last >> arrows
				if ($nextPage >= $maxPages){
					$pagination .= "<li class='last' aria-hidden='true'><span class='smallText'><i class='fa fa-forward'></i></span></li>";
				}else{
					$pagination .= "<li class='last'><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Last page, page %d'), $i - 1) . "'><i class='fa fa-forward'></i></a></li>";
				}

				echo "</ul>";
			}
			?>
			
		<p id="records-per-page">
			<select id='numberRecordsPerPage' name='numberRecordsPerPage'>
				<?php
				foreach ($recordsPerPageDD as $i){
					if ($i == $recordsPerPage){
						echo "<option value='" . $i . "' selected>" . $i . "</option>";
					}else{
						echo "<option value='" . $i . "'>" . $i . "</option>";
					}
				}
				?>
			</select>
			<label for="numberRecordsPerPage"><?php echo _("records per page");?></label>
		</p>

			<?php 
			if ($totalRecords > $recordsPerPage){
				echo "</nav>";
			}1
			?>

			<script>
			    //for performing excel output
			    $("#export").on('click', function () {
			        window.open('export.php');
			        return false;
			    });
			</script>
			<?php
		}

?>
