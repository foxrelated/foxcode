
function approvePoll(iPoll)
{
	$Core.jsConfirm({}, function(){
		$.ajaxCall('poll.moderatePoll','iResult=0&iPoll='+iPoll);
	}, function(){});
	return false;
	
}

function deletePoll(iPoll)
{
	$Core.jsConfirm({}, function(){
		$.ajaxCall('poll.moderatePoll','iResult=2&iPoll='+iPoll);
	}, function(){});
	return false;
}