<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 100 2009-01-26 15:15:26Z Raymond_Benc $
 */
class Poke_Component_Ajax_Ajax extends Phpfox_Ajax
{

	public function poke()
	{		
		$this->setTitle(_p('poke'));
		
		Phpfox::getBlock('poke.poke');

		echo '<script type="text/javascript">$Core.loadInit();</script>';

	}
	
	public function doPoke()
	{
		if (!Phpfox::getUserParam('poke.can_poke'))
		{
			return Phpfox_Error::display(_p('you_are_not_allowed_to_send_pokes'));
		}
		if (Phpfox::getUserParam('poke.can_only_poke_friends') &&
            Phpfox::isModule('friend') && !Friend_Service_Friend::instance()->isFriend(Phpfox::getUserId(), $this->get('user_id')))
		{
			return Phpfox_Error::display(_p('you_can_only_poke_your_own_friends'));
		}
		
		
		if (Poke_Service_Process::instance()->sendPoke($this->get('user_id')))
		{
			/* Type 1 is when poking back from the display block*/
			if ($this->get('type') == '1')
			{
				$this->call('$("#poke_'.$this->get('user_id') .'").hide().remove();');
			}
			else
			{
				$this->call('$("#liPoke").hide().remove();');
				$this->alert(_p('poke_sent'));
			}			
		}
		else
		{
			$this->alert(_p('poke_could_not_be_sent'));
		}
		
		list($iTotalPokes, $aPokes) = Poke_Service_Poke::instance()->getPokesForUser(Phpfox::getUserId());
		if (!$iTotalPokes)
		{
			$this->call('$("#js_block_border_poke_display").remove();');
		}
		else
		{
			$this->call('$("#poke_'.$this->get('user_id') .'").hide().remove();');
		}
        return null;
	}
	
	public function ignore()
	{
		Phpfox::isUser(true);
        Poke_Service_Process::instance()->ignore($this->get('user_id'));
		
		list($iTotalPokes, $aPokes) = Poke_Service_Poke::instance()->getPokesForUser(Phpfox::getUserId());
		if (!$iTotalPokes)
		{
			$this->call('$("#js_block_border_poke_display").remove();');
		}
		else
		{
			$this->call('$("#poke_'.$this->get('user_id') .'").hide().remove();');
		}
	}
	
	public function viewMore()
	{
		Phpfox::isUser(true);
		Phpfox::getBlock('poke.display');
		$this->html('#js_block_border_poke_display .content', $this->getContent(false));
	}
}