<?php
require 'config.php';
require 'inc/session.php';
require 'inc/items_core.php';
require 'inc/categories_core.php';
if($_session->isLogged() == false)
	header('Location: index.php');

$_page = 2;

$role = $_session->get_user_role();
if($role==4)
	header('Location: items.php');

if(isset($_POST['act'])) {
	if($_POST['act'] == '1') {
		if(!isset($_POST['name']) || !isset($_POST['descrp']) || !isset($_POST['cat']) || !isset($_POST['qty']) || !isset($_POST['molecularformula'])|| !isset($_POST['cas'])|| !isset($_POST['supplier']) || !isset($_POST['datereceived'])|| !isset($_POST['expierydate'])|| !isset($_POST['tarewight']) || !isset($_POST['location'])|| !isset($_POST['locationdetail'])|| !isset($_POST['chemicalstate'])|| !isset($_POST['hazard'])|| !isset($_POST['owner'])|| !isset($_POST['safety']))
			die('wrong');
		
		$name = $_POST['name'];
		$descrp = $_POST['descrp'];
		$cat = $_POST['cat'];
		$qty = $_POST['qty'];
		$molecularformula = $_POST['molecularformula'];
		$cas = $_POST['cas'];
		$supplier = $_POST['supplier'];
		$datereceived = $_POST['datereceived'];
		$expierydate = $_POST['expierydate'];
		$tarewight = $_POST['tarewight'];
		$location = $_POST['location'];
		$locationdetail = $_POST['locationdetail'];
		$chemicalstate = $_POST['chemicalstate'];
		$hazard = $_POST['hazard'];
		$owner= $_POST['owner'];
		$safety = $_POST['safety'];
        $density = $_POST['density'];
		
		// Fix price
		
	//public function new_item($name, $desc, $cat, $qty, $molecularformula,$cas,$supplier,$datereceived,$expierydate,$tarewight,$location,$locationdetail,$chemicalstate,$hazard) {

		if($_items->new_item($name, $descrp, $cat, $qty, $molecularformula,$cas,$supplier,$datereceived,$expierydate,$tarewight,$location,$locationdetail,$chemicalstate,$hazard,$owner,$safety, $density) == false)
			die('wrong');
		die('1');
	}
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
			<h2>New Chemical</h2>
			<div class="center">
				<div class="new-item form">
					<form method="post" action="" name="new-item">
						Chemical Name:<br />
						<div class="ni-cont">
							<input type="text" name="item-name" class="ni" />
						</div>
                        Molecular Formula:<br />
						<div class="ni-cont">
							<input type="text" name="molecularformula" class="ni" />
						</div>
                        CAS:<br />
						<div class="ni-cont">
							<input type="text" name="cas" class="ni" />
						</div>          
                        Supplier:<br />
						<div class="ni-cont">
							<input type="text" name="supplier" class="ni" />
						</div> 
                        Date Received:
						<div class="ni-cont">
							<input type="date" name="datereceived" class="ni" />
						</div>       
                        Expiry date:
						<div class="ni-cont">
							<input type="date" name="expierydate" class="ni" />
						</div>
                Hazard Classification:<br />
                            <div class="select-holder">
                            <i class="fa fa-caret-down"></i>
                <select class="select-holder" name="hazard" onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor">
                    <i class="fa fa-caret-down"></i>
                    <option value="volvo" disabled>Choose here </option>
                        <option value = "A" style="background:#0095D0">A</option>
                        <option value = "B" style="background:#E60E16;">B</option>
                        <option value = "C" style="background:#FFD500">C</option>
                        <option value = "D" style="background:#07C8BE">D</option>             <option value = "E" style="background:#B87BD5">E</option>
                        <option value = "F" style="background:#A5CA03">F</option>
                        <option value = "G" style="background:#009846">G</option>
                        <option value = "J" style="background:#9a6d01">J</option>      
                        <option value = "K" style="background:#F59300">K</option>
                        <option value = "L" style="background:#EB7FAF">L</option>             <option value = "X" style="background:#808080">X</option>
                        <option value = "OA" style="background:#8B42AB">OA</option>
                        <option value = "S" style="background:#F7F8F9">S</option>
                        </select>
                        </div>
                        Location:<br />
						<div class="ni-cont">
							<input type="text" name="location" class="ni" required/>
						</div>      
                        Location Details:<br />
						<div class="ni-cont">
							<input type="text" name="locationdetail" class="ni" />
						</div>        
                        Chemical State:<br />
						<div class="select-holder">
                            <i class="fa fa-caret-down"></i>
                            <select id="chemical-state-select" class="select-holder" name="chemicalstate">
                               <option value="volvo" disabled>Chemical State</option>
                                <option value="solid">Solid</option>
                                <option value="liquid">Liquid</option>
                                <option value="gas">Gas</option>
                            </select>
                            <div id="solid-inputs">
                                <p>Tare Mass (g)<br /> <input id="solid-a"></p>
                                <p>Original Mass of Product (g)<br /> <input name="item-qty" id="solid-b"></p>
                            </div>
                            <div id="liquid-inputs">
                                <p>Tare Mass (g) <br /><input id="liquid-a"></p>
                                <p>Original Volume of Product (mL) <br /> <input name="item-qty" id="liquid-b"></p>
                                <p>Density (g/mL) <br /> <input id="liquid-c"></p>
                            </div>
                            <div id="gas-inputs">
                                <p>Tare Mass (g): <br /><input name="item-qty" id="gas-a"></p>
                            </div>
						</div>  
						<span class="item-desc-left">Description (400 characters):</span><br />
						<div class="ni-cont">
							<textarea name="item-descrp" class="ni"></textarea>
						</div>					
                        <span >Post Safety Sheet URL</span><br />
						<div>
							<textarea name="item-safety" class="ni"></textarea>
						</div>
						Department:<br />
						<div class="select-holder">
							<i class="fa fa-caret-down"></i>
							<?php
							if($_cats->count_cats() == 0)
								echo '<select name="item-category" disabled><option value="no">You need to create a category first</option></select>';
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
						<input type="submit" name="item-submit" class="ni btn blue" value="Create Chemical" />
					</form>
				</div>
			</div>
		</div>
		
		<div class="clear" style="margin-bottom:40px;"></div>
		<div class="border" style="margin-bottom:30px;"></div>
	</div>
    <script>
        // Chemical state fields
        var chemState = document.getElementById('chemical-state-select');
        var tareInput = document.getElementById('tare-weight-input');
        
        // Solid state fields
        var solidInputs = document.getElementById('solid-inputs');
        
        // Liquid state fields
        var liquidInputs = document.getElementById('liquid-inputs');
        
        // Gas state fields
        var gasInputs = document.getElementById('gas-inputs');
        
        chemState.addEventListener('change', function(event) {
            changeState(event.target);
        });
        changeState(chemState);
        
        function hideInputs(state) {
            solidInputs.style.display = state === 'solid' ? 'block' : 'none';
            liquidInputs.style.display = state === 'liquid' ? 'block' : 'none';
            gasInputs.style.display = state === 'gas' ? 'block' : 'none';
        }
        
        function changeState(select) {
            hideInputs(select.value);
        }
    </script>
</body>
</html>