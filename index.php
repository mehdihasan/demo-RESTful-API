<?php
$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
echo $actual_link."<br>\n";

$url_elements = explode('/', $_SERVER['PATH_INFO']);
print_r($url_elements);

?>