<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function getFeedAsJSON(){
    $feed = array('title'=>'Blog Title','articles'=>[]); // Default values prevent errors when feed is empty
    $xml = file_get_contents('rss.xml'); // Read the XML file containing the RSS feed itself
    $articles_regex = '/<item>[\s\S]*?<link>(?<link>.*?)<\/link>[\s\S]*?<title>(?<title>[\s\S]*?)<\/title>[\s\S]*?<description>(?<description>[\s\S]*?)<\/description>[\s\S]*?<pubDate>(?<date>.*?)<\/pubDate>[\s\S]*?<\/item>/';
    $title_regex = '/<channel>[\s\S]*?<title>(?<title>.*?)<\/title>[\s\S]*?<\/channel>/';
    foreach (array('articles'=>$articles_regex,'title'=>$title_regex) as $key=>$regex){
	if (preg_match_all($regex, $xml, $matches, PREG_SET_ORDER)){ // As long as at least one match is found
	    $feed[$key] = $matches; // Array of objects, each object has a 'title', 'link', and 'description' key=>value pair
	}
    }
    return $feed;
}
function getHandleByLink($link){
    $url_elements = explode('=', $link);
    return $url_elements[count($url_elements)-1];
}
function getArticleByHandle($handle){
    $feed = getFeedAsJSON();
    foreach ($feed['articles'] as $article){
	if ($handle == getHandleByLink($article['link'])){
	    return $article;
	}
    }
}
function limitText($text, $limit){
    $limited_text = trim(substr($text, 0, $limit));
    if ($limited_text != $text){
	$limited_text .= '...';
    }
    return $limited_text;
}
function renderArticleAsHTML($article, $limit=128){
    if ($article != null){
	echo "<article>"; // Use HTML <article> tags for SEO
	$link = '?article='.getHandleByLink($article['link']);
	echo "<h2><a href='{$link}'>{$article['title']}</a></h2>";
	$date = date('D M Y - H:i',strtotime($article['date'])); // Format the date into a nicer format
	echo "<time datetime='{$article['date']}' pubdate='pubdate'>{$date}</time>"; // Use HTML <time> tag for SEO
	$description = limitText($article['description'], $limit);
	echo "<p>{$description}</p>";
	echo "</article>";
    } else {
	echo "<h2>That article doesn't exist :(</h2><a href='?'>Go home</a>";
    }
}
function renderFeedAsHTML($article=null){
    $feed = getFeedAsJSON();
    echo "<h1>{$feed['title'][0][1]}</h1>";
    echo "<a class='feed-link' href='rss.xml'>RSS Feed</a>"; // Provide a link to the raw RSS feed
    if ($article == null){ // If no article has been passed
	foreach ($feed['articles'] as $article){ // Render every article in the feed
	    renderArticleAsHTML($article);
	}
    } else {
	renderArticleAsHTML($article, $limit=null); // Only render the passed article
    }
}
?>
<html>
    <head>
	<link rel="stylesheet" href="style.css">
    </head>
    <body>
	<div class="feed-container">
	    <?php
	    if (isset($_GET['article']) && $_GET['article'] != ''){
		renderFeedAsHTML(getArticleByHandle($_GET['article'])); // Render a specific article (with no character limit)
	    } else {
		renderFeedAsHTML(); // Render the whole feed
	    }
	    ?>
	</div>
    </body>
</html>
