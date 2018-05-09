<?php
require 'config.php';
require 'inc/session.php';
require 'inc/items_core.php';
require 'inc/categories_core.php';
if($_session->isLogged() == false)
	header('Location: index.php');
$_items->set_session_obj($_session);

$_page = 13;

$role = $_session->get_user_role();
if($role != 1 && $role != 2)
	header('Location: items.php');

if(isset($_POST['act'])) {
    if($_POST['act'] == '1') {
		if(!isset($_POST['itemid']) || !isset($_POST['name']) || !isset($_POST['descrp'])|| !isset($_POST['owner'])|| !isset($_POST['hazard'])|| !isset($_POST['molecularformula']) || !isset($_POST['cas']) || !isset($_POST['cat']))
			die('wrong 1 ');
		if($_POST['itemid'] == '' || $_POST['name'] == '')
			die('wrong 2 ');
        

		
		$itemid = $_POST['itemid'];
		$name = $_POST['name'];
		$descrp = $_POST['descrp'];
		$cat = $_POST['cat'];
        $owner = $_POST['owner'];
        $hazard = $_POST['hazard'];
        $molecularformula = $_POST['molecularformula'];
        $cas = $_POST['cas'];




		
		// Fix price

		
		if($_items->update_item($itemid, $name, $descrp, $cat, $molecularformula, $hazard, $owner,$cas) == false)
			die('wrong ');
		die('1');
	}
}

if(!isset($_GET['id']))
	header('Location: items.php');
$itemid = $_GET['id'];

if($_items->get_item_name($itemid) == '')
	header('Location: items.php');

$item = $_items->get_item($itemid);
?>	
<!DOCTYPE HTML>
<html>
<head>
<?php require 'inc/head.php'; ?>
</head>
<body>
	<div id="main-wrapper">
		<?php require 'inc/header.php'; ?>
		
		<div class="wrapper-pad">
			<h2>Edit Chemical <?php echo "CHEM-". $itemid; ?></h2>
			<div class="center">
				<div class="new-item form">
					<form method="post" action="" name="edit-item" data-id="<?php echo $itemid; ?>">
						Chemical Name:<br />
						<div class="ni-cont">
							<input type="text" name="item-name" class="ni" value="<?php echo $item->name; ?>" />
						</div>				
                        Molecularformula:<br />
						<div class="ni-cont">
							<input type="text" name="item-molecularformula" class="ni" value="<?php echo $item->molecularformula; ?>" />
						</div>                    
                        CAS:<br />
						<div class="ni-cont">
							<input type="text" name="item-cas" class="ni" value="<?php echo $item->cas; ?>" />
						</div>                                   
                        Hazard Class:<br />
						<div class="ni-cont">
							<input type="text" name="item-hazard" class="ni" value="<?php echo $item->hazard; ?>" />
						</div>
						<?php
						$desc = 400 - strlen($item->descrp);
						?>
						<span class="item-desc-left">Description (<?php echo $desc; ?> characters left):</span><br />
						<div class="ni-cont">
							<textarea name="item-descrp" class="ni"><?php echo $item->descrp; ?></textarea>
						</div>
						Category:<br />
						<div class="select-holder">
							<i class="fa fa-caret-down"></i>
							<?php
							if($_cats->count_cats() == 0)
								echo '<select name="item-category" disabled><option val="no">You need to create a category first</option></select>';
							else{
								echo '<select name="item-category">';
								$cats = $_cats->get_cats_dropdown();
								while($catt = $cats->fetch_object()) {
									echo "<option value=\"{$catt->id}\">{$catt->name}</option>";
								}
								echo '</select>';
							}
							?>
						</div>
                                                Owner:<br />
						<div class="select-holder">
							<i class="fa fa-caret-down"></i>
							<?php
							if($_cats->count_cats() == 0)
								echo '<select name="item-owner" disabled><option value="no">You need to create a category first</option></select>';
							else{
								echo '<select name="item-owner">';
								$cats = $_cats->get_usernames();
								while($catt = $cats->fetch_object()) {
									echo "<option value=\"{$catt->id}\">{$catt->name}</option>";
								}
								echo '</select>';
							}
							?>
						</div>
						<input type="submit" name="item-submit" class="ni btn blue" value="Save changes" />
					</form>
				</div>
			</div>
		</div>
		
		<div class="clear" style="margin-bottom:40px;"></div>
		<div class="border" style="margin-bottom:30px;"></div>
	</div>
</body>
</html>