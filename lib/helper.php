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
            $s = addslashes($s);
            $s = addcslashes($s, '%_');
        }

        return $s;
    }

    public function sendMail($msg){
        $to       = 'vermerschth@gmail.com';
        $subject  = 'Testing sendmail.exe';
        $headers  = 'From: hids.epsi@gmail.com' . "\r\n" .
                    'MIME-Version: 1.0' . "\r\n" .
                    'Content-type: text/html; charset=utf-8';

        if(mail($to, $subject, $msg, $headers))
            echo "Email sent";
        else
            echo "Email sending failed";

        die;
    }
}
?>