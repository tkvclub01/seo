<?php
	class functions{
		function get_random_string($valid_chars, $length){
    
			// start with an empty random string
			$random_string = "";

			// count the number of chars in the valid chars string so we know how many choices we have
			$num_valid_chars = strlen($valid_chars);

			// repeat the steps until we've created a string of the right length
			for ($i = 0; $i < $length; $i++)
			{
				// pick a random number from 1 up to the number of valid chars
				$random_pick = mt_rand(1, $num_valid_chars);

				// take the random character out of the string of valid chars
				// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
				$random_char = $valid_chars[$random_pick-1];

				// add the randomly-chosen char onto the end of our string so far
				$random_string .= $random_char;
			}

			// return our finished random string
			return $random_string;
		}// end of get_random_string()
		
		function sanitize($string){
			// check string value
			$string = addslashes(trim(strip_tags(stripslashes($string))));
			return $string;
		}// end of sanitize()
		
		function check_integer($which) {
			if(isset($_GET[$which])){
				if (intval($_GET[$which])>0) {
					return intval($_GET[$which]);
				} else {
					return false;
				}
			}
			return false;
		}//end of check_integer()

		function get_current_page() {
			if(($var=$this->check_integer('page'))) {
				//return value of 'page', in support to above method
				return $var;
			} else {
				//return 1, if it wasnt set before, page=1
				return 1;
			}
		}//end of method get_current_page()
		
		function doPages($page_size, $thepage, $query_string, $total=0, $keyword) {
			//per page count
			$index_limit = 10;
			
			//set the query string to blank, then later attach it with $query_string
			$query='';
			
			if(strlen($query_string)>0){
				$query = "&amp;".$query_string;
			}
				
			//get the current page number example: 3, 4 etc: see above method description
			$current = $this->get_current_page();
			
			$total_pages=ceil($total/$page_size);
			$start=max($current-intval($index_limit/2), 1);
			$end=$start+$index_limit-1;

			echo '<div id="page_num">';

			if($current==1) {
				echo '';
			} else {
				$i = $current-1;
				echo '<a href="'.$thepage.'?page='.$i.$query.'&keyword='.$keyword.'" rel="nofollow" title="go to page '.$i.'"><button type="button" class="tombol"><</button></a>';
				//echo '<p>...</p>&nbsp;';
			}
				//<button>'.$i.'</button>
			if($start > 1) {
				$i = 1;
				echo '<a href="'.$thepage.'?page='.$i.$query.'&keyword='.$keyword.'" title="go to page '.$i.'">'.$i.'</a>&nbsp;';
			}

			for ($i = $start; $i <= $end && $i <= $total_pages; $i++){
				if($i==$current) {
					echo '<button type="button" class="tombol-selected">'.$i.'</button>';
				} else {
					echo '<a href="'.$thepage.'?page='.$i.$query.'&keyword='.$keyword.'" title="go to page '.$i.'"><button type="button" class="tombol">'.$i.'</button></a>';
				}
			}

			if($total_pages > $end){
				$i = $total_pages;
				echo '<a href="'.$thepage.'?page='.$i.$query.'&keyword='.$keyword.'" title="go to page '.$i.'">'.$i.'</a>&nbsp;';
			}

			if($current < $total_pages) {
				$i = $current+1;
				//echo '<p>...</p>&nbsp;';
				echo '<a href="'.$thepage.'?page='.$i.$query.'&keyword='.$keyword.'" rel="nofollow" title="go to page '.$i.'"><button type="button" class="tombol">></button></a>&nbsp;';
			} else {
				echo '';
			}
				
			//if nothing passed to method or zero, then dont print result, else print the total count below:       
			if ($total != 0){
				//prints the total result count just below the paging
				echo '<p><br>( total '.$total.' )</p></div>';
			}else {
				echo '</div>';
			};
		 
		}//end of method doPages()
		
	}
?>