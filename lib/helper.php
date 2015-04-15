<?php
abstract class helper{
	public function debug_s($s){
		echo $s; die;
	}

	public function redirect($uri){
		header('Location: ' . BASE_URI . $uri);
	}

	public function encrypt($s){
		return md5(B_SALT . $s . E_SALT);
	}

    public function securiseString($s){
        $s = htmlentities($s);
        $s = htmlspecialchars($s);

        if(ctype_digit($s))
        {
            $s = intval($s);
        }
        else
        {
            $s = mysql_real_escape_string($s);
            $s = addcslashes($s, '%_');
        }

        return $s;
    }
}
?>