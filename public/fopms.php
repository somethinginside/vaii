<?php

$connect = mysqli_connect('localhost','root','','unii');

    if (!$connect){
        die('Ошибка при подключении к Базе данных!');
    }

    $name = $_POST['name'];
    $age = $_POST['age'];
	$country = $_POST['country'];
	$why = $_POST['why'];
	$which = $_POST['which'];
    $email = $_POST['email'];
    
    $sql = mysqli_query($connect, "INSERT INTO `unicorn` (`id`, `name`, `age`, `country`, `why`, `which`, `email`) VALUES (NULL, '$name', '$age', '$country', '$why', '$which', '$email')");
	
	header("Location: tombola.html");
?>