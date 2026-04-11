<?php

$blog_url = 'https://hogwild.uk/blog';
$password_hash = '$2y$10$ZSHhR1CkeXoifijLc1fy3uUZOicCxnR8NJhsewwFdFrslFULpdpSy';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

function renderMessages(){
    $error = $message = '';
    if (isset($_GET["error"])){
	$error = $_GET["error"];
    } else if (isset($_GET["message"])){
	$message = $_GET["message"];
    }
    return '<span class="error">'.$error.'</span>
    <span class="success-message">'.$message.'</span>'; // Will have display: none set if empty
}
function checkFieldsFilled($input_names){ // For an array of input names, check if the POST data was filled and not blank
    foreach ($input_names as $input_name){ // For every input name in the array
        if (!isset($_POST[$input_name]) or $_POST[$input_name] == ''){ // If the input wasn't set or was blank
            header("Location: ?error=Fill+in+all+fields"); // Refresh with error message at the first failure
	    die(); // Prevent further PHP processing
        }
    }
}
function convertArticleToXML(){
    global $blog_url;
    $article_xml = "<item><link>{$blog_url}?article={$_POST['link']}</link>";
    foreach(['title','description','pubDate'] as $tag){
	$content = htmlspecialchars($_POST[$tag], ENT_QUOTES); // Make sure there's no HTML content or other weirdness
        $article_xml .= "<{$tag}>{$content}</{$tag}>"; // Use the input names as XML tag names
    }
    $article_xml .= '</item>';
    return $article_xml;
}
function submitArticle(){
    $xml = file_get_contents('../rss.xml');
    $article_xml = convertArticleToXML();
    $new_post_position = strpos($xml, '<item>'); // Insert before the first (newest) item already there
    if ($new_post_position == 0){ // No items yet, insert before end of channel
	$new_post_position = strpos($xml, '</channel>');
    }
    $xml = substr_replace($xml, convertArticleToXML(), $new_post_position, 0); // Insert the new post into the XML
    file_put_contents('../rss.xml', $xml);
    header("Location: ?message=Post+submitted+successfully");
}

$show_login = true;
if (isset($_SESSION['logged_in'])){ // Already logged in
    $show_login = false;
    if (isset($_POST['submit'])){
	checkFieldsFilled(['title','description','link']);
	submitArticle();
    }
} else if (isset($_POST['login'])){ // Pressed login but not yet successful
    checkFieldsFilled(['password']);
    global $password_hash;
    if (password_verify($_POST['password'], $password_hash)){
	$_SESSION['logged_in'] = true;
	header("Location: ?message=Logged+in+successfully");
    } else {
	header("Location: ?error=Incorrect+password");
    }
}
?>
<html>
    <head>
	<link rel="stylesheet" href="../style.css">
    </head>
    <body>
	<form class="feed-container" method="POST">
	    <?php
	    if (!$show_login){
		echo '<h1>Submit Post</h1>'.renderMessages().'
    <label for="pubDate-input">Date</label><input id="pubDate-input" name="pubDate" value="'.date(DATE_RSS, time()).'" readonly>
    <label for="link-input">URL Handle</label><input id="link-input" name="link" placeholder="my-post"
    <label for="title-input">Title</label><input id="title-input" name="title" placeholder="Post Title">
    <label for="post-input">Post</label><textarea id="post-input" name="description" placeholder="This post will say yaddaaa yaaadaa"></textarea>
    <input name="submit" type="submit" value="Post">';
	    } else {
		echo '<h1>Admin Login</h1>'.renderMessages().'<label for="password-input">Password</label><input id="password-input" name="password"><input name="login" type="submit" value="Log In">';
	    }
	    ?>
	</form>
    </body>
</html>
