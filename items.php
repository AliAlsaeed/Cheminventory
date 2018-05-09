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
			<h2>TRU Chemical Database </h2>
			<div id="table-head">
				<form method="get" action="">
					<input type="text" name="search" placeholder="Search..." class="search fleft" <?php if($s!=false) echo 'value="'.$s.'"'; ?>/>
                </form>
				<img src="media/img/loader-small.gif" class="fleft loader" width="15" height="15" />
				<div class="fright">
					<div class="select-holder">
						<i ></i>
						<select name="show-per-page">
							<option value="25" <?php if($pp==25) echo 'selected'; ?>>25</option>
							<option value="50" <?php if($pp==50) echo 'selected'; ?>>50</option>
							<option value="100" <?php if($pp==100) echo 'selected'; ?>>100</option>
							<option value="150" <?php if($pp==150) echo 'selected'; ?>>150</option>
							<option value="200" <?php if($pp==200) echo 'selected'; ?>>200</option>
							<option value="300" <?php if($pp==300) echo 'selected'; ?>>300</option>
							<option value="500" <?php if($pp==500) echo 'selected'; ?>>500</option>
						</select>
					</div>


				</div>

				<div class="fright" style="height:5px; margin-right:55px;"></div>
				<?php
				if($role == 1 || $role == 2)
                     '<div class="well-sm col-sm-12">';

				?>
            
                
                
                

			</div>
            
            <div style="overflow: hidden; "> <form method="get" action="">
                        <select style="position: relative; float: left " name="location" class="select-holder">
                            <i class="fa fa-caret-down"></i>
                    <option value="" style="padding: 10px;"> Filter By Location </option>
                    <?php
                        $sql = "SELECT * FROM ".$SETTINGS["data_table"]." GROUP BY location ORDER BY location";
                        $sql_result = mysql_query ($sql, $connection ) or die ('request "Could not execute SQL query" '.$sql);
                        while ($row = mysql_fetch_assoc($sql_result)) {
                            echo "<option value='".$row["location"]."'".($row["location"]==$_REQUEST["location"] ? " selected" : "").">".$row["location"]."</option>";
                        }
                    ?>
                    </select>  
                <select style="position: relative; float: left " name="hazard" class="select-holder">
                            <i class="fa fa-caret-down"></i>
                    <option value=""> Filter By Hazard </option>
                    <?php
                        $sql = "SELECT * FROM ".$SETTINGS["data_table"]." GROUP BY hazard ORDER BY hazard";
                        $sql_result = mysql_query ($sql, $connection ) or die ('request "Could not execute SQL query" '.$sql);
                        while ($row = mysql_fetch_assoc($sql_result)) {
                            echo "<option value='".$row["hazard"]."'".($row["hazard"]==$_REQUEST["hazard"] ? " selected" : "").">".$row["hazard"]."</option>";
                        }
                    ?>
                    </select>
                    <select style="position: relative; float: left" name="owner">
                    <option value="">Filter By Owner</option>
                    <?php
                        $sql = "SELECT invento_users.id as id, invento_users.name as name FROM ".$SETTINGS["data_table"]." JOIN invento_users ON invento_items.owner=invento_users.id GROUP BY invento_users.name ORDER BY invento_users.name";
                        $sql_result = mysql_query ($sql, $connection ) or die ('request "Could not execute SQL query" '.$sql);
                        while ($row = mysql_fetch_assoc($sql_result)) {
                            echo "<option value='".$row["id"]."'".($row["id"]==$_REQUEST["owner"] ? " selected" : "").">".$row["name"]."</option>";
                        }
                    ?>
                    </select>
                <div >

                    <button style="float: left; width: 150px; margin: 2px; height: 28px; font-size:12px; text-align:center; color: white; border-radius: 4px; background-color: #003647;" type="Submit">Filter</button>
                    <form>
<input style="float: left; height: 28px; width: 150px; font-size:12px; margin: 2px; background-color: #65a1b2;
    color: white;
    padding: 5.5px 10px;
    margin: 0px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;" type="button" value="Reset" onclick="window.location.href='http://www.cheminventory.ca/items.php'" />
</form>
      </div>
