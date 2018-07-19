<?php

class Main {
	public $root, $db_connection;
	
	function __construct() {
		global $root, $db_connection;
		$this -> root = $root;
		$this -> db_connection = $db_connection;
		$this -> list_limit = 100;
	}
	
	public function sign_in($email, $password, $link) {
		$login_query_string = "SELECT * FROM `people` WHERE `email` = '$email' AND `password` = '$password'";
		$login_query = $this->query($login_query_string);
		
		if(mysqli_num_rows($login_query)) {
			$person = $login_query -> fetch_assoc();
			$_SESSION['person_id'] = $person['person_id'];
			if($email == 'admin@ezylinc.com') $_SESSION['admin_id'] = $person['person_id'];
			$this -> sign('in', $person['person_id']);
			if(strlen($person['gender']) > 2) {
				echo '<script>document.location = \''.$link.'\'</script>';
			} else {
				echo '<script>document.location = \'welcome\'</script>';
			}
			
		} else {
			$this->print_alert('Email or password is incorrect', 'danger');
		}
	}
	
	public function clean_text($text, $clean = 0) {
		//if($clean) return nl2br(stripslashes($text));
		return addslashes(htmlspecialchars($text));
	}
	
	public function validate($validity, $input, $value, $length = 0) {
		if($validity == 'length') {
			if(strlen($input) < $length) die($this->print_alert('Please enter '.$value, 'warning'));
		} elseif($validity == 'default') {
			if(strpos($input, $value)) die($this->print_alert('Please choose '.$value, 'warning'));
		} elseif($validity == 'zero') {
			if($input < 1) die($this->print_alert('Please choose '.$value, 'warning'));
		} 	
	}
	
	public function query($query_string) {
		$query = $this -> db_connection->query($query_string) or die($this->print_alert(mysqli_error($this->db_connection), 'danger'));
		return $query;
	}

	public function ago($datetime) {
		$diff = time() - $datetime;
		if($diff < 30) return 'now';
		if($diff < 60) return $diff.'s.';
		if($diff < 3600) return round($diff/60).'m.';
		if($diff < 86400) return round($diff/3600).'h.';
		return round($diff/86400).'d.';
	}
	
	public function get_sum($table_name, $column_name, $condition) {
		$query = "SELECT SUM(`$column_name`) AS subtotal FROM `$table_name` $condition ";
		$items_query = $this->query($query);
		$subtotal_array = $items_query->fetch_assoc();
		$subtotal = $subtotal_array['subtotal'];
		if($subtotal) return $subtotal;
		return 0;
	}
	
