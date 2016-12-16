<?php
if (!defined('PHPFOX_PAGE_ITEM_TYPE_1')) {
    define('PHPFOX_PAGE_ITEM_TYPE_1', 'groups');
}

event('app_settings', function ($settings){
    if (isset($settings['pf_group_enabled'])) {
        Admincp_Service_Module_Process::instance()->updateActivity('groups', $settings['pf_group_enabled']);
    }
});

\Phpfox_Module::instance()->addServiceNames([
    'groups'          => '\Apps\PHPfox_Groups\Service\Groups',
    'groups.facade'   => '\Apps\PHPfox_Groups\Service\Facade',
    'groups.category' => '\Apps\PHPfox_Groups\Service\Category',
    'groups.process'  => '\Apps\PHPfox_Groups\Service\Process',
    'groups.type'     => '\Apps\PHPfox_Groups\Service\Type',
    'groups.api'      => '\Apps\PHPfox_Groups\Service\Api',
    'groups.browse'   => '\Apps\PHPfox_Groups\Service\Browse',
    'groups.callback' => '\Apps\PHPfox_Groups\Service\Callback',
])->addComponentNames('controller', [
    'groups.admincp.category'     => '\Apps\PHPfox_Groups\Controller\Admin\CategoryController',
    'groups.admincp.add-category' => '\Apps\PHPfox_Groups\Controller\Admin\AddCategoryController',
    'groups.admincp.convert' => '\Apps\PHPfox_Groups\Controller\Admin\ConvertController',
])->addTemplateDirs([
    'groups' => PHPFOX_DIR_SITE_APPS . 'PHPfox_Groups' . PHPFOX_DS . 'views',
])->addAliasNames('groups', 'PHPfox_Groups');

if (setting('pf_group_enabled')) {

    \Phpfox_Module::instance()->addComponentNames('block', [
        'groups.about'    => '\Apps\PHPfox_Groups\Block\GroupAbout',
        'groups.admin'    => '\Apps\PHPfox_Groups\Block\GroupAdmin',
        'groups.category' => '\Apps\PHPfox_Groups\Block\GroupCategory',
        'groups.events'   => '\Apps\PHPfox_Groups\Block\GroupEvents',
        'groups.members'  => '\Apps\PHPfox_Groups\Block\GroupMembers',
        'groups.menu'     => '\Apps\PHPfox_Groups\Block\GroupMenu',
        'groups.photo'    => '\Apps\PHPfox_Groups\Block\GroupPhoto',
        'groups.profile'  => '\Apps\PHPfox_Groups\Block\GroupProfile',
        'groups.widget'   => '\Apps\PHPfox_Groups\Block\GroupWidget',
        'groups.cropme'   => '\Apps\PHPfox_Groups\Block\GroupCropme',
    ])->addComponentNames('ajax', [
        'PHPfox_Groups.ajax' => '\Apps\PHPfox_Groups\Ajax\Ajax',
        'groups.ajax'        => '\Apps\PHPfox_Groups\Ajax\Ajax',
    ])->addComponentNames('controller', [
        'groups.index'   => '\Apps\PHPfox_Groups\Controller\IndexController',
        'groups.add'     => '\Apps\PHPfox_Groups\Controller\AddController',
        'groups.all'     => '\Apps\PHPfox_Groups\Controller\AllController',
        'groups.view'    => '\Apps\PHPfox_Groups\Controller\ViewController',
        'groups.profile' => '\Apps\PHPfox_Groups\Controller\ProfileController',
        'groups.widget'  => '\Apps\PHPfox_Groups\Controller\WidgetController',
        'groups.frame'   => '\Apps\PHPfox_Groups\Controller\FrameController',
    ]);
}

route('/groups/admincp/convert', function (){
    auth()->isAdmin(true);
    Phpfox_Module::instance()->dispatch('groups.admincp.convert');
    return 'controller';
});

group('/groups', function (){
    route('/admincp', function (){
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('groups.admincp.category');

        return 'controller';
    });

    route('/admincp/category/order', function (){
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[ $id ] = $key + 1;
        }
        Core_Service_Process::instance()->updateOrdering([
                'table'  => (request()->get('type') == 'main' ? 'pages_type' : 'pages_category'),
                'key'    => 'type_id',
                'values' => $values,
            ]
        );

        \Phpfox::getLib('cache')->remove('groups', 'substr');

        return true;
    });

    route('/admincp/add-category', 'groups.admincp.add-category');

    if (setting('pf_group_enabled')) {
        route('/category/:id/:name/*', 'groups.index')
            ->where([':id' => '([0-9]+)']);
        route('/sub-category/:id/:name/*', 'groups.index')
            ->where([':id' => '([0-9]+)']);
        route('/', 'groups.index');

        route('/profile/*', 'groups.view');

        route('/add/*', 'groups.add');

        route('/frame/*', 'groups.frame');

        route('/:name/*', 'groups.view')
            ->where([':name' => '([0-9]+)'])
            ->filter(function (){
                return true;
            });
    }
});