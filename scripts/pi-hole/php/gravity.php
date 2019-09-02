<?php
/* Pi-hole: A black hole for Internet advertisements
*  (c) 2017 Pi-hole, LLC (https://pi-hole.net)
*  Network-wide ad blocking via your own hardware.
*
*  This file is copyright under the latest version of the EUPL.
*  Please see LICENSE file for your rights under this license. */

require "scripts/pi-hole/php/database.php";

function gravity_last_update($raw = false)
{
	$db = SQLite3_connect(getGravityDBFilename());
	$date_file_created_unix = $db->querySingle("SELECT value FROM info WHERE property = 'updated';");
	if($date_file_created_unix !== false)
	{
		$date_file_created = date_create("@".intval($date_file_created_unix));
		$date_now = date_create("now");
		$gravitydiff = date_diff($date_file_created,$date_now);
		if($raw)
		{
			$output = array(
			"file_exists"=> true,
			"absolute" => $date_file_created_unix,
			"relative" => array(
				"days" =>  $gravitydiff->format("%a"),
				"hours" =>  $gravitydiff->format("%H"),
				"minutes" =>  $gravitydiff->format("%I"),
				)
			);
		}
		else
		{
			if($gravitydiff->d > 1)
			{
				$output = $gravitydiff->format("Blocking list updated %a days, %H:%I ago");
			}
			elseif($gravitydiff->d == 1)
			{
				$output = $gravitydiff->format("Blocking list updated one day, %H:%I ago");
			}
			else
			{
				$output = $gravitydiff->format("Blocking list updated %H:%I ago");
			}
		}
	}
	else
	{
		if($raw)
		{
			$output = array("file_exists" => false);
		}
		else
		{
			$output = "Gravity database not available";
		}
	}
	return $output;
}
?>
