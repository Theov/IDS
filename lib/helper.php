<?php
abstract class helper{
	public function debug_s($s){
		echo $s; die;
	}

	public function redirect($uri){
		header('Location: '.$uri, TRUE, 200);
	}

	public function encrypt($s){
		return md5(B_SALT . $s . E_SALT);
	}
}
?>