<?php

class Session {
	private $self_file = 'session.php';
	private $mysqli = false;
	
	public function __construct($m) { $this->mysqli = $m; }
	
	public function isLogged() {
		if(!isset($_SESSION['invento_logged']) || !is_array($_SESSION['invento_logged']))
			return false;
		
		if(!isset($_SESSION['invento_logged']['u']) || !isset($_SESSION['invento_logged']['p']))
			return false;
		
		$u = $_SESSION['invento_logged']['u'];
		$p = $_SESSION['invento_logged']['p'];
		
		$prepared = $this->prepare("SELECT count(*) as c FROM invento_users WHERE username=? && password=?", 'isLogged()');
		$this->bind_param($prepared->bind_param('ss', $u, $p), 'isLogged()');
		$this->execute($prepared, 'isLogged()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			$c = $row->c;
		}else{
			$prepared->bind_result($c);
			$prepared->fetch();
		}
		
		if($c == '1')
			return true;
		return false;
	}
	
	public function refresh_password($pass) {
		$_SESSION['invento_logged']['p'] = md5($pass);
		return true;
	}
	
	public function login($u, $p) {
		$p = md5($p);
		
		$prepared = $this->prepare("SELECT count(*) as c FROM invento_users WHERE username=? and password=?", 'isLogged()');
		$this->bind_param($prepared->bind_param('ss', $u, $p), 'login()');
        
       // error_log($prepared, 3, "session.log");
		$this->execute($prepared, 'login()');
		
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			$c = $row->c;
		}else{
			$prepared->bind_result($c);
			$prepared->fetch();
		}
		
		if($c != '1')
			return false;
			
		$_SESSION['invento_logged']['u'] = $u;
		$_SESSION['invento_logged']['p'] = $p;
		
		return true;
	}
	
	public function logout() {
		if(isset($_SESSION['invento_logged']))
			$_SESSION['invento_logged'] = false;
		unset($_SESSION);
		session_destroy();
		return true;
	}
	
	public function get_user_id() {
		$username = $_SESSION['invento_logged']['u'];
		
		$prepared = $this->prepare("SELECT id FROM invento_users WHERE username=?", 'get_user_id()');
		$this->bind_param($prepared->bind_param('s', $username), 'get_user_id()');
		$this->execute($prepared, 'get_user_id()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->id;
		}else{
			$prepared->bind_result($id);
			$prepared->fetch();
			return $id;
		}
	}
	
	public function get_user_name_by_id($id) {
		$prepared = $this->prepare("SELECT username FROM invento_users WHERE id=?", 'get_user_name_by_id()');
		$this->bind_param($prepared->bind_param('i', $id), 'get_user_name_by_id()');
		$this->execute($prepared, 'get_user_name_by_id()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->username;
		}else{
			$prepared->bind_result($username);
			$prepared->fetch();
			return $username;
		}
	}
	
	public function get_user_role() {
		$id = $this->get_user_id();
		
		$prepared = $this->prepare("SELECT role FROM invento_users WHERE id=?", 'get_user_role()');
		$this->bind_param($prepared->bind_param('i', $id), 'get_user_role()');
		$this->execute($prepared, 'get_user_role()');
		
		if($this->is_mysqlnd()) {
			$result = $prepared->get_result();
			$row = $result->fetch_object();
			return $row->role;
		}else{
			$prepared->bind_result($role);
			$prepared->fetch();
			return $role;
		}
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
            
        {
            $prepared_str =  print_r($prepared, true);
            die("Couldn't execute query. inc/{$this->self_file} - $func - $exec - $prepared_str ");}
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

$_session = new Session($mysqli);