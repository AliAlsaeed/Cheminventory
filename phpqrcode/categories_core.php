<?php

class Categories {
	private $self_file = 'categories_core.php';
	private $mysqli = false;
	private $session = false;
	
	public function __construct($m) { $this->mysqli = $m; }
	
	public function set_session_obj($obj) { $this->session = $obj; }
	
	public function get_cats($page, $items_per_page) {
		$page = stripslashes($page);
		$items_per_page = stripslashes($items_per_page);
		
		if($page == 0 || $page == 1)
			$x = 0;
		else
			$x = ($items_per_page * ($page-1));
		$y = $items_per_page;
		$q = $this->query("SELECT * FROM invento_categories ORDER BY id DESC LIMIT $x,$y", 'get_cats()');
		return $q;
	}
	
	public function search($string, $page, $items_per_page) {
		$s = "%$string%";
		
		if($page == 0 || $page == 1)
			$x = 0;
		else
			$x = ($items_per_page * ($page-1));
		$y = $items_per_page;

		$prepared = $this->prepare("SELECT * FROM invento_categories WHERE id LIKE ? OR name LIKE ? OR place LIKE ? OR descrp LIKE ? OR date_added LIKE ? ORDER BY id DESC LIMIT $x,$y", 'search()');
		$this->bind_param($prepared->bind_param('sssss', $s, $s, $s, $s, $s), 'search()');
		$this->execute($prepared, 'search()');
		
		if($this->is_mysqlnd())
			return $prepared->get_result();
		else{
			return $this->prepared_to_object($prepared);
		}
	}
	
	public function count_cats() {
		$res = $this->query("SELECT COUNT(*) as c FROM invento_categories", 'count_cats()');
		$obj = $res->fetch_object();
		return $obj->c;
	}
	
	public function count_cats_search($string) {
		$string = "%$string%";
		$prepared = $this->prepare("SELECT COUNT(*) as c FROM invento_categories WHERE id LIKE ? OR name LIKE ? OR place LIKE ? OR descrp LIKE ? OR date_added LIKE ?", 'count_cats_search()');
		$this->bind_param($prepared->bind_param('sssss', $string, $string, $string, $string, $string), 'count_cats_search()');
		$this->execute($prepared, 'count_cats_search()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->c;
		}else{
			$prepared->bind_result($val);
			$prepared->fetch();
			return $val;
		}
	}
	
	public function delete_cat($catid) {
		$prepared = $this->prepare("DELETE FROM invento_categories WHERE id=?", 'delete_cat()');
		$this->bind_param($prepared->bind_param('i', $catid), 'delete_cat()');
		$this->execute($prepared, 'delete_cat()');
		return true;
	}
	
	public function get_cats_dropdown() {
		$q = $this->query("SELECT id,name FROM invento_categories", 'get_cats_dropdown()');
		return $q;
	}
    	public function get_usernames() {
		$q = $this->query("SELECT id,name FROM invento_users", 'get_usernames()');
		return $q;
	}
    
	
	public function new_cat($name, $place, $desc) {
		$date = date('Y-m-d');
		$prepared = $this->prepare("INSERT INTO invento_categories(name,place,descrp,date_added) VALUES(?,?,?,?)", 'new_cat()');
		$this->bind_param($prepared->bind_param('ssss', $name, $place, $desc, $date), 'new_cat()');
		$this->execute($prepared, 'new_cat()');
		return true;
	}
	
	public function edit_cat($catid, $name, $place, $desc) {
		$prepared = $this->prepare("UPDATE invento_categories SET name=?, place=?, descrp=? WHERE id=?", 'edit_cat()');
		$this->bind_param($prepared->bind_param('sssi', $name, $place, $desc, $catid), 'edit_cat()');
		$this->execute($prepared, 'edit_cat()');
		return true;
	}
	
	public function get_cat($catid) {
		$res = $this->query("SELECT * FROM invento_categories WHERE id=$catid", 'get_cat()');
		$obj = $res->fetch_object();
		return $obj;
	}	
    
    public function getusernames($userid) {
		$res = $this->query("SELECT * FROM invento_users WHERE id=$userid", 'getusernames()');
		$obj = $res->fetch_object();
		return $obj;
	}
	
    function make_urls_into_links($plain_text) {
    return preg_replace(
        '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-~]*(\?\S+)?)?)?)@',
        '<a href="$1">$1</a>', $plain_text);
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

$_cats = new Categories($mysqli);