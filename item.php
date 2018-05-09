<?php
require 'config.php';
require 'inc/session.php';
require 'inc/items_core.php';
require 'inc/categories_core.php';
require 'inc/users_core.php';

if($_session->isLogged() == false)
    header('Location: index.php');

$_page = 16;

if(!isset($_GET['id']))
    header('Location: items.php');
$item = $_items->get_item($_GET['id']);
if(!$item->id)
    header('Location: items.php');
?>
<!DOCTYPE html>
<html>
<head>
	<?php require 'inc/head.php'; ?>
	<title></title>
	<link href="media/css/style.css" media="all" rel="stylesheet" type="text/css">
	<link href="media/css/w3.css" media="all" rel="stylesheet" type="text/css">
</head>
<body>
	<div id="main-wrapper">
		<?php require 'inc/header.php'; ?>
		<div class="wrapper-pad">
			<h2>Chemical Details</h2>
			<div style=" text-align: right; position: absolute;z-index:1000;">
				<?php 

				                    include('lib/full/qrlib.php');
				                    
				                    // Configuring SVG
				                    
				                    $cattt = $_cats->get_cat($item->category);
				                    $cat = $_cats->getusernames($item->owner);
				                                
				                    $dataText   = 'ID: CHEM-'.$item->id."\n"; 
				                    $dataText .= 'Item Name: '.$item->name."\n"; 
				                    $dataText .= 'CAS: '.$item->cas."\n"; 
				                    $dataText .= 'Tare Weight: '.$item->tarewight."\n"; 
				                    $dataText .= 'Hazard Class: '.$item->hazard."\n"; 
				                    $dataText .= 'Location: '.$item->location."\n"; 
				                    $dataText .= 'Category: '.$cattt->name."\n"; 
				                    $dataText .= 'Orginal QTY: '.$cat->qty."\n"; 
				                    $dataText .= 'Density: '.$cat->density."\n"; 
				                    $dataText .= 'Date Added: '.$cat->date_added."\n"; 
				                    $dataText .= 'Date Received: '.$cat->datereceived."\n"; 
				                    $dataText .= 'Expiry Date: '.$cat->expierydate."\n"; 
				                    $dataText .= 'Supplier: '.$cat->supplier."\n"; 
				                    $dataText .= 'Chemicalstate: '.$cat->chemicalstate."\n"; 
				                    $dataText .= 'Owner: '.$cat->name."\n"; 
                                    $dataText .= 'Descrption: '.$item->descrp."\n"; 
				                    $svgTagId   = 'id-of-svg';
				                    $saveToFile = false;
				                    $imageWidth = 280; // px
				                    $imageWidth2 = 120; // px
				                    // SVG file format support
				                    $svgCode = QRcode::svg($dataText, $svgTagId, $saveToFile, QR_ECLEVEL_L, $imageWidth);
				                    $svgCode2 = QRcode::svg($dataText, $svgTagId, $saveToFile, QR_ECLEVEL_L, $imageWidth2);
				                    
				               
				                    
				                    
				                                
				                                ?>
                <div> <?      echo $svgCode; ?> 
                
                </div>
			</div>
			<div  id= "printableArea" class="div2" style=" float:right; width:500px; height:600px;">
				<section class="performance-facts" >

<div class="w3-container">
    <?php echo "CHEM-".$item->id; ?><br>
  <h4><?php echo $item->name; ?></h4>

    <div id="wrapper">
    <div id="first"><img src="media/img/rectangles.png" style="width:80%"></div>
    <div id="second"><?php echo $svgCode2; ?></div>
</div>


  <div class="w3-card-3" style="width:70%">


      
      
        <table>

  <tr>
    <td style="background-color: <?php echo switchColor(strip_tags($item->hazard)); ?>"><?php echo $item->hazard; ?></td>
    <td><?php echo $item->location; ?></td>
    <td><?php
						                                                $cat = $_cats->get_cat($item->category);
						                                                echo $cat->name;
						                                                ?></td>
  </tr>
  <tr>
    <td><?php
						                                                
						                                                $cat = $_cats->getusernames($item->owner);
						                                                echo $cat->name;
						                                                
						                                                
						                                                
						                                                ?></td>
    <td></td>
    <td></td>
  </tr>
</table>
    <div class="w3-container w3-center">    
        
    </div>

  </div>
</div>

</section>
             <button id="print" onclick="printDiv('printableArea')" class="button">Print labe </button>         

			</div>
            
            <style>
            @media (max-width: 40em) {
#print,#printableArea
                {
display: none;
}
                }
            </style>    
            
            
            
			<div class="center">
				<div class="form">
					ID:<br>
					<div class="ni-cont light">
						<?php echo "CHEM-".$item->id; ?><br>
						<br>
					</div>Name:<br>
					<div class="ni-cont light">
						<?php echo $item->name; ?><br>
						<br>
					</div>Date Added:<br>
					<div class="ni-cont light">
						<?php echo $item->date_added; ?><br>
						<br>
					</div>Date Received:<br>
					<div class="ni-cont light">
						<?php echo $item->datereceived; ?><br>
						<br>
					</div>Expiry Date:<br>
					<div class="ni-cont light">
						<?php echo $item->expierydate; ?><br>
						<br>
					</div>CAS:<br>
					<div class="ni-cont light">
						<?php echo $item->cas; ?><br>
						<br>
					</div>Tare Weight:<br>
					<div class="ni-cont light">
						<?php echo $item->tarewight; ?><br>
						<br>
					</div>Supplier:<br>
					<div class="ni-cont light">
						<?php echo $item->supplier; ?><br>
						<br>
					</div>Hazard:<br>
					<div class="ni-cont light">
						<?php echo $item->hazard; ?><br>
						<br>
					</div>Location:<br>
					<div class="ni-cont light">
						<?php echo $item->location; ?><br>
						<br>
					</div>Location Detail:<br>
					<div class="ni-cont light">
						<?php echo $item->locationdetail; ?><br>
						<br>
					</div>Density:<br>
					<div class="ni-cont light">
						<?php echo $item->density; ?><br>
						<br>
					</div>Chemical State:<br>
					<div class="ni-cont light">
						<?php echo $item->chemicalstate; ?><br>
						<br>
					</div>Description:<br>
					<div class="ni-cont light">
						<?php echo $item->descrp; ?><br>
						<br>
					</div>Category:<br>
					<div class="ni-cont light">
						<?php
						                                                $cat = $_cats->get_cat($item->category);
						                                                echo $cat->name;
						                                                ?><br>
						<br>
					</div>Owner:<br>
					<div class="ni-cont light">
						<?php
						                                                
						                                                $cat = $_cats->getusernames($item->owner);
						                                                echo $cat->name;
						                                                
						                                                
						                                                
						                                                ?><br>
						<br>
					</div>Quantity:<br>
					<div class="ni-cont light">
						<?php echo $item->qty; ?><br>
						<br>
					</div>Safety Sheet:<br>
					<div class="ni-cont light">
						<?php
						                                                $cat = $_cats->make_urls_into_links($item->safety);
						                                                
						                                                echo "<td><a href='" . $cat . "'>" . $cat . "</a>  </td>";
						                                                
						                                                ?><br>
						<br>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clear" style="margin-bottom:40px;"></div>
	<div class="border" style="margin-bottom:30px;"></div>
    <script>
    function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
    
    
    </script>
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
    ?>


</body>
    
    <style>
    
#wrapper {
    overflow: auto;
}
#first {
    float: left;
    width: 175px;
}
#second {
    margin: 0 0 0 302px;
}
    </style>
</html>