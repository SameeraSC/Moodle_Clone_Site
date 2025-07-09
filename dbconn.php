

<?php

$servername="localhost";
$username="root";
$password="";
$dbname="bitlessons";


$conn= new mysqli($servername,$username,$password,$dbname);

if($conn===false){

	die("error could not connect".mysql_errno() );

} 


?>