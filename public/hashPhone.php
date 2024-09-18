<?php 
    header('Content-type: text/javascript');

    // connection to MySQL database
    $dbConnect['DB_HOSTNAME'] = 'localhost'; // database host
    $dbConnect['DB_PORT']     = '3306'; // database host
    $dbConnect['DB_NAME']     = 'gpswox_web'; // database name
    $dbConnect['DB_USERNAME'] = 'root'; // database user name
    $dbConnect['DB_PASSWORD'] = 'bksGNnRGnK81ioKepc55t7WjYQW76AuL'; // database password

    // connect to mysql  
    $ms = mysqli_connect($dbConnect['DB_HOSTNAME'], $dbConnect['DB_USERNAME'], $dbConnect['DB_PASSWORD'], $dbConnect['DB_NAME'], $dbConnect['DB_PORT']);
    if (!$ms) { echo "An unexpected error occurred in the database connection"; die; }

    // check number phone in Wox
    if ($_GET['check']=='number' and $_GET['whatsapp']!='' ) {
        $number=$_GET['whatsapp'];

        $q = "SELECT `api_hash`, `phone_number` FROM `users` WHERE `phone_number`='".$number."'";
        mysqli_set_charset($ms, "utf8"); //utf8 data format

        if(!$result = mysqli_query($ms, $q)) die();

        $list = array(); // create an array
        if($row = mysqli_fetch_array($result)) 
        { 
            echo $row['api_hash'];
        }
    }
    else{
        echo "parameters not found";
    }
?>
