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

			//This is an interim solution until this page can be refactored in the v2026.04 release.
			function buildPageNav($page, $recordsPerPage, $totalRecords){
				//maximum number of pages to display on screen at one time
				$maxDisplay = 25;
				echo "<nav class='pagination' aria-label='"._('Records per page')."'><ul>";

				//print starting <<
				if ($page == 1){
					echo "<li class='first'><span class='small'><i class='fa fa-backward'></i></span></li>";
				}else{
					$prevPage = $page - 1;
					echo "<li class='first'><a href='javascript:void(0);' id='{$prevPage}' class='setPage smallLink' aria-label='" . sprintf(_('First page, page %d'), $prevPage ? $prevPage : 1) . "'><i class='fa fa-backward'></i></a></li>";
				}


				//now determine the starting page - we will display 3 prior to the currently selected page
				$startDisplayPage = ($page > 3) ? $page - 3 : 1;
				$maxPages = ($totalRecords / $recordsPerPage) + 1;

				//now determine last page we will go to - can't be more than maxDisplay
				$lastDisplayPage = $startDisplayPage + $maxDisplay;
				if ($lastDisplayPage > $maxPages){
					$lastDisplayPage = ceil($maxPages);
				}

				for ($i=$startDisplayPage; $i<$lastDisplayPage;$i++){
					$pageLabel = sprintf(_('Page %d'), $i);
					$currentPage = ($i == $page);
					$ariaCurrent = ($currentPage) ? "aria-current='page'" : "";
					$currentPageLink = "<span>{$i}</span>";
					$otherPageLink = "<a href='javascript:void(0);' id='{$i}' aria-label='{$pageLabel}' class='setPage smallLink'>{$i}</a>";
					$linkHTML = ($currentPage) ? $currentPageLink : $otherPageLink;
					echo "<li {$ariaCurrent}>{$linkHTML}</li>";
				}

				$nextPage = $page + 1;
				//print last >> arrows
				if ($nextPage >= $maxPages){
					echo "<li class='last'><span class='smallerText'><i class='fa fa-forward'></i></span></li>";
				}else{
					echo "<li class='last'><a href='javascript:void(0);' id='{$nextPage}' class='setPage smallLink' aria-label='" . sprintf(_('Last page, page %d'), $i - 1) . "'><i class='fa fa-forward'></i></a></li>";
				}

				echo "</ul></nav>";
			}

			//print out page selectors as long as there are more records than the number that should be displayed
			if ($totalRecords > $recordsPerPage){
				buildPageNav($page, $recordsPerPage, $totalRecords);
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
				if ($totalRecords > $recordsPerPage){
					buildPageNav($page, $recordsPerPage, $totalRecords);
				} 
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