</form>
                
                </div>

            
           
            
            
                           	<div class="well-sm col-sm-12">
		<div class="btn-group pull-right">	
			<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">					
				<button type="submit" id="export_data" name='export_data' value="Export to excel" class="btn btn-info">Export to Excel</button>
			</form>
		</div>
	</div>	
            
            
            <style>
            @media (max-width: 40em) {
#export_data {
display: none;
}
                }
            </style>    
            
            
            
            
			
			<?php
			if($c_items == 0)
				echo '<br /><br />No items';
			else{
			?>
            <div style="overflow-x:auto; padding :10px;">
            
                    
                    
                <table border="3" rules="rows" id="items">
				<thead>
                    
					<tr>
						<td width="5%"> </td>
                        
						<td width="6%">ID </td>
						<td width="30%">Name</td>
						<td width="20%">MF</td>
						<td width="10%">CAS</td>
						<td width="8%">Tare weight</td>
						<td width="15%">Hazard Class</td>
						<td width="15%">Location</td>
						<td width="10%">Owner</td>
						<td width="1%"> </td>
					</tr>
				</thead>
				
				<tbody>
                    
                    
                    
<?php
    function switchColor($rowValue) { 

//Define the colors first 
$a = '#0095D0'; 
$b = '#E60E16'; 
$c = '#FFD500'; 
$d = '#07C8BE'; 
$f = '#A5CA03'; 
$g = '#009846'; 
$j = '#9a6d01'; 
$k = '#F59300'; 
$l = '#EB7FAF'; 
$al = '#0000ff'; 
$oa = '#8B42AB'; 
$s = '#F7F8F9'; 
$e = '#B87BD5'; 
$x = '#808080'; 

switch ($rowValue) { 
    case 'A': 
        echo $a; 
        break; 
    case 'B': 
        echo $b; 
        break;   
    case 'C': 
        echo $c; 
        break; 
    case 'D': 
        echo $d; 
        break;   
    case 'F': 
        echo $f; 
        break; 
    case 'G': 
        echo $g; 
        break;   
    case 'J': 
        echo $j; 
        break; 
    case 'K': 
        echo $k;
        break;   
    case 'L':
        echo $l;
        break;
    case 'A/L': 
        echo $al;
        break;
    case 'OA': 
        echo $oa;
        break;
    case 'S':       
        echo $s;
        break;
    case 'E':      
        echo $e; 
        break;
    case 'X': 
        echo $x; 
        break; 
    default: 
        echo $color3; 
       } 
      } 
					if($_items->is_mysqlnd() || $s == false) {
						while($item = $items->fetch_object()) {
?>
					<tr data-type="element" data-id="<?php echo $item->id; ?>">
						<td><input type="checkbox" name="chbox" value="<?php echo $item->id; ?>" /></td>
						<td class="hover" data-type="id"><?php echo "CHEM-".$item->id; ?></td>
						<td class="hover" data-type="name" ><?php echo $_items->parse_price($item->name); ?></td>
						<td class="hover" data-type="molecularformula"><?php echo $item->molecularformula; ?></td>
						<td class="hover" data-type="cas"><?php echo $item->cas; ?></td>
						<td class="hover" data-type="tarewight"><?php echo  $item->tarewight ." g"; ?></td>
                        
                        <td style="background-color: <?php echo switchColor(strip_tags($item->hazard)); ?>"> <?php echo $item->hazard; ?></td>
                        
						<td class="hover" data-type="location"><?php echo $item->location; ?></td>
                        <td class="hover" data-type="usernames"><?php $cat = $_cats->getusernames($item->owner);
                        echo $cat->name; ?></td>
                        						<td>

							<?php
							if($role == 1 || $role == 2)
								echo '<a href="edit-item.php?id='.$item->id.'" name="c3" title="Edit Item"><i class="fa fa-pencil"></i></a>';
							if($role == 1 || $role == 2 || $role == 3)
								echo '<a href="logs.php?itemid='.$item->id.'" name="c4" title="Log"><i styel = "  background-color: #f2f2f2" class="fa fa-file-text-o"></i></a>';
							if($role == 1 || $role == 2)
								echo '<a href="barcode.php?id='.$item->id.'" name="c4" title="Barcode"><i styel = "  background-color: #f2f2f2" class="fa fa-barcode"></i></a>';

							if($role == 1 || $role == 2)
                                
                               
								echo '<a href="" name="c5" title="Delete Item"><i class="fa fa-close"></i></a>';
							?>
						</td>

					</tr>
                    
                    
<?php
						}
					}else{
						foreach($items as $item) {
?>
					<tr data-type="element" data-id="<?php echo $item->id; ?>">
						<td><input type="checkbox" name="chbox" value="<?php echo $item->id; ?>" /></td>
						<td class="hover" data-type="id"><?php echo $item->id; ?></td>
						<td class="hover" data-type="name"><?php echo $item->name; ?></td>
						<td class="hover" data-type="cat"><?php echo $_items->get_category_name($item->category); ?></td>
						<td><?php echo $item->qty; ?></td>
						<td>
							<a href="" name="c1" title="Check-In"><i class="fa fa-arrow-down"></i></a>
							<a href="" name="c2" title="Check-Out"><i class="fa fa-arrow-up"></i></a>
							<?php
							if($role == 1 || $role == 2)
								echo '<a href="edit-item.php?id='.$item->id.'" name="c3" title="Edit Item"><i class="fa fa-pencil"></i></a>';
							if($role == 1 || $role == 2 || $role == 3)
								echo '<a href="logs.php?itemid='.$item->id.'" name="c4" title="Log"><i class="fa fa-file-text-o"></i></a>';
							if($role == 1 || $role == 2)
								echo '<a href="" name="c5" title="Delete Item"><i class="fa fa-close"></i></a>';
							?>
						</td>
					</tr>
<?php
						}
					}
?>
				</tbody>
			</table>
			
<style>
table {

    border: 2px solid #ddd;
    
}

th, td {
white-space: nowrap;
  overflow: hidden;
    text-align: center;
}

tr:nth-child(even) {
    background-color: #f2f2f2
}
    
#location{
 width:150px;   
}

#location option{
 width:100px;   
}
</style>
            
            
            </div>
			<?php } ?>
			
		</div>
		
		<div id="pagination">
			<?php
			if($page != 1)
				echo '<div class="prev" name="'.($page-1).'"><i class="fa fa-caret-left"></i></div>';
			?>
			<div class="page"><?php echo $page; ?></div>
			<?php
			$total_items = $c_items;
			if($total_items > $pp) {
				$total_pages = $total_items / $pp;
				if($total_pages > $page)
					echo '<div class="next" name="'.($page+1).'"><i class="fa fa-caret-right"></i></div>';
			}
			?>
            
		</div>
		
		<div class="clear" style="margin-bottom:40px;"></div>
		<div class="border" style="margin-bottom:30px;"></div>
        
        
	
        
	</div>

</head>
<body>




    
</body>
</html>