	public function pagenate($data, $page, $show=0) {
		$number_of_records = $data->num_rows;

		if($number_of_records < ($this->list_limit + 1)) {
			$number_of_pages = 1;
		} else {
			$raw_number_of_pages = $number_of_records/$this->list_limit;
			$raw_number_of_pages = explode('.', $raw_number_of_pages);
			
			if($number_of_records%$this->list_limit) {
				if($raw_number_of_pages[1]) {
					$number_of_pages = $raw_number_of_pages[0] + 1;
				} else {
					$number_of_pages = $raw_number_of_pages[0];
				}
			} else {
				$number_of_pages = $raw_number_of_pages[0];
			}
		}

		if(isset($_REQUEST['p'])) {
			$current_page = $_REQUEST['p'];
		} else {
			$current_page = 1;
		}
		
		if(isset($_REQUEST['s'])) {
			$show = $_REQUEST['s'];
		}
		
		$pagenation = '<div class="col-lg-4" style="margin-bottom:10px">';
		
		$pagenation .= '<div class="input-group">
		<span class="input-group-addon">Page</span>
		<select class="form-control tipsy" style="" id="current_page" data-placement="top" title="Jump to a page" onchange="loadList(\'list.php\', this.value)">';
		$pagenation .= '<option value="'.$current_page.'">'.$current_page.'</option>';
		for($pages = 1; $pages <= ($number_of_pages); $pages++) { if($pages != $current_page) $pagenation .= '<option value="'.$pages.'">'.$pages.'</option>'; }
		$pagenation .= '</select><span class="input-group-addon">
		of '.$number_of_pages.' of '.$number_of_records;
		if($number_of_records > 1) { $pagenation .= ' records'; } else { $pagenation .= ' record'; }
		$pagenation .= '</span></div> </div>';	
		
		$pagenation .= '<div class="col-lg-4" style="margin-bottom:10px">
		<div class="btn-group btn-group-justified">
		<div class="btn-group" role="group">';

		$pagenation .= '<a class="btn btn-default tipsy" data-placement="top" title="First page" onclick="loadList(\'list.php\', 1)"><span class="fa  fa-fast-backward"></span></a>';
		
		$pagenation .= ' </div> <div class="btn-group" role="group">';
		
		if($current_page > 1) {
			$pagenation .= '<a class="btn btn-default tipsy" style="border-left:0" data-placement="top" title="Prev. page" onclick="loadList(\'list.php\', '.($current_page - 1).')"> <span class="fa  fa-backward"></span></a>';
		} else {
			$pagenation .= '<a class="btn btn-default tipsy" disabled style="border-left:0"> <span class="fa  fa-backward"></span></a>';
		}
		
		$pagenation .= ' </div> <div class="btn-group" role="group">';
		
		if($number_of_pages > $current_page) {
			$pagenation .= '<a class="btn btn-default tipsy" style="border-left:0" data-placement="top" title="Next page" onclick="loadList(\'list.php\', '.($current_page + 1).')"><span class="fa  fa-forward"></span></a>';
		} else {
			$pagenation .= '<a class="btn btn-default tipsy" style="border-left:0" disabled "><span class="fa  fa-forward"></span></a>';
		} 
		
		$pagenation .= ' </div> <div class="btn-group" role="group">';
		
		$pagenation .= '<a class="btn btn-default tipsy" style="border-left:0" data-placement="top" title="Last page" onclick="loadList(\'list.php\', '.$number_of_pages.')"> <span class="fa fa-fast-forward"></span></a>';
		
		$pagenation .= ' </div>';
		
		if(strlen($page)) { 
			$pagenation .= '<div class="btn-group" role="group">
			<a class="btn btn-primary tipsy" style="border-left:0" title="Create new record" data-toggle="modal" data-backdrop="static" data-target="#modal_md" href="form.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
			</div>';
		}

		$pagenation .= '</div>';
		
		$pagenation .= '</div>';
		
		return $pagenation;
	}
	
