<?php
	phpinfo();
	exit;
	$url ="http://162.243.234.189/".$_GET['filename'];
    $content = file_get_contents($url);

    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($content));
    header('Content-Disposition: inline; filename="'.$_GET['filename'].'"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    ini_set('zlib.output_compression','0');

    die($content);
?>