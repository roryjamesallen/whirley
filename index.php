<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function getFeedAsJSON(){
    $feed = array('title'=>'','articles'=>[]); // Default values prevent errors when feed is empty
    $xml = file_get_contents('rss.xml'); // Read the XML file containing the RSS feed itself
    $articles_regex = '/<item>[\s\S]*?
<title>(?<title>.*?)<\/title>[\s\S]*?
<link>(?<link>.*?)<\/link>[\s\S]*?
<description>(?<description>.*?)<\/description>[\s\S]*?
<pubDate>(?<date>.*?)<\/pubDate>[\s\S]*?
<\/item>/'; // Search the XML to find the key data for each article
    $title_regex = '/<channel>[\s\S]*?<title>(?<title>.*?)<\/title>[\s\S]*?<\/channel>/';
    foreach (array('articles'=>$articles_regex,'title'=>$title_regex) as $key=>$regex){
	if (preg_match_all($regex, $xml, $matches, PREG_SET_ORDER)){ // As long as at least one match is found
	    $feed[$key] = $matches; // Array of objects, each object has a 'title', 'link', and 'description' key=>value pair
	}
    }
    return $feed;
}
function renderArticleAsHTML($article){
    echo "<article>"; // Use HTML <article> tags for SEO
    echo "<h2>{$article['title']}</h2>";
    $date = date('D M Y - H:i',strtotime($article['date'])); // Format the date into a nicer format
    echo "<time datetime='{$article['date']}' pubdate='pubdate'>{$date}</time>"; // Use HTML <time> tag for SEO
    echo "<p>{$article['description']}</p>";
    echo "</article>";
}
function renderFeedAsHTML(){
    $feed = getFeedAsJSON();
    echo "<h1>{$feed['title'][0][1]}</h1>";
    echo "<a class='feed-link' href='rss.xml'>RSS Feed</a>"; // Provide a link to the raw RSS feed
    foreach ($feed['articles'] as $article){
	renderItemAsHTML($article);
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
