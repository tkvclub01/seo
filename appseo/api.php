<?php

	DEFINE ('DB_USER', 'root');	 
	DEFINE ('DB_PASSWORD', '');
	DEFINE ('DB_HOST', 'localhost');
	DEFINE ('DB_NAME', 'news');
	 
	$mysqli = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to MySQL');
	@mysql_select_db (DB_NAME) OR die ('Could not select the database');
 
 ?>
<?php
 
 	mysql_query("SET NAMES 'utf8'"); 
	//mysql_query('SET CHARACTER SET utf8');
	
	if(isset($_GET['cat_id']))
	{
			//$query="SELECT * FROM tbl_news_category WHERE cid='".$_GET['cat_id']."' ORDER BY tbl_news_category.cid DESC";		
			//$resouter = mysql_query($query);
			
			$query="SELECT * FROM tbl_news_category c,tbl_news n WHERE c.cid=n.cat_id and c.cid='".$_GET['cat_id']."' ORDER BY n.nid DESC";			
			$resouter = mysql_query($query);
			
	}
	else if(isset($_GET['latest_news']))
	{
			$limit=$_GET['latest_news'];	 	
			
			$query="SELECT * FROM tbl_news_category c,tbl_news n WHERE c.cid=n.cat_id ORDER BY n.nid DESC LIMIT $limit";			
			$resouter = mysql_query($query);
	}
	else if(isset($_GET['apps_details']))
	{ 
			$query="SELECT * FROM tbl_settings WHERE id='1'";		
			$resouter = mysql_query($query);
	}
	else
	{	
			$query="SELECT * FROM tbl_news_category ORDER BY cid DESC";			
			$resouter = mysql_query($query);
	}
     
    $set = array();
     
    $total_records = mysql_num_rows($resouter);
    if($total_records >= 1){
     
      while ($link = mysql_fetch_array($resouter, MYSQL_ASSOC)){
	   
        $set['NewsApp'][] = $link;
      }
    }
     
     echo $val= str_replace('\\/', '/', json_encode($set));
	 	 
	 
?>