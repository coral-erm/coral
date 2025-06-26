<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 2.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as
**   published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
**   of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

?>

<footer class="footer">
    <?php
			$copyright = _("Copyright");
			$year = date('Y');
			$versionString = _("CORAL version");
      $versionNumber = "2025.04.04";
		?>
		<p><?php echo "{$copyright} &copy; {$year}. {$versionString} {$versionNumber}"; ?></p>
  <p>
    <a href="https://coral-erm.org/" <?php echo getTarget(); ?> class="site-title-link logo"><?php echo _('CORAL eRM project website'); ?></a>
    <a href="https://github.com/coral-erm/coral/issues" <?php echo getTarget(); ?> id="report-issue"><?php echo _("Report an Issue");?></a>
  </p>
</footer>

<script>
  <?php $ilsValue = ($config->ils->ilsConnector) ?? 0; ?>
  const CORAL_ILS_LINK=<?php echo $ilsValue; ?>;
  Date.format = '<?php echo return_datepicker_date_format(); ?>';
  const CORAL_NUMBER_LOCALE='<?php echo str_replace('_', '-', return_number_locale()); ?>';
  const CORAL_NUMBER_DECIMALS='<?php echo return_number_decimals(); ?>';
</script>

<?php
// Additional scripts and closing body/html tags in parent HTML file
?>