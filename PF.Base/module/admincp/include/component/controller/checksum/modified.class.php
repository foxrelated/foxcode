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
 * @package  		Module_Admincp
 * @version 		$Id: index.class.php 2831 2011-08-12 19:44:19Z Raymond_Benc $
 */
class Admincp_Component_Controller_Checksum_Modified extends Phpfox_Component {

    public function process() {

        $check = array();
        $failed = 0;

        if ($this->request()->get('check')) {
            $this->template()->assign('check', true);
            $lines = file(PHPFOX_DIR_INCLUDE . 'checksum' . PHPFOX_DS . 'md5');
            foreach ($lines as $line) {
                $line = trim($line);
                $parts = explode(' ', $line, 2);
                $file = PHPFOX_PARENT_DIR . trim($parts[1]);
                $file_name = trim($parts[1]);

                if ($file_name == 'PF.Base/include/checksum/md5') {
                    continue;
                }

                $message = '';
                $has_failed = false;
                $file = str_replace('/', PHPFOX_DS, $file);

                if (!file_exists($file)) {
                    $message = 'MISSING';
                    $failed++;
                    $has_failed = true;
                } else {
                    if (md5(file_get_contents($file)) == $parts[0]) {

                    } else {
                        $message = 'MODIFIED <!-- ' . md5(file_get_contents($file)) . ' vs ' . $parts[0] . ' -->';
                        $failed++;
                        $has_failed = true;
                    }
                }

                if ($has_failed) {
                    $check[$file_name] = $message;
                }
            }
        }

        $this->template()->setTitle(_p('checking_modified_files'))
            ->setSectionTitle(_p('modified_files'))
            ->assign(array(
                'url' => $this->url()->makeUrl('admincp.checksum.modified', ['check' => true]),
                'files' => $check,
                'failed' => $failed
            ));
    }

}