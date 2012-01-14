<?php 
// PHP 4.1

$email = $_GET['ipn_email']; 
$header = ""; 
$emailtext = "Testing."; 
$eol = PHP_EOL;
$separator = md5(time());
$filename = 'ThisisAFile.pdf';
// main header (multipart mandatory)
$mailheaders  = "From: "."noreply@someplace.com".$eol;
$mailheaders .= "MIME-Version: 1.0".$eol; 
$mailheaders .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol.$eol; 
$mailheaders .= "Content-Transfer-Encoding: 7bit".$eol;
$mailheaders .= "This is a MIME encoded message.".$eol.$eol;

 $emailtext = '<html>
Put some kind of text here... or reference HTML from anoter file/location	
</html>';


// message
$mailheaders .= "--".$separator.$eol;
$mailheaders .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
$mailheaders .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
$mailheaders .= $emailtext.$eol.$eol;

// attachment
$mailheaders .= "--".$separator.$eol;
$mailheaders .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol; 
$mailheaders .= "Content-Transfer-Encoding: base64".$eol;
$mailheaders .= "Content-Disposition: attachment".$eol.$eol;
$mailheaders .= $attachment.$eol.$eol;
$mailheaders .= "--".$separator."--";

$attachment = chunk_split(base64_encode($filename));

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_name1 = $_POST['item_name1'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
if (!$fp) {
// HTTP ERROR
} else {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);
}
fclose ($fp);
}

//Check if it's all good then send an email.
if ($payment_status === "Completed" && ($item_name === "**ProductName/ID**" || $item_name1 === "**ProductName/ID**"))
{	
  mail($payer_email, "Order Confirmation", $emailtext , $mailheaders);
}

?>