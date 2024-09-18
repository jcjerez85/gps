<?php
$id = intval($_GET['id']);
$type = $_GET['type'];
if ($type !== 'logo' && $type !== 'logo-main' && $type !== 'favicon' && $type !== 'background')
    $type = 'logo';

if ($id > 0)
    $logo = current(glob('/var/www/html/images/logos/'.$type.'-'.$id.'.*'));
else
    $logo = current(glob('/var/www/html/images/'.$type.'.*'));

$filename = basename($logo);
$file_extension = strtolower(substr(strrchr($filename,"."),1));
switch( $file_extension ) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "ico": $ctype="image/x-icon"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    case "svg": $ctype="image/svg+xml"; break;
    default:
}

header('Content-type: ' . $ctype);
header("Content-Length: " . filesize($logo));

// dump the picture and stop the script
readfile($logo);
exit;
