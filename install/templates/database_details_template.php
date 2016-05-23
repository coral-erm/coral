<?php

function database_details_template($shared_database_info)
{
	$dbusername = _("Database Username");
	$dbpassword = _("Database Password");
	$dbhost     = _("Database Host");
	$username   = isset($_SESSION["POSTDATA"]["dbusername"]) ? $_SESSION["POSTDATA"]["dbusername"] : _("Username");
	$password   = isset($_SESSION["POSTDATA"]["dbpassword"]) ? _("leave blank to leave unchanged") : _("Password");
	$host       = isset($_SESSION["POSTDATA"]["dbhost"]) ? $_SESSION["POSTDATA"]["dbhost"] : _("Hostname");
	$submit     = _("Start Installing");

	$leave_blank_instruction = _("Leave fields blank if you do not intend to install respective modules.");

	$cards = function($shared_database_info) {
		return join(array_reduce($shared_database_info, function($carry, $item){
			$carry[] = <<<HEREDOC
			<div class="card-half">
				<label for="dbauth">{$item["title"]}</label>
				<input class="u-full-width" type="text" placeholder="{$item["default_value"]}" value="{$item["default_value"]}" name="dbauth">
			</div>
HEREDOC;
			return $carry;
		}));
	};

	return <<<HEREDOC
<form class="pure-form pure-form-aligned">
	<fieldset>
		<div class="row">
			<div class="six columns">
				<label for="dbusername">$dbusername</label>
				<input class="u-full-width" type="text" placeholder="$username" name="dbusername">
			</div>

			<div class="six columns">
				<label for="dbpassword">$dbpassword</label>
				<input class="u-full-width" type="password" placeholder="$password" name="dbpassword">
			</div>
		</div>
		<div class="row">
			<div class="twelve columns">
				<label for="dbhost">$dbhost</label>
				<input class="u-full-width" type="text" placeholder="$host" name="dbhost">
			</div>
		</div>

		<div class="twelve columns">
			<a href="#" class="toggleSection" data-alternate-message="hide advanced" data-toggle-section=".advancedSection">show advanced</a>
		</div>
		<div class="advancedSection" style="display: none;">
			<div class="row">
				$leave_blank_instruction
			</div>
			<div class="row" id="scoped-content">
				<style type="text/css" scoped>
					.card-half {
						width: 48%;
						float: left;
					}
					.card-half:nth-child(2n) {
						margin-right: 2%;
					}
					.card-half:nth-child(2n+1) {
						margin-left: 2%;
					}
				</style>

				{$cards($shared_database_info)}

			</div>
		</div>
		<div class="row">
			<input type="submit" value="$submit" />
		</div>
	</fieldset>
</form>
HEREDOC;
}