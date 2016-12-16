<?php

$path = flavor()->active->path;
$sThemeFile = $path . 'html/' . $sTemplate . '.html.php';
if (file_exists($sThemeFile)) {
	$sFile = $sThemeFile;
}