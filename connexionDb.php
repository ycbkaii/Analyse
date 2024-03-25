<?php

/**
 * @version 1.0
 * @author Yanis Chiouar, Baron Noah
 * @api dblp.org
 */

$host = "localhost";
$dbname = "postgres";
$user = "bkaii";
$password = "password";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if(!$conn){
    die("erreur de connexion :".pg_last_error());
}


?>