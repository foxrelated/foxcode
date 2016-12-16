<?php

if (request()->segment(2) == 'v') {
	$sSection = '@App/PHPfox_Videos';
	$bIsSubSection = true;
}