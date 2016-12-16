<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

use MediaEmbed\MediaEmbed;

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Service
 * @version 		$Id: link.class.php 7240 2014-03-31 15:22:15Z Fern $
 */
class Link_Service_Link extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('link');	
	}

	private function get_remote_contents($sUrl){

        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER,[
            ''
        ]);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_MAXREDIRS,5);
        curl_setopt($ch, CURLOPT_TIMEOUT,1);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }
	
	public function getLink($sUrl)
	{
		if (substr($sUrl, 0, 7) != 'http://' && substr($sUrl, 0, 8) != 'https://')
		{
			$sUrl = 'http://' . $sUrl;
		}
			
		$aParts = parse_url($sUrl);	
				
		if (!isset($aParts['host']))
		{
			return Phpfox_Error::set(_p('not_a_valid_link'), true);
		}



		$aParseBuild = array();
        if (class_exists('DOMDocument')){
            $doc = new DOMDocument("1.0", 'utf-8');
            $html = $this->get_remote_contents($sUrl);

            // now we inject another meta tag
            $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
            $html = str_replace('<head>', '<head>' . $contentType, $html);
            @$doc->loadHTML($html);
            $titleList = $doc->getElementsByTagName("title");
            $metaList = $doc->getElementsByTagName("meta");
            foreach($metaList as $iKey => $meta) {
                $type = $meta->getAttribute('property');
                $content = $meta->getAttribute('content');
                $aParseBuild[$type] = $content;
            }
            if($titleList->length > 0){
                $aParseBuild['title'] =  $titleList->item(0)->nodeValue;
            } else {
                $aParseBuild['title'] = '';
            }
        } else {
            $sContent = Phpfox_Request::instance()->send($sUrl, array(), 'GET', $_SERVER['HTTP_USER_AGENT'], null, true);
            preg_match_all('/<(meta|link)(.*?)>/i', $sContent, $aRegMatches);
            if (preg_match('/<title>(.*?)<\/title>/is', $sContent, $aMatches)) {
                $aParseBuild['title'] = $aMatches[1];
            }
            else if (preg_match('/<title (.*?)>(.*?)<\/title>/is', $sContent, $aMatches) && isset($aMatches[2])) {
                $aParseBuild['title'] = $aMatches[2];
            }

            if (isset($aRegMatches[2]))
            {
                foreach ($aRegMatches as $iKey => $aMatch)
                {
                    if ($iKey !== 2)
                    {
                        continue;
                    }

                    foreach ($aMatch as $sLine)
                    {
                        $sLine = rtrim($sLine, '/');
                        $sLine = trim($sLine);

                        preg_match('/(property|name|rel|image_src)=("|\')(.*?)("|\')/is', $sLine, $aType);
                        if (count($aType) && isset($aType[3]))
                        {
                            $sType = $aType[3];
                            preg_match('/(content|type)=("|\')(.*?)("|\')/i', $sLine, $aValue);
                            if (count($aValue) && isset($aValue[3]))
                            {
                                if ($sType == 'alternate')
                                {
                                    $sType = $aValue[3];
                                    preg_match('/href=("|\')(.*?)("|\')/i', $sLine, $aHref);
                                    if (isset($aHref[2]))
                                    {
                                        $aValue[3] = $aHref[2];
                                    }
                                }
                                $aParseBuild[$sType] = $aValue[3];
                            }
                        }
                    }
                }
            }
        }
		$image = '';
		$embed = '';
		$MediaEmbed = new MediaEmbed();
		$MediaObject = $MediaEmbed->parseUrl($sUrl);
		if (!$MediaObject instanceof \MediaEmbed\Object\MediaObject) {
			if (isset($aParseBuild['og:image'])) {
				$image = $aParseBuild['og:image'];
			}
		}
		else {
			$image = $MediaObject->image();
			$embed = $MediaObject->getEmbedCode();
		}
		if (!$embed) {
			if (isset($aParseBuild['application/json+oembed'])) {
				$context = stream_context_create(
					['http' => ['header' => 'Connection: close',
						'user_agent'=> $_SERVER['HTTP_USER_AGENT']
					]]);
				$source = json_decode(preg_replace('/[^(\x20-\x7F)]*/', '', file_get_contents($aParseBuild['application/json+oembed'], 0, $context)));
				if (isset($source->html)) {
					$id = str_replace('fb://photo/', '', $aParseBuild['al:android:url']);
					$image = 'https://graph.facebook.com/' . $id . '/picture';
					$embed = '<div class="fb_video_iframe"><iframe src="https://www.facebook.com/video/embed?video_id=' . $id . '"></iframe></div>';
				}
			}
		}

		if (isset($aParseBuild['title'])) {
			$aParseBuild['og:title'] = $aParseBuild['title'];
			if (isset($aParseBuild['description'])) {
				$aParseBuild['og:description'] = $aParseBuild['description'];
			}
		}

		if (!$image && isset($aParseBuild['og:image'])) {
			$image = $aParseBuild['og:image'];
		}

		$aReturn =  [
			'link' => $sUrl,
			'title' => (isset($aParseBuild['og:title']) ? $aParseBuild['og:title'] : ''),
			'description' => (isset($aParseBuild['og:description']) ? $aParseBuild['og:description'] : ''),
			'default_image' => $image,
			'embed_code' => $embed
		];
        return $aReturn;
	}
	
	public function getEmbedCode($iId, $bIsPopUp = false)
	{
		$aLinkEmbed = $this->database()->select('embed_code')	
			->from(Phpfox::getT('link_embed'))
			->where('link_id = ' . (int) $iId)
			->execute('getSlaveRow');

		$iWidth = 640;
		$iHeight = 390;
		if (!$bIsPopUp)
		{
			$iWidth = 480;
			$iHeight = 295;
		}
		
		$aLinkEmbed['embed_code'] = preg_replace('/width=\"(.*?)\"/i', 'width="' . $iWidth . '"', $aLinkEmbed['embed_code']);
		$aLinkEmbed['embed_code'] = preg_replace('/height=\"(.*?)\"/i', 'height="' . $iHeight . '"', $aLinkEmbed['embed_code']);
		$aLinkEmbed['embed_code'] = str_replace(array('&lt;', '&gt;', '&quot;'), array('<', '>', '"'), $aLinkEmbed['embed_code']);

		if(Phpfox::getParam('core.force_https_secure_pages') && Phpfox::getParam('core.force_secure_site'))
		{
			$aLinkEmbed['embed_code'] = str_replace('http://', 'https://', $aLinkEmbed['embed_code']);
		}
		
		return $aLinkEmbed['embed_code'];
	}
	
	public function getLinkById($iId)
	{
		$aLink = $this->database()->select('l.*, u.user_name')
			->from(Phpfox::getT('link'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if (!isset($aLink['link_id']))
		{
			return false;
		}
		
		return $aLink;
	}
	
	/* Have'nt tested this */
	public function getInfoForAction($aItem)
	{
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		$aRow = $this->database()->select('l.link_id, l.title, l.user_id, u.gender, u.full_name')
			->from(Phpfox::getT('link'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
		
		$aRow['link'] = Phpfox_Url::instance()->permalink('link', $aRow['link_id'], $aRow['title']);
		return $aRow;
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('link.service_link__call'))
		{
			eval($sPlugin);
			return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}