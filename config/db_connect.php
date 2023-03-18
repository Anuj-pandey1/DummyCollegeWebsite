<?php 

	$conn = mysqli_connect('localhost', 'anuj', 'anuj', 'dean');

	if(!$conn){
		echo 'Connection error: '. mysqli_connect_error();
	}

?>