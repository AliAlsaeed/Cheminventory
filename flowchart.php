<?php
require 'config.php';
require 'configg.php';
require 'inc/session.php';
require 'inc/items_core.php';
require 'inc/logs_core.php';
require 'inc/users_core.php';
require 'inc/items_core.php';
require 'inc/categories_core.php';
require 'exportdata/db_connect.php';
require 'exportdata/export_data.php';
require 'exportdata/header.php'; 
if($_session->isLogged() == false)
	header('Location: index.php');
$_items->set_session_obj($_session);

$_page = 3;

$role = $_session->get_user_role();

if(isset($_POST['act'])) {
	// Search count
	if($_POST['act'] == '1') {
		if(!isset($_POST['val']) || $_POST['val'] == '')
			die('wrong');
		$search_string = $_POST['val'];
		if($_items->count_items_search($search_string) == 0)
			die('2');
		die('3');
	}
	
	// Delete item
	if($_POST['act'] == '2') {
		if(!isset($_POST['id']) || $_POST['id'] == '')
			die('wrong');
		
		if($role == 3 || $role == 4)
			die('wrong');
		
		if($_items->delete_item($_POST['id']) == true)
			die('1');
		die('wrong');
	}
	
	// Update item quantity (check-in/check-out)
	if($_POST['act'] == '3' || $_POST['act'] == '4') {
		if(!isset($_POST['id']) || !isset($_POST['val']) || !isset($_POST['fromval']) || $_POST['id'] == '' || $_POST['val'] == '' || $_POST['fromval'] == '')
			die('wrong');
		if($_POST['act'] == '3')
			$type = 1;
		elseif($_POST['act'] == '4') {
			$type = 2;
			$location = $_items->get_item($_POST['id']);
			$location= $qty[0]->location;
			if($qty < $_POST['val'])
				die('2');
		}
		
		if($_items->update_item_qty($type, $_POST['id'], $_POST['fromval'], $_POST['val']) == true)
			die('1');
		die('wrong');
	}
	
	// Delete Items
	if($_POST['act'] == '5') {
		if(!isset($_POST['data']) || $_POST['data'] == '')
			die('wrong');
		
		if($role == 3 || $role == 4)
			die('wrong');
		
		$decoded = json_decode($_POST['data']);
		$deleted = array();
		foreach($decoded as $id) {
			if($_items->delete_item($id) == true)
				$deleted[] = $id;
		}
		$reencoded = json_encode($deleted);
		if(count($reencoded) == 0)
			die('wrong');
		die($reencoded);
	}
	
	die();
    

}

if(!isset($_GET['page']) || $_GET['page'] == 0 || !is_numeric($_GET['page']))
	$page = 1;
else
	$page = $_GET['page'];

	
if(!isset($_GET['pp']) || !is_numeric($_GET['pp'])) {
	$pp = 25;
}else{
	$pp = $_GET['pp'];
	if($pp != 25 && $pp != 50 && $pp != 100 && $pp != 150 && $pp != 200 && $pp != 300 && $pp != 500)
		$pp = 25;
}

// Search query
if(isset($_GET['search']) && ($_GET['search'] != '')){
	$s = urldecode($_GET['search']);
	$items = $_items->search($s, $page, $pp);
	$c_items = $_items->count_items_search($s);
} else if ($_GET['location'] || $_GET['owner']|| $_GET['hazard']) {
    $items = $_items->filter($_GET['location'], $_GET['owner'], $_GET['hazard'], $page, $pp);
    $c_items = $_items->count_items_filter($_GET['location'], $_GET['owner'], $_GET['hazard']);
} else{
	$s = false;
	$items = $_items->get_items($page, $pp);
	$c_items = $_items->count_items();
}
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
			<h2>Thompson Rivers University: Chemical Categorization and Storage Flowchart</h2>
			<div id="table-head">

				<img src="media/img/loader-small.gif" class="fleft loader" width="15" height="15" />
				<div class="fright">



				</div>

				<div class="fright" style="height:5px; margin-right:55px;"></div>
				<?php
				if($role == 1 || $role == 2)

				?>
            
                
                
                

			</div>
            
            
                        <embed src="http://www.cheminventory.ca/clowcart.pdf" width="900" height="600" type='application/pdf'>

            
            </div>
		
			
		</div>

		<div class="clear" style="margin-bottom:40px;"></div>
		<div class="border" style="margin-bottom:30px;"></div>
        
        
	
        
	</div>

</head>
<body>




    
</body>
</html>