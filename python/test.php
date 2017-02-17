<?php 
$hello = 'world';

$result = shell_exec('C:\Python27\python test.py ' . $hello);
var_dump ($result);
?>