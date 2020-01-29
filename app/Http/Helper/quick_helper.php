<?php

function printR($array, $description = '-------'){
  echo "<br>".$description;
  echo "<pre>";
  print_r($array);
  echo "</pre>\n";
}

function strToPascal($string){
  return str_replace('_', '', ucwords($string, '_'));
}
function isset2($key, $array){
  return array_key_exists($key, $array);
}
function console($message){
  echo ">>> ".$message."<br>";
}