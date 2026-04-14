<?php
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
function getHandleByLink($link){ // Get the handle (unique identifier) of an article from its absolute URL
    $url_elements = explode('=', $link); // Split using '=' as the separator
    return $url_elements[count($url_elements)-1]; // Return the last item (after the last '=')
}
function getArticleByHandle($handle){ // Get an article in JSON format by its handle (unique identifier)
    $feed = getFeedAsJSON(); // Get the entire XML feed in JSON format
    foreach ($feed['articles'] as $article){ // For every article in the feed
        if ($handle == getHandleByLink($article['link'])){ // If the article's handle is the one passed
            return $article; // Return the article in JSON format
        }
    }
}
function limitText($text, $limit){ // Cut text off beyond specified character limit
    $limited_text = trim(substr($text, 0, $limit)); // Trim the text according to the passed limit. Use trim to remove trailing spaces
    if ($limited_text != $text){ // If the trimmed text is different to the original (it was actually trimmed)
        $limited_text .= '...'; // Add an ellipsis so it's clear that it's been trimmed
    }
    return $limited_text;
}
function renderArticleAsHTML($article, $limit=128){ // Render an article in JSON format as HTML with a given character limit
    if ($article != null){
        echo "<article>"; // Use HTML <article> tags for SEO
        $link = '?article='.getHandleByLink($article['link']); // Generate dynamic link to the full article (unlimited) view
        echo "<h2><a href='{$link}'>{$article['title']}</a></h2>"; // Render as HTML link using the title for the anchor text
        $date = date('D M Y - H:i',strtotime($article['date'])); // Format the date into a nicer format
        echo "<time datetime='{$article['date']}' pubdate='pubdate'>{$date}</time>"; // Use HTML <time> tag for SEO
        $description = limitText($article['description'], $limit); // Generate the body (description) of the article, limited to $limit characters long
        echo "<p>{$description}</p>"; // Render the post body (description) as an HTML paragraph
        echo "</article>";
    } else {
        echo "<h2>That article doesn't exist :(</h2><a href='?'>Go home</a>"; // If the article doesn't exist (null passed instead of JSON)
    }
}
function renderFeedAsHTML($article=null){ // Render all articles in feed or just one article if passed
    $feed = getFeedAsJSON(); // Get the entire XML feed in JSON format
    echo "<h1>{$feed['title'][0][1]}</h1>"; // Render the overall feed (channel) title as the top level heading
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
    if (isset($_GET['article']) && $_GET['article'] != ''){ // If the article has been set and the article isn't blank
		renderFeedAsHTML(getArticleByHandle($_GET['article'])); // Render a specific article (with no character limit)
    } else {
		renderFeedAsHTML(); // Render the whole feed (if a specific article isn't set)
    }
?>
	</div>
    </body>
</html>
