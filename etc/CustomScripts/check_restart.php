<?php
/**
 * Created by PhpStorm.
 * User: bpeters
 * Date: 2/9/2016
 * Time: 2:18 PM
 */


$subject = 'Nagios Service Restart Failure';
$headers = "From: DoNotReply@" . Config::$EmailDomain . "\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
$to = 'someone@email.com';
$body = '<br>';

$output = file(Config::$NagiosPath . 'etc/restart.log');

foreach ($output as $line) {
 $body .= $line . PHP_EOL;
}

mail($to, $subject, $body, $headers);

?>