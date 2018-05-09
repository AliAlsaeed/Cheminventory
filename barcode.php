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
?>
<!DOCTYPE HTML>
<html>
<head>
</head>
<body>

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
    $dataText .= 'Descrption: '.$item->descrp."\n"; 
    $dataText .= 'Category: '.$cattt->name."\n"; 
    $dataText .= 'Owner: '.$cat->name."\n"; 
    $svgTagId   = 'id-of-svg';
    $saveToFile = false;
    $imageWidth = 280; // px
    // SVG file format support
    $svgCode = QRcode::svg($dataText, $svgTagId, $saveToFile, QR_ECLEVEL_L, $imageWidth);
    
    echo $svgCode;
    
    
                
                ?>
                                    

		
		<div class="clear" style="margin-bottom:40px;"></div>
		<div class="border" style="margin-bottom:30px;"></div>

</body>
</html>