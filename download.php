<?php
$filePath = $_GET['file'];
$file = basename($filePath);

if(!$file){ // file does not exist
    die('file not found');
} else {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='. $file);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));

    ob_clean();
    flush();
    readfile($filePath);
    exit;
}