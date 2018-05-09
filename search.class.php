<?php
class search {
	
	var $table;
	var $field1;
	var $field2;
	
function queryRow($query){
//define database settings
define("host", "localhost");
define("login", "cheminve_ali");
define("senha", "t00532799?");
//define database name
define("data", "cheminve_ali");
//conection routine
    try{
    	$host = host;
    	$data = data;
             $connection = new PDO("mysql:host=$host;dbname=$data", login, senha);
             //$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             $result = $connection->prepare($query);
             $result->execute();
             return $result;
             
             $this->connection = $connection;
    }catch(PDOException $e){
    echo $e->getMessage();
    }
    }
    
      function close($connection){
	      $connection = null;
	      }
	function query($query){
	    $host = host;
	    $result = $this->queryRow($query);
	    $row = $result->fetch(PDO::FETCH_ASSOC);
	    $this->close($this->connection);
	    $this->query = $query;
	    return $row;
	    }
//finish connection

//method to list the fields
function fieldSelect(){	
	$query = $this->queryRow('SHOW FULL COLUMNS FROM '.$this->table);
	$retorno  = "<select name=\"fieldselect\">\n";
	foreach ($query as $collums){
	if ($_POST['fieldselect'] == $collums['Field']){
				$selected = " selected=\"selected\" ";
	}else{
				$selected = "";		
	}
	$retorno .= "<option value=\"$collums[Field]\"$selected>$collums[Field]</option>\n";
	}
	$retorno .= "</select>\n";
	return $retorno;	
}
//method to select the functions to condictions
function whereSelect(){
	$wheres = array();
	$wheres[] = 'equal';
	$wheres[] = 'diferent';
	$wheres[] = 'minor';
	$wheres[] = 'more';
	$wheres[] = 'minororequal';
	$wheres[] = 'moreorequal';
	$wheres[] = 'content';
	$wheres[] = 'notcontent';
	$wheres[] = 'between';
	$wheres[] = 'notbetween';
	
	$label[] = 'Equal';
	$label[] = 'Diferent';
	$label[] = 'Minor';
	$label[] = 'More';
	$label[] = 'Minor or Equal';
	$label[] = 'More or Equal';
	$label[] = 'Content';
	$label[] = 'Not Content';
	$label[] = 'Between';
	$label[] = 'Not Between';
	
	$retorno  = "<select name=\"select\">\n";
		$i=0;
		do{
			if ($_POST['select'] == $wheres[$i]){
				$selected = " selected=\"selected\" ";
			}else{
				$selected = "";		
		}
	    $retorno .= "<option value=\"$wheres[$i]\"$selected>$label[$i]</option>\n";		
		$i++;
		}while($i < count($wheres));
	
	$retorno .= "</select>\n";
	return $retorno;	
}
	function fieldText($size, $max){
		$retorno .= "<input type=\"text\" name=\"fieldtext\" size=\"$size\" maxlength=\"$max\" value=\"$_POST[fieldtext]\" />\n";
	
		return $retorno;
		
}
//method to implement condictions and your variables
	function wheres($value){
		$retorno = "";
		//parei aqui
		$this->field2 = explode(' OR ',$this->field2);
		//var_dump($this->field2);
		$i = 0;
		switch($value){
		case 'equal':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 = '$field2' ";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'diferent':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 != '$field2'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'minor':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 < '$field2'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'more':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 > '$field2'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'minororequal':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 <= '$field2'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'moreorequal':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 >= '$field2'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'content':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 LIKE '%$field2%'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'notcontent':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 NOT LIKE '%$field2%'";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'between':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 BETWEEN $field2";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
		case 'notbetween':
		foreach ($this->field2 as $field2){
		$retorno .= "$this->field1 NOT BETWEEN $field2";
		$i = ++$i;
		if ($i != 0 && $i != count($this->field2)){
		$retorno .= " OR ";
		}
		}
		break;
	}
	return $retorno;
	}
//method to list results of sql consult
	function result($fields){
	if (isset($_POST['submit'])){
	$this->field1 = $_POST['fieldselect'];
	$this->field2 = $_POST['fieldtext'];
	$resultfields = "";
	if(is_array($fields)){
		$i = 0;
		foreach($fields as $collums){
			if($i< count($fields)-1){
			$resultfields .= $collums.', ';
		}else{
			$resultfields .= $collums;
		}
		$i = ++$i;
		
	}
	}else{
		$resultfields = $fields;
	}
	$query = $this->queryRow("SELECT $resultfields FROM $this->table WHERE ".$this->wheres($_POST['select']));	
	$retorno = "<table>\n";
	foreach($query as $querycollum){
	$retorno .= "<tr>";
	if(is_array($fields)){
	foreach($fields as $collumstable){
		$retorno .= "<td>$querycollum[$collumstable]</td>";
			}
	$retorno .= "</tr>\n";
	}
	}	
	$retorno .= "</table>\n";
	return $retorno;
	}
}
}
 ?>