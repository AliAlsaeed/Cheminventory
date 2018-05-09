<?php

class Items {
	private $self_file = 'items_core.php';
	private $mysqli = false;
	private $session = false;
	
	public function __construct($m) { $this->mysqli = $m; }
	
	public function set_session_obj($obj) { $this->session = $obj; }
	
	public function get_items($page, $items_per_page) {
		$page = stripslashes($page);
		$items_per_page = stripslashes($items_per_page);
		
		if($page == 0 || $page == 1)
			$x = 0;
		else
			$x = ($items_per_page * ($page-1));
		$y = $items_per_page;
		
		$res = $this->query("SELECT * FROM invento_items ORDER BY id DESC LIMIT $x,$y", 'get_items()');
		return $res;
	}
	
	public function count_items() {
		$res = $this->query("SELECT COUNT(*) as c FROM invento_items", 'count_items()');
		$obj = $res->fetch_object();
		return $obj->c;
	}
    
    public function get_co_items($page, $items_per_page) {
		$page = stripslashes($page);
		$items_per_page = stripslashes($items_per_page);
		
		if($page == 0 || $page == 1)
			$x = 0;
		else
			$x = ($items_per_page * ($page-1));
		$y = $items_per_page;
		
		$res = $this->query("SELECT DISTINCT i.* FROM invento_items i JOIN invento_logs l ON i.id=l.item ORDER BY i.id DESC LIMIT $x,$y", 'get_co_items()');
		return $res;
	}
	
	public function count_co_items() {
		$res = $this->query("SELECT COUNT(DISTINCT i.id) as c FROM invento_items i JOIN invento_logs l ON i.id=l.item", 'count_items()');
		$obj = $res->fetch_object();
		return $obj->c;
	}
	
	public function count_items_search($string) {
		$s = "%$string%";
		$prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_items WHERE id LIKE ? OR name LIKE ? OR descrp LIKE ? OR date_added LIKE ? OR category IN (SELECT id FROM invento_categories WHERE name LIKE ?)", 'count_items_search()');
		$this->bind_param($prepared->bind_param('sssss', $s, $s, $s, $s, $s), 'count_items_search()');
		$this->execute($prepared, 'count_items_search()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->c;
		}else{
			$prepared->bind_result($c);
			$prepared->fetch();
			return $c;
		}
	}
	
	public function search($string, $page, $items_per_page) {
		$s = "%$string%";
		if($page == 0 || $page == 1)
			$x = 0;
		else
			$x = ($items_per_page * ($page-1));
		$y = $items_per_page;
		
		$prepared = $this->prepare("SELECT * FROM invento_items WHERE id LIKE ? OR name LIKE ? OR descrp LIKE ? OR date_added LIKE ? OR category IN (SELECT id FROM invento_categories WHERE name LIKE ?) ORDER BY id DESC LIMIT $x,$y", 'search()');
		$this->bind_param($prepared->bind_param('sssss', $s, $s, $s, $s, $s), 'search()');
		$this->execute($prepared, 'search()');
		
		if($this->is_mysqlnd())
			return $prepared->get_result();
		else
			return $this->prepared_to_object($prepared);
	}
    
    public function count_items_filter($location, $owner,$hazard) {
        if ($owner == '') {
            $prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_items WHERE location=?", 'count_items_filter()');
            $this->bind_param($prepared->bind_param('s', $location), 'count_items_filter()');
        }
        else if ($location == '') {
            $prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_items WHERE owner=?", 'count_items_filter()');
		  $this->bind_param($prepared->bind_param('s', $owner), 'count_items_filter()');
        }  
        else if ($hazard == '') {
            $prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_items WHERE hazard=?", 'count_items_filter()');
		  $this->bind_param($prepared->bind_param('s', $hazard), 'count_items_filter()');
        }
        else {
            $prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_items WHERE ( location=? AND owner=? ) AND hazard=?", 'count_items_filter()');
            $this->bind_param($prepared->bind_param('sss', $location, $owner,$hazard), 'count_items_filter()');
        }
		$this->execute($prepared, 'count_items_filter()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->c;
		}else{
			$prepared->bind_result($c);
			$prepared->fetch();
			return $c;
		}
	}
    
