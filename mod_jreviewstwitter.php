<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jreviewstwitter
 *
 * @copyright   Copyright (C) 2009 - 2016 Open Source Technologies, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname ( __FILE__ ) . '/TwitterAPIExchange.php');

//get params
$twitterid = trim($params->get( 'username' ));
$fieldname = trim($params->get( 'fieldname',' ' ));
$tw_count= $params->get( 'tweetnumber' ,20);
$dyn_twitterid=0;

$settings = array(
    'oauth_access_token' => trim($params->get( 'access_token' )),
    'oauth_access_token_secret' => trim($params->get( 'access_secret' )),
    'consumer_key' => trim($params->get( 'consumer_key' )),
    'consumer_secret' => trim($params->get( 'consumer_secret' ))
);

 $op=JFactory::getApplication()->input->get('option');
 $uid=JFactory::getApplication()->input->get('id');
 
		if($op == 'com_content' && $uid)
	{
		$id = preg_replace("/[^0-9]/","",$uid);
		$db	= JFactory::getDBO();		
		@$db->setQuery("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME = '".$fieldname."' AND table_name = '".$db->getPrefix()."jreviews_content' LIMIT 1;");
		@$db->query();
		$column = $db->loadObjectList();		
		if($column){	
					$query= "SELECT ".$fieldname." FROM #__jreviews_content WHERE contentid = ".$id ;	 
					$db->setQuery($query);
					$id = $db->loadAssoc();
			if(count($id))
							$dyn_twitterid = $id[$fieldname];
					}
	
	}
  
  
	if($dyn_twitterid)
	 $twitterid	= $dyn_twitterid;

if($twitterid){
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$getfield = '?screen_name='.$twitterid.'&count='.$tw_count;
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	$rs=json_decode($twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest());

	echo'<style>span.tw strong {color: #0B78A1 !important;}</style>';


if(! isset($rs->errors)):
foreach($rs as $ritem):  

$showtext = $ritem->text;  

$showtext = utf8_decode($showtext); 

$showtext = url2link($showtext); 

$status_timestamp = strtotime($ritem->created_at); 

$status_locdate = date("Y-m-d (H:i:s)",$status_timestamp); 

echo "<p style=\"text-align:left; width:auto; clear:both;background:none repeat scroll 0 0 #F5F5F5; color:#666666;padding:0.5%\">\r\n"; 

echo "<a href=\"http://twitter.com/".$ritem->user->screen_name."\" title=\"".$ritem->user->name."\" target=\"_blank\"><img src=\"".$ritem->user->profile_image_url."\" alt=\"".$ritem->user->name."\" align=\"left\" width=\"30\" height=\"30\" border=\"0\" style=\"padding:10px 8px 2px 0px;\" /></a>\r\n"; 
echo "<span class='tw'>".$showtext."</span>\r\n"; 
echo "<br />&nbsp;\r\n"; 
echo "<small style=\"padding:0px 12px 0px 0px; float:right;\">".$status_locdate."</small>\r\n"; 
echo "</p>\r\n"; 
endforeach; 
		else :
        echo "<P>Sorry, Twitter cannot be contacted. Try again soon.</P>"; 
	  endif; 
	  
  }
  else
  {
	         echo "<P>Sorry, Twitter cannot be contacted. Try again soon.</P>";  
  }
	  
	  
	  
	  
	  
	  function url2link ($string, $target="_blank") { 

preg_match_all('|(http://[^\s]+)|', $string, $matches1);

if($matches1) { 

foreach($matches1[0] as $match1) { 

$hypertext = "<a href=\"".$match1."\" target=\"".$target."\"><strong>".$match1."</strong></a>";

$string = str_replace($match1, $hypertext, $string);

}

}

preg_match_all('|(@[^\s]+)|', $string, $matches2);

if($matches2) { 

foreach($matches2[0] as $match2) { 

$hypertext = "<a href=\"http://twitter.com/".$match2."\" target=\"".$target."\"><strong>".$match2."</strong></a>";

$string = str_replace($match2, $hypertext, $string);

}

}

return $string;

}

?>

<style>
.tw  a{word-wrap:break-word;}
</style>