	public function resize_image($source_image, $destination_filename, $width = 200, $height = 200, $quality = 70, $crop = true) {
		if(! $image_data = getimagesize($source_image)){
			return false;
		}

		switch($image_data['mime']) {
			case 'image/gif':
				$img_original = imagecreatefromgif($source_image);
				$suffix = ".gif";
			break;
			case 'image/jpeg';
				$img_original = imagecreatefromjpeg($source_image);
				$suffix = ".jpg";
			break;
			case 'image/png':
				$img_original = imagecreatefrompng($source_image);
				$suffix = ".png";
			break;
			
			default:
				$img_original = imagecreatefromjpeg($source_image);
		}

		$old_width = $image_data[0];
		$old_height = $image_data[1];
		$new_width = $width;
		$new_height = $height;
		$src_x = 0;
		$src_y = 0;
		$current_ratio = round($old_width / $old_height, 2);
		$desired_ratio_after = round($width / $height, 2);
		$desired_ratio_before = round($height / $width, 2);
		
		if($crop) {
			$new_image = imagecreatetruecolor($width, $height);
			if($current_ratio > $desired_ratio_after) {
				$new_width = $old_width * $height / $old_height;
			}

			if($current_ratio > $desired_ratio_before && $current_ratio < $desired_ratio_after) {
				if($old_width > $old_height) {
						$new_height = max($width, $height);
						$new_width = $old_width * $new_height / $old_height;
				}
				else {
						$new_height = $old_height * $width / $old_width;
				}
			}

			if($current_ratio < $desired_ratio_before ) {
				$new_height = $old_height * $width / $old_width;
			}
			
			$width_ratio = $old_width / $new_width;
			$height_ratio = $old_height / $new_height;

			$src_x = floor((($new_width - $width) / 2) * $width_ratio);
			$src_y = round((($new_height - $height) / 2) * $height_ratio);
		} else {
			if($old_width > $old_height) {
					$ratio = max($old_width, $old_height) / max($width, $height);
			} else {
					$ratio = max($old_width, $old_height) / min($width, $height);
			}
			$new_width = $old_width / $ratio;
			$new_height = $old_height / $ratio;
			$new_image = imagecreatetruecolor($new_width, $new_height);
		}

		if(($image_data['mime'] == 'image/png') || ($image_data['mime'] == 'image/gif')) {
			$backgroundColor = imagecolorallocate($new_image, 250, 250, 250);
			imagefill($new_image, 0, 0, $backgroundColor);
		}
		imagecopyresampled($new_image, $img_original, 0, 0, $src_x, $src_y, $new_width, $new_height, $old_width, $old_height);
		
		imageinterlace($new_image, 1);
		imagejpeg($new_image, $destination_filename);
		imagedestroy($new_image);
		imagedestroy($img_original);
		return true;
	}

	public function list_items($table_name, $column_id, $column_name, $condition = '', $selected_id = 0) {
		$query = "SELECT * FROM $table_name $condition ORDER BY '$column_name'";
		$items_query = $this->db_connection->query($query) or die($this->print_alert(mysqli_error($this->db_connection), 'danger'));
		while($items = $items_query->fetch_assoc()) {
			$col_id = $items[$column_id];
			$col_name = $items[$column_name];
			if($col_id == $selected_id) {
				echo '<option value="'.$col_id.'" > '.$col_name.' </option>';
			} else {
				echo '<option value="'.$col_id.'" > '.$col_name.' </option>';
			}
		}
	}

	public function get_item($table_name, $column_name, $column_id, $id_value) {
		$query = "SELECT * FROM `$table_name` WHERE `$column_id` = '$id_value'";
		$items_query = $this->db_connection->query($query) or die($this->print_alert(mysqli_error($this->db_connection), 'danger'));
		while($items = $items_query->fetch_assoc()) {
			return $items[$column_name];
		}
	}

	public function get_next_id($table_name, $column_name, $condition = '') {
		if(strlen($condition)) {
			$query = "SELECT MAX(`$column_name`) AS max_id FROM `$table_name` $condition ";
		} 	else	{
			$query = "SELECT MAX(`$column_name`) AS max_id FROM `$table_name`";
		}	
		$items_query = $this->db_connection->query($query) or die($this->print_alert(mysqli_error($this->db_connection), 'danger'));
		$max_id = $items_query->fetch_assoc();
		return $max_id['max_id'] + 1;
	}

	public function get_json($query) {
		$items = array();
		$items_query = $this->db_connection->query($query) or die($this->print_alert(mysqli_error($this->db_connection), 'danger'));
		while($item = mysqli_fetch_assoc($items_query)) {
			array_push($items, $item);
		}
		return json_encode($items);
	}
	
	public function print_alert($message, $alert_level) {
		if($alert_level == 'success') {
			$icon = 'ok-sign';
		} elseif($alert_level == 'info') {
			$icon = 'info-sign';
		} elseif($alert_level == 'warning') {
			$icon = 'alert';
		} elseif($alert_level == 'danger') {
			$icon = 'minus-sign';
		} else {
			$icon = 'info_sign';
		}
		echo '<div class="alert alert-'.$alert_level.' alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong><span class="glyphicon glyphicon-'.$icon.'" aria-hidden="true"></span> '.$message.'</strong>
		</div>';
	}
	
