<?php 
header('Content-Type: text/xml');

$output = file_get_contents($auto_builder->getPublishCfgFilePath());

echo $output;

