<?php

return function(Phpfox_Installer $Installer) {
    $Installer->db->addField(['table' => Phpfox::getT('forum_access'), 'field' => 'access_id', 'type' => 'INT', 'null' => false, 'attribute' => 'AUTO_INCREMENT', 'first' => true]);
    $Installer->db->addPrimaryKey(Phpfox::getT('forum_access'), 'access_id');
};