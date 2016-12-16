if (typeof $Core.Pages == 'undefined') $Core.Pages = {};

$Core.Pages.Claim = 
{
	approve : function(iClaimId)
	{
		$Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_transfer_ownership']}, function() {
			$.ajaxCall('pages.approveClaim', 'claim_id=' + iClaimId);
		}, function(){});
	},
	
	deny : function(iClaimId)
	{
		$Core.jsConfirm({message: oTranslations['are_you_sure_you_want_to_deny_this_claim_request']}, function() {
			$.ajaxCall('pages.denyClaim', 'claim_id=' + iClaimId);
		}, function(){});
	}
};