<?php

if (request()->get('force-flavor')) {
	flavor()->set_active(request()->get('force-flavor'));
}