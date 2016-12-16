<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: top.html.php 5616 2013-04-10 07:54:55Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aTopBloggers)}
<ul>
{foreach from=$aTopBloggers item=aTopBlogger}
	<li>
<!--        {$aTopBlogger.full_name} {if Phpfox::getParam('blog.display_post_count_in_top_bloggers')} ({$aTopBlogger.top_total}){/if}-->
        <div class="forum_row checkRow table_row">
            <div class="forum_image">
                <div class="forum_image_holder">
                    {img user=$aTopBlogger suffix='_50_square'}
                </div>
            </div>
            <div class="forum_title">
                <div class="forum_title_inner_holder">
                    <header>
                        {$aTopBlogger|user}
                        {if Phpfox::getParam('blog.display_post_count_in_top_bloggers')}
                        <div class="forum_user_details">
                            <time>{$aTopBlogger.top_total} {_p('blog(s)')}</time>
                        </div>
                        {/if}
                    </header>
                </div>
            </div>

        </div>
    </li>
{/foreach}
</ul>
{/if}