	public function get_count($table_name, $count_query = '') {
		if(strlen($count_query)) {
			$query = $count_query;
		} else {
			$query = "SELECT * FROM `$table_name`";
		}
		$items_query = $this->db_connection->query($query) or die($this->print_alert(mysqli_error($this->db_connection), 'danger'));
		return mysqli_num_rows($items_query);
	}
	
	public function date_difference($date1, $date2) {
		$diff = abs($date2-$date1);
		
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		
		$diff = '';
		
		if($years) $diff .= $years.'y ';
		if($months) $diff .= $months.'m ';
		if($days) $diff .= $days.'d ';
		
		if(!strlen($diff)) $diff = '0d';
		
		return $diff;
	}
	
	public function is_member($group_id, $person_id) {
		$fetch_query_string = "SELECT * FROM `groupmembers` WHERE (`group_id` = $group_id AND (`member_id` = '$person_id' OR `admin_id` = '$person_id') AND (`status` = 1))";
		$fetch_query = $this->query($fetch_query_string);
		return mysqli_num_rows($fetch_query);
	}
	
	public function is_guest($group_id, $person_id) {
		$fetch_query_string = "SELECT * FROM `groupmembers` WHERE (`group_id` = $group_id AND (`member_id` = '$person_id' OR `admin_id` = '$person_id'))";
		$fetch_query = $this->query($fetch_query_string);
		return mysqli_num_rows($fetch_query);
	}
	
	public function send_mail($email, $title, $content) {
		$message = '<html>
		<head>
		<!DOCTYPE html>
		<html lang="en-us">

		<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>'.$title.'</title>
		</head>

		<body style="font-family:\'HelveticaNeue-Light\', Tahoma, \'Lusida Sans Unicode\', Arial, Tunga; font-size:16px; color:rgb(90,90,90); background-color:rgb(250,250,250); padding:0; margin:0;} ">

		<div style="width:100%">

		<div style="width:100%; max-width:800px; margin:0 auto">

		<div style="font-size:1.8em; border-bottom:1px solid rgb(180,180,180); color:rgb(255,255,255); background-color:rgb(230,230,230); padding:0.6em">
		<a href="http://www.ezylinc.com" style="text-decoration:none;"><span style="color:rgb(120,10,10)">Ezylinc.com</span></a>
		<span style="float:right">explore the world</span>
		</div>
		<div style="border-bottom: 1px solid rgb(220,220,220); padding:0.8em">
		'.$title.'
		</div>

		<div style="padding:0.6em"">
		<p>
		'.$content.'
		</p><p>
		Regards,<br />
		Ezylinc Team.<br />
		</p>
		</div>

		<div style="background-color:rgb(230,230,230); text-align:center; border-top:1px solid rgb(200,200,200); padding:0.6em;">

		Ezylinc - Explorer the World.

		</div>

		</div>

		</div>

		</body>
		</html>';

		$to = $email;
		$subject = $title;
		$headers = 'From: Ezylinc Team<no-reply@ezylinc.com>' . "\r\n" .
					'Reply-To: team@ezylinc.com' . "\r\n" .
					'MIME-Version: 1.0' . "\r\n".
					'Content-type: text/html; charset=utf-8' . "\r\n".
					'X-Mailer: PHP/' . phpversion();
						
		mail($to, $subject, $message, $headers);

		//echo $message;
	}
	
	public function sign($status, $person_id) {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
			$mobile = true;
		} else {
			$mobile = false;
		}
		$now = time();
		$sign_query_string = "INSERT INTO `sign` 
			VALUES (
				'',
				'$status',
				'$person_id',
				'$useragent',
				'$mobile',
				'$now'
			)";
		$sign_query = $this->query($sign_query_string);
	}
}

$main = new Main();

?>