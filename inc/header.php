<?php
$headrole = $_session->get_user_role();
if($headrole == 1)
	$as = 'Administrator';
elseif($headrole == 2)
	$as = 'Department Administrator';
elseif($headrole == 3)
	$as = 'Researcher';
elseif($headrole == 4)
	$as = 'Student';
?>
<div id="header">
    	<link type="text/css" rel="stylesheet" href="media/css/bootstrap.min.css" media="all" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

			<div class="left">
				<a href="items.php"><img src="media/img/logo3x.png" width="150" height="50" alt="TRU Chemical Database" /></a>
				<div style="font-size:12px; font-style:italic;color:#bbb;"><?php echo $as; ?></div>
			</div>
			<div class="right">
				<?php
				if($headrole == 1 || $headrole == 2 || $headrole == 3)
					echo '<a href="users.php" title="Users">Users</a>|';
				?>
				<a href="settings.php" title="Settings">Settings</a>|
				<a href="logout.php" title="Logout">Logout</a>
			</div>
			<div class="clear"></div>
		</div>
		
		<input type="checkbox" class="toggle" id="opmenu" style="display:none"/>
		<label for="opmenu" id="open-menu"><i class="fa fa-align-justify"></i> Menu</label>
		<div id="menu">
			<ul id="menuli">
				<?php
				// Home only for Admin and General Supervisor (Stats)
				if($headrole == 1 || $headrole == 2) {
				?>
					<li<?php if($_page == 1) { ?> class="active"<?php } ?>><a href="items.php" title="Home"><i class="fa fa-home"></i> Home</a></li>
				<?php
				}
				?>
				
				<?php
				// Add Item only for Admin, General Supervisor and Supervisor
				if($headrole == 1 || $headrole == 2 || $headrole == 3){
				?>
					<li<?php if($_page == 2) { ?> class="active"<?php } ?>><a href="new-item.php" title="New Item"><i class="fa fa-plus"></i> New Item</a></li>
				<?php
				}
				?>
				
				<li<?php if($_page == 3) { ?> class="active"<?php } ?>><a href="items.php" title="Items"><i class="fa fa-flask"></i> Inventory</a></li>
				<li<?php if($_page == 4) { ?> class="active"<?php } ?>><a href="check-in.php" title="Check-In Item"><i class="fa fa-arrow-down"></i> Check-In Chemical</a></li>
				<li<?php if($_page == 5) { ?> class="active"<?php } ?>><a href="check-out.php" title="Check-Out Item"><i class="fa fa-arrow-up"></i> Check-Out/Return</a></li>
				
				<?php
				// Add Item only for Admin, General Supervisor and Supervisor
				if($headrole == 1 || $headrole == 2 || $headrole == 3){
				?>
					<li<?php if($_page == 6) { ?> class="active"<?php } ?>><a href="logs.php" title="Logs"><i class="fa fa-file-text"></i> Logs</a></li>
				<?php
				}
				?>
				<li<?php if($_page == 7) { ?> class="active"<?php } ?>><a href="categories.php" title="Categories"><i class="fa fa-folder"></i> Departments </a></li>			
                <li><a href="flowchart.php" title="Sheet"><i class="fa fa-asterisk"></i> Flow Chart </a></li>          
                <li><a href="Documentation.html" title="Documentation"><i class="fa fa-folder-open-o"></i> Documentation </a></li>
			</ul>
		</div>