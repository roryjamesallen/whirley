<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function getFeedAsJSON(){
    $feed = array();
    $xml = file_get_contents('rss.xml'); // Read the XML file containing the RSS feed itself
    foreach (array('items'=>'/<item>[\s\S]*?<title>(?<title>.*?)<\/title>[\s\S]*?<link>(?<link>.*?)<\/link>[\s\S]*?<description>(?<description>.*?)<\/description>[\s\S]*?<pubDate>(?<date>.*?)<\/pubDate>[\s\S]*?<\/item>/','title'=>'/<channel>[\s\S]*?<title>(?<title>.*?)<\/title>[\s\S]*?<\/channel>/') as $key=>$regex){
	if (preg_match_all($regex, $xml, $matches, PREG_SET_ORDER)){ // As long as at least one match is found
	    $feed[$key] = $matches; // Array of objects, each object has a 'title', 'link', and 'description' key=>value pair
	}
    }
    return $feed;
}
function renderItemAsHTML($item){
    echo "<article class='item'>";
    echo "<h2>{$item['title']}</h2>";
    $date = date('D M Y - H:i',strtotime($item['date']));
    echo "<time datetime='{$item['date']}' pubdate='pubdate'>{$date}</time>";
    echo "<p>{$item['description']}</p>";
    echo "</article>";
}
function renderFeedAsHTML(){
    $feed = getFeedAsJSON();
    echo "<h1>{$feed['title'][0][1]}</h1>";
    echo "<a class='feed-link' href='rss.xml'>RSS Feed</a>";
    foreach ($feed['items'] as $item){
	renderItemAsHTML($item);
    }
}
?>
<html>
    <head>
	<link rel="stylesheet" href="style.css">
    </head>
    <body>
	<div class="feed-container">
	    <?php renderFeedAsHTML(); ?>
	</div>
    </body>
</html>
