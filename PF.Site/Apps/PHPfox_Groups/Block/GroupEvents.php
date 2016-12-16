<?php
namespace Apps\PHPfox_Groups\Block;
use Phpfox;
use Phpfox_Component;
use Event_Service_Event;
use Core;

defined('PHPFOX') or exit('NO DICE!');

class GroupEvents extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isModule('event')) {
            return false;
        }

        $page = $this->getParam('aPage');
        if (!Phpfox::isAdmin() && !Core\Lib::appsGroup()->isMember($page['page_id']) && in_array($page['reg_method'], [1, 2])){
            return false;
        }
        $events = Event_Service_Event::instance()->getForParentBlock('groups', $page['page_id']);
        if (!$events) {
            return false;
        }

        $this->template()->assign([
            'sHeader' => _p('Group Events'),
            'events'  => $events,
        ]);

        return 'block';
    }
}