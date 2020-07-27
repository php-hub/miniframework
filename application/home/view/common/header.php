<?php
// get utm
if(!empty(input("get.utm_source")) || !empty(input("get.utm_medium")) || !empty(input("get.utm_campaign"))){
	$utm ='';
	$utm .= input("get.utm_source")!=''?input("get.utm_source"):'default';
	$utm .= input("get.utm_medium")!=''?'|'.input("get.utm_medium"):'|default';
	$utm .= input("get.utm_campaign")!=''?'|'.input("get.utm_campaign"):'|default';
	$_SESSION["utm"] = $utm;
}else{
	$_SESSION["utm"] = (empty($_SESSION["utm"]))?"default|default|default":$_SESSION["utm"];
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>sasatinnie</title>
    <meta name="description" content="">
    <link rel="stylesheet" href="<?= __CONTENT__ ?>assets/css/style.css">
  </head>
  <body>