    public function filter($location, $owner, $hazard, $page, $items_per_page) {
		if($page == 0 || $page == 1)
			$x = 0;
		else
			$x = ($items_per_page * ($page-1));
		$y = $items_per_page;
		
        if ($owner == '') {
            $prepared = $this->prepare("SELECT * FROM invento_items WHERE location=? ORDER BY id DESC LIMIT $x,$y", 'filter()');
            $this->bind_param($prepared->bind_param('s', $location), 'filter()');
        }
        else if ($location == '') {
            $prepared = $this->prepare("SELECT * FROM invento_items WHERE owner=? ORDER BY id DESC LIMIT $x,$y", 'filter()');
            $this->bind_param($prepared->bind_param('s', $owner), 'filter()');
            
        }  
        else if ($hazard == '') {
            $prepared = $this->prepare("SELECT * FROM invento_items WHERE hazard=? ORDER BY id DESC LIMIT $x,$y", 'filter()');
            $this->bind_param($prepared->bind_param('s', $hazard), 'filter()');
        }
        else {
            $prepared = $this->prepare("SELECT * FROM invento_items WHERE ( location=? AND owner=? ) AND hazard=? ORDER BY id DESC LIMIT $x,$y", 'filter()');
            $this->bind_param($prepared->bind_param('sss', $location, $owner,$hazard), 'filter()');
        }
		$this->execute($prepared, 'filter()');
		
		if($this->is_mysqlnd())
			return $prepared->get_result();
		else
			return $this->prepared_to_object($prepared);
	}
	
	public function get_category_name($id) {
		$prepared = $this->prepare("SELECT name FROM invento_categories WHERE id=?", 'get_category_name()');
		$this->bind_param($prepared->bind_param('i', $id), 'get_category_name()');
		$this->execute($prepared, 'get_category_name()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->name;
		}else{
			$prepared->bind_result($name);
			$prepared->fetch();
			return $name;
		}
	}
		public function get_users_name($id) {
		$prepared = $this->prepare("SELECT name FROM invento_users WHERE id=?", 'get_users_name()');
		$this->bind_param($prepared->bind_param('i', $id), 'get_users_name()');
		$this->execute($prepared, 'get_users_name()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->username;
		}else{
			$prepared->bind_result($name);
			$prepared->fetch();
			return $name;
		}
	}
	
	public function get_item_name($id) {
		$prepared = $this->prepare("SELECT name FROM invento_items WHERE id=?", 'get_item_name()');
		$this->bind_param($prepared->bind_param('i', $id), 'get_item_name()');
		$this->execute($prepared, 'get_item_name()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->name;
		}else{
			$prepared->bind_result($name);
			$prepared->fetch();
			return $name;
		}
	}
    

	public function delete_item($id) {
		$prepared = $this->prepare("DELETE FROM invento_items WHERE id=?", 'delete_items()');
		$this->bind_param($prepared->bind_param('i', $id), 'delete_item()');
		$this->execute($prepared, 'delete_item()');
		
		$prepared = $this->prepare("DELETE FROM invento_logs WHERE item=?", 'delete_items()');
		$this->bind_param($prepared->bind_param('i', $id), 'delete_item()');
		$this->execute($prepared, 'delete_item()');
		
		return true;
	}
	
	public function update_item_qty($type, $id, $fromqty, $location ) {
		
		// First, update the item
		if($type == 1) {
            if(!is_numeric($id) || !is_numeric($fromqty) || !is_numeric($location)) {
			  die('inc/items_core.php - update_item_qty - Non Numeric Values');
            }
            
			$prepared = $this->prepare("UPDATE invento_items SET qty = IF(chemicalstate = 'solid', ? - tarewight, IF(chemicalstate = 'liquid', (? - tarewight) / density , qty - ?)) WHERE id=?", 'update_item_qty()');
			$this->bind_param($prepared->bind_param('iiii', $location, $location, $location, $id), 'update_item_qty()');
			$this->execute($prepared, 'update_item_qty()');
		}elseif($type == 2){
			$prepared = $this->prepare("UPDATE invento_items SET location = '$location' WHERE id=?", 'update_item_qty()');         
            
			$this->bind_param($prepared->bind_param('i', $id), 'update_item_qty()');
			$this->execute($prepared, 'update_item_qty()');
		}
		
		// Try to create the log, if fail, revert change
		if($type == 1)
			$update = $this->new_log(1, $id, $fromqty, $location);
		else
			$update = $this->new_log(2, $id, $fromqty, $location);
		
		if($update == false) {
			$prepared = $this->prepare("UPDATE invento_items SET location = $fromqty WHERE id=?", 'update_item_qty()');
			$this->bind_param($prepared->bind_param('i', $id), 'update_item_qty()');
			$this->execute($prepared, 'update_item_qty()');
			return false;
		}
		return true;
	}
	
