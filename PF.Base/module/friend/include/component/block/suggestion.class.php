<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: suggestion.class.php 3461 2011-11-07 10:39:23Z Miguel_Espinoza $
 */
class Friend_Component_Block_Suggestion extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!Phpfox::getParam('friend.enable_friend_suggestion'))
		{
			return false;
		}
		
		if ($this->getParam('reload'))
		{
            Friend_Service_Suggestion::instance()->reBuild();
		}
		
		$aSuggestion = Friend_Service_Suggestion::instance()->getSingle();

		if ($aSuggestion === false)
		{			
			if (PHPFOX_IS_AJAX)
			{
				echo '<div class="extra_info">' . _p('we_are_unable_to_find_any_friends_to_suggest_at_this_time_once_we_do_you_will_be_notified_within_our_dashboard') . '</div>';
				return null;
			}			
						
			return false;
		}			
					
		$this->template()->assign(array(
				'sHeader' => _p('suggestions'),
				'sDeleteBlock' => 'friend_suggestion',
				'aSuggestion' => $aSuggestion,
				'aFooter' => array(
					_p('view_all') => $this->url()->makeUrl('friend.suggestion')
				)
			)
		);	
		
		if (!PHPFOX_IS_AJAX)
		{
			return 'block';
		}
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_suggestion_clean')) ? eval($sPlugin) : false);
	}
}