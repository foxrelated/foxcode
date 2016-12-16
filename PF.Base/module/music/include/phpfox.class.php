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
 * @package  		Module_Music
 * @version 		$Id: phpfox.class.php 1166 2009-10-09 11:38:32Z Raymond_Benc $
 */
class Module_Music
{
    public static $aTables = [
        'music_album',
        'music_album_text',
        'music_genre',
        'music_profile',
        'music_song',
    ];
    
    public static $aInstallWritable = [
        'file/music/',
        'file/pic/music/'
    ];
}