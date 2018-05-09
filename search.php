<?php
###########################################################
/*
GuestBook Script
Copyright (C) 2012 StivaSoft ltd. All rights Reserved.


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/gpl-3.0.html.

For further information visit:
http://www.phpjabbers.com/
info@phpjabbers.com

Version:  1.0
Released: 2012-03-18
*/
###########################################################

error_reporting(0);
include("configg.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MySQL table search</title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<style>
BODY, TD {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
</style>
</head>


<body>

<form id="form1" name="form1" method="post" action="search.php">
<label for="from">From</label>
<input name="from" type="text" id="from" size="10" value="<?php echo $_REQUEST["from"]; ?>" />
<label for="to">to</label>
<input name="to" type="text" id="to" size="10" value="<?php echo $_REQUEST["to"]; ?>"/>
 <label>Name or Email:</label>
<input type="text" name="string" id="string" value="<?php echo stripcslashes($_REQUEST["string"]); ?>" />
<label>City</label>
<select name="location">
<option value="">--</option>
<?php
	$sql = "SELECT * FROM ".$SETTINGS["data_table"]." GROUP BY location ORDER BY location";
	$sql_result = mysql_query ($sql, $connection ) or die ('request "Could not execute SQL query" '.$sql);
	while ($row = mysql_fetch_assoc($sql_result)) {
		echo "<option value='".$row["location"]."'".($row["location"]==$_REQUEST["location"] ? " selected" : "").">".$row["location"]."</option>";
	}
?>
</select>
<input type="submit" name="button" id="button" value="Filter" />
  </label>
  <a href="search.php"> 
  reset</a>
</form>
<br /><br />
<table width="700" border="1" cellspacing="0" cellpadding="4">
  <tr>
    <td width="90" bgcolor="#CCCCCC"><strong>From date</strong></td>
    <td width="95" bgcolor="#CCCCCC"><strong>To date</strong></td>
    <td width="159" bgcolor="#CCCCCC"><strong>Name</strong></td>
    <td width="191" bgcolor="#CCCCCC"><strong>Email</strong></td>
    <td width="113" bgcolor="#CCCCCC"><strong>City</strong></td>
  </tr>
<?php
if ($_REQUEST["string"]<>'') {
	$search_string = " AND (name LIKE '%".mysql_real_escape_string($_REQUEST["string"])."%' OR hazard LIKE '%".mysql_real_escape_string($_REQUEST["string"])."%')";	
}
if ($_REQUEST["location"]<>'') {
	$search_city = " AND location='".mysql_real_escape_string($_REQUEST["location"])."'";	
}

if ($_REQUEST["from"]<>'' and $_REQUEST["to"]<>'') {
	$sql = "SELECT * FROM ".$SETTINGS["data_table"]." WHERE datereceived >= '".mysql_real_escape_string($_REQUEST["from"])."' AND expierydate <= '".mysql_real_escape_string($_REQUEST["to"])."'".$search_string.$search_city;
} else if ($_REQUEST["from"]<>'') {
	$sql = "SELECT * FROM ".$SETTINGS["data_table"]." WHERE datereceived >= '".mysql_real_escape_string($_REQUEST["from"])."'".$search_string.$search_city;
} else if ($_REQUEST["to"]<>'') {
	$sql = "SELECT * FROM ".$SETTINGS["data_table"]." WHERE expierydate <= '".mysql_real_escape_string($_REQUEST["to"])."'".$search_string.$search_city;
} else {
	$sql = "SELECT * FROM ".$SETTINGS["data_table"]." WHERE id>0".$search_string.$search_city;
}

$sql_result = mysql_query ($sql, $connection ) or die ('request "Could not execute SQL query" '.$sql);
if (mysql_num_rows($sql_result)>0) {
	while ($row = mysql_fetch_assoc($sql_result)) {
?>
  <tr>
    <td><?php echo $row["datereceived"]; ?></td>
    <td><?php echo $row["expierydate"]; ?></td>
    <td><?php echo $row["name"]; ?></td>
    <td><?php echo $row["hazard"]; ?></td>
    <td><?php echo $row["location"]; ?></td>
  </tr>
<?php
	}
} else {
?>
<tr><td colspan="5">No results found.</td>
<?php	
}
?>
</table>


<script>
	$(function() {
		var dates = $( "#from, #to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 2,
			dateFormat: 'yy-mm-dd',
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	</script>

</body>
</html>