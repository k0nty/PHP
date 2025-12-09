<?php

require 'User.php';

$user = new User("Denys", "denysprokopiuk@gmail.com"); 

echo "<h3>{$user->getInfo()}</h3>";

?>