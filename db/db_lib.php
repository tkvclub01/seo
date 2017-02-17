<?php

class DB {

	private $db = array();
	private $connection;

	public function db($args = array()) {
		$this->db['server']		= DBHOST;
		$this->db['username'] 	= DBUSER;
		$this->db['password'] 	= DBPWD;
		$this->db['database'] 	= DBNAME; 
		$this->connection = mysqli_connect($this->db["server"], $this->db["username"], $this->db["password"]);
		// Check connection
		if (mysqli_connect_errno()){
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$this->select_db();
	}
		
	public function close() {
		mysqli_close($this->connection);
		$this->connection = null; 
	}

	public function select_db() {
		mysqli_select_db($this->connection, $this->db["database"]); 
	}

	public function query($sql) {
		$this->result = mysqli_query($this->connection, $sql); 
	}
	
	public  function  fetch_array (){
		$rows = mysqli_fetch_array($this->result,MYSQLI_ASSOC);
		return $rows;
	}
	
	public function is_connected() {
		return ($this->connection) ? true : false; }

	public function clean($dirty) {
		if (!is_array($dirty)) {
			$dirty = ereg_replace("[\'\")(;|`,<>]", "", $dirty);
			$dirty = mysqli_real_escape_string($this->connection, trim($dirty));
			$clean = stripslashes($dirty);
			return $clean; };
			$clean = array();
			foreach ($dirty as $p=>$data) {
				$data = ereg_replace("[\'\")(;|`,<>]", "", $data);
				$data = mysqli_real_escape_string($this->connection, trim($data));
				$data = stripslashes($data);
				$clean[$p] = $data; };
				return $clean; }

}

function add_result($date, $kw, $rank, $path, $area, $engine, $domain) {
	$db = new DB ();
	$sql = "INSERT INTO page_rank (date, keyword, search_engine, area, rank, url, status, domain)
	VALUE ('$date','$kw','$engine','$area','$rank','$path',1, '$domain')";
	$db->query ( $sql );
	$db->close ();
}
function search_result($kw, $domain, $search_engine) {
	$db = new DB ();
	$sql = "SELECT * FROM page_rank WHERE keyword='$kw' and domain = '$domain' and search_engine='$search_engine' ORDER BY id DESC LIMIT 1";
	$db->query ( $sql );
	// output data of each row
	while ( $row = $db->fetch_array () ) {
		return $row;
	}
	$db->close ();
}
function update_result($id, $search_engine, $keyword, $domain, $curr_rank, $pre_rank, $path, $date) {
	$date_rp = string_to_date ( $date );
	$db = new DB ();
	$sql = "UPDATE page_rank SET search_engine='$search_engine', keyword='$keyword', domain='$domain', rank=$curr_rank, pre_rank=$pre_rank, url='$path', date='$date_rp' where id=$id";
	$db->query ( $sql );
	$db->close ();
}
function string_to_date($string_date) {
	if (isset ( $string_date )) {
		$string_date = new DateTime ( $string_date );
		$string_date = $string_date->format ( 'Y-m-d' );
		return $string_date;
	} else {
		$tz_object = new DateTimeZone ( 'Europe/London' );
		// date_default_timezone_set('Brazil/East');
		$datetime = new DateTime ();
		$datetime->setTimezone ( $tz_object );
		return $datetime->format ( 'Y-m-d' );
	}
}
function list_keyword($keyword, $id) {
	$condition = '';
	if (isset ( $id )) {
		$condition = " AND id < '$id'";
	}
	$keyword = trim ( $keyword );
	$db = new DB ();
	$sql = "SELECT * FROM page_rank WHERE keyword = '$keyword'" . $condition . " AND domain='" . $_POST ['domain'] . "' ORDER BY id DESC limit 1 ";
	// var_dump($sql);
	$db->query ( $sql );
	while ( $row = $db->fetch_array () ) {
		$list_data [] = array (
				'id' => $row ['id'],
				'date' => $row ['date'],
				'keyword' => $row ['keyword'],
				'path' => $row ['url'],
				'rank' => $row ['rank']
		);
	}
	return $list_data;
	$db->close ();
}
function get_content_by_date($string_date, $keywords, $domain, $search_engine) {
	// Clean the post data and make usable
	$domain = filter_var ( $domain, FILTER_SANITIZE_STRING );
	$keywords = filter_var($keywords, FILTER_SANITIZE_STRING);
	// Remove begining http and trailing /
	$domain = trim($domain);
	$domain = substr ( $domain, 0, 7 ) == 'http://' ? substr ( $domain, 7 ) : $domain;
	$domain = substr ( $domain, - 1 ) == '/' ? substr_replace ( $domain, '', - 1 ) : $domain;
	
	$db = new DB ();
	$date_rp = string_to_date ( $string_date );
	$condition = '';
	if (isset ( $keywords ) && $keywords != '') {
		$keywords = trim($keywords);
		$keywords = str_replace ( "\r\n", '|', $keywords );
		$list_keywords = explode ( '|', $keywords );
		$condition .= ' and (';		
		for($k = 0; $k < count ( $list_keywords ); $k ++) {
			$keywords = trim($list_keywords [$k]);
			if ($k == (count ( $list_keywords ) - 1)) {
				$condition .= " keyword = '$keywords' ";
			}else{
				$condition .= " keyword = '$keywords' or ";
			}
		}
		$condition .= ' )';
	}

	if (isset ( $domain ) && $domain != '') {
		$condition .= " and domain = '$domain' ";
	}

	$sql = "SELECT DISTINCT * FROM (SELECT * FROM page_rank WHERE date = '$date_rp' and search_engine = '$search_engine'" . $condition . 'ORDER BY id DESC) AS tbl';
	$db->query ( $sql );
	while ( $row = $db->fetch_array () ) {
		$list_data [] = array (
				'id' => $row ['id'],
				'date' => $row ['date'],
				'keyword' => $row ['keyword'],
				'url' => $row ['url'],
				'rank' => $row ['rank'],
				'pre_rank' => $row ['pre_rank'],
				'engine' => $row ['search_engine']
		);
	}
	return $list_data;
	$db->close ();
}


?>