	private function new_log($type, $item, $from, $to) {
		if($type == 1) {
			$date = date('Y-m-d');
			$user = $this->session->get_user_id();
            
			$prepared = $this->prepare("INSERT INTO invento_logs(`type`,item,fromqty,toqty,date_added,`user`) VALUES(1,?,?,(select qty from invento_items where id = ?),?,?)", 'new_log_1()');
			$this->bind_param($prepared->bind_param('iiisi', $item, $from, $item, $date, $user), 'new_log_1()');
			$this->execute($prepared, 'new_log_1()');
			
		}elseif($type == 2) {
			$date = date('Y-m-d');
			$user = $this->session->get_user_id();
			
			$prepared = $this->prepare("INSERT INTO invento_logs(`type`,item,fromqty,toqty,date_added,`user`) VALUES(2,?,?,?,?,?)", 'new_log()');
			$this->bind_param($prepared->bind_param('isssi', $item, $from, $to, $date, $user), 'new_log()');
			$this->execute($prepared, 'new_log()');
			
		}elseif($type == 3) {
			$date = date('Y-m-d');
			$user = $this->session->get_user_id();
			// Get actual price (from)
			$prepared = $this->prepare("INSERT INTO invento_logs(`type`,item,fromprice,toprice,date_added,`user`) VALUES(3,?,(SELECT name FROM invento_items WHERE id=?),?,?,?)", 'new_log()');
			$this->bind_param($prepared->bind_param('ssssi', $item, $item, $to, $date, $user), 'get_log()');
			$this->execute($prepared, 'new_log()');
		}
		return true;
	}
	
	public function get_cat_reg_items($catid) {
		$prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_items WHERE category=?", 'get_cat_reg_items()');
		$this->bind_param($prepared->bind_param('i', $catid), 'get_cat_reg_items()');
		$this->execute($prepared, 'get_cat_reg_items()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->c;
		}else{
			$prepared->bind_result($c);
			$prepared->fetch();
			return $c;
		}
	}
	
	public function get_cat_tot_items($catid) {
		$prepared = $this->prepare("SELECT SUM(qty) as s FROM invento_items WHERE category=?", 'get_cat_tot_items()');
		$this->bind_param($prepared->bind_param('i', $catid), 'get_cat_tot_items()');
		$this->execute($prepared, 'get_cat_tot_items()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			$s = $row->s;
		}else{
			$prepared->bind_result($s);
			$prepared->fetch();
		}
		
		if($s == '')
			return 0;
		return $s;
	}
	
	public function new_item($name, $descrp, $cat, $qty, $molecularformula,$cas,$supplier,$datereceived,$expierydate,$tarewight,$location,$locationdetail,$chemicalstate,$hazard,$owner,$safety, $density) {
        
        error_log($name."--". $descrp."--". $cat."--". $qty."--". $molecularformula."--".$cas."--".$supplier."--".$datereceived."--".$expierydate."--".$tarewight."--".$location."--".$locationdetail."--".$chemicalstate."--".$hazard."--".$owner, 3, "items_core.log");

		$name = stripslashes($name);
		$descrp = stripslashes($descrp);
		$cat = stripslashes($cat);
		$qty = stripslashes($qty);
		$molecularformula = stripslashes($molecularformula);
		$cas = stripslashes($cas);
		$supplier = stripslashes($supplier);
		$datereceived = stripslashes($datereceived);
		$expierydate = stripslashes($expierydate);
		$tarewight = stripslashes($tarewight);
		$location = stripslashes($location);
		$locationdetail = stripslashes($locationdetail);
		$chemicalstate = stripslashes($chemicalstate);
		$hazard = stripslashes($hazard);
		$owner = stripslashes($owner);
		$safety = stripslashes($safety);
        $density = stripslashes($density);
        if ($density == '') {
            $density = 0;
        }
        
        
		$date = date('Y-m-d');
		if($qty == '')
			$qty = 0;
        
        try{
		$prepared = $this->prepare("INSERT INTO invento_items(name,molecularformula,cas,supplier,datereceived,expierydate,tarewight,qty,hazard,location,locationdetail,chemicalstate,descrp,category,date_added,owner,safety,density) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", 'new_item()');
            $temp = $prepared->bind_param('sssssssssssssssssd',$name, $molecularformula, $cas, $supplier, $datereceived, $expierydate, $tarewight, $qty, $hazard, $location, $locationdetail,   $chemicalstate, $descrp, $cat, $date,$owner,$safety,$density);
            error_log ($temp,3,"items_core.log");
		    $this->bind_param($temp, 'new_item()');		
            
            
            //$this->bind_param($prepared->bind_param('ssiiis',$name, $molecularformula, $cas, $supplier, $datereceived, $expierydate, $tarewight, $qty, $hazard, $location, $locationdetail, $chemicalstate, $desc, $cat, $date), 'new_item()');
            
		$this->execute($prepared, 'new_item()');
        }catch (Exception $e){
        error_log ($ex,3,"items_core.log");}
            
		return true;
        
	}
	
	public function get_item($itemid) {
		$prepared = $this->prepare("SELECT * FROM invento_items WHERE id=?", 'get_item()');
		$this->bind_param($prepared->bind_param('i', $itemid), 'get_item()');
		$this->execute($prepared, 'get_item()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			return $result->fetch_object();
		}else{
			return $this->prepared_to_object($prepared);
		}
	}

	public function update_item($itemid, $name, $descrp, $cat) {
		// Create log

		$prepared = $this->prepare("UPDATE invento_items SET name=?, descrp=?, category=? WHERE id=?", 'update_item()');
		$this->bind_param($prepared->bind_param('sssi', $name, $descrp, $cat, $itemid), 'update_item()');
		$this->execute($prepared, 'update_item()');
		return true;
	}

   	public function parse_price($p) {
		return $p;
	}

	/***
	  *  Private functions
	  *
	***/
	private function prepare($query, $func) {
		$prepared = $this->mysqli->prepare($query);
		if(!$prepared)
			die("Couldn't prepare query. inc/{$this->self_file} - $func");
		return $prepared;
	}
	private function bind_param($param, $func) {
		if(!$param)
			die("Couldn't bind parameters. inc/{$this->self_file} - $func");
		return $param;
	}
	private function execute($prepared, $func) {
		$exec = $prepared->execute();
		if(!$exec)
			die("Couldn't execute query. inc/{$this->self_file} - $func");
		return $exec;
	}
	private function query($query, $func) {
		$q = $this->mysqli->query($query);
		if(!$q)
			die("Couldn't run query. inc/{$this->self_file} - $func");
		return $q;
	}
	
	/****
	 * Alternative to fetch_object for users who doesn't have MySQL Native Driver
	 * (Single row)
	*****/
	private function prepared_to_sobject($prepared) {
		$parameters = array();
		$metadata = $prepared->result_metadata();
		
		while($field = $metadata->fetch_field())
			$parameters[] = &$row[$field->name];
		call_user_func_array(array($prepared, 'bind_result'), $parameters);
		
		$nrs = 0;
		while($prepared->fetch()) {
			$cls = new stdClass;
			foreach($row as $key => $val)
				$cls->$key = $val;
			$nrs++;
		}
		
		return ($nrs == 0) ? 0 : $cls;
	}
	
	/****
	 * Alternative to fetch_object for users who doesn't have MySQL Native Driver
	 * (Multiple rows)
	*****/
	private function prepared_to_object($prepared) {
		$parameters = array();
		$metadata = $prepared->result_metadata();
		
		while($field = $metadata->fetch_field())
			$parameters[] = &$row[$field->name];
		call_user_func_array(array($prepared, 'bind_result'), $parameters);
		
		$nrs = 0;
		while($prepared->fetch()) {
			$cls = new stdClass;
			foreach($row as $key => $val)
				$cls->$key = $val;
			$results[] = $cls;
			$nrs++;
		}
		
		return ($nrs == 0) ? 0 : $results;
	}
	public function is_mysqlnd() {
		if(function_exists('mysqli_stmt_get_result'))
			return true;
		return false;
	}
	public function __destruct() {
		if(is_resource($this->mysqli) && get_resource_type($this->mysqli) == 'mysql link')
			$this->mysqli->close();
	}
}

$_items = new Items($mysqli);