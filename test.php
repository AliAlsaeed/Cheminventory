<?php
include('search.class.php');
$search = new search;
//table to search
$search->table = 'cidades';
//array to show results
$result = array('id', 'Regiao');
 ?>
 <p> Insert a field to search</p>
 <p> For user between, an not between, use the boollean operator AND </p>
 <p> For search with more words, use the boollean operator OR</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
<?=$search->fieldSelect()?>
<?=$search->whereSelect()?>
<?=$search->fieldText(10,20)?>
<input type="submit" name="submit" value="submit" />
</form>
<?=$search->result($result)?>