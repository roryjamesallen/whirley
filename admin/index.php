<?php

$blog_url = '';
$password_hash = '$2y$10$ZSHhR1CkeXoifijLc1fy3uUZOicCxnR8NJhsewwFdFrslFULpdpSy';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

function validateInputs($input_names){
    foreach ($input_names as $input_name){
        if (!isset($_POST[$input_name]) or $_POST[$input_name] == ''){
            header("Location: ?error=Fill+in+all+fields");
        }
    }
}
function postToXML(){
    $xml_post = '<item>';
    foreach(['title','link','description','pubDate'] as $tag){
        $xml_post .= "<{$tag}>{$_POST[$tag]}</{$tag}>";
    }
    $xml_post .= '</item>';
    return $xml_post;
}
function submitPost(){
    $xml = file_get_contents('../rss.xml');
    $post_xml = postToXML();
    $new_post_position = strpos($xml, '<item>'); // Insert before the first (newest) item already there
    $xml = substr_replace($xml, postToXML(), $new_post_position, 0); // Insert the new post into the XML
    file_put_contents('../rss.xml', $xml);
    header("Location: ?message=Post+submitted+successfully");
}

$show_login = true;
if (isset($_SESSION['logged_in'])){ // Already logged in
    $show_login = false;
    if (isset($_POST['submit'])){
        validateInputs(['title','description']);
        submitPost();
    }
} else if (isset($_POST['login'])){ // Pressed login but not yet successful
    validateInputs(['password']);
    global $password_hash;
    if ($_POST['password'] == $password_hash){
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
    if ($show_login){
        echo '<h1>Submit Post</h1>
	    <span class="error"><?php if (isset($_GET['error'])){ echo $_GET['error']; } ?></span>
	    <span class="success-message"><?php if (isset($_GET['message'])){ echo $_GET['message']; } ?></span>
	    <label for="pubDate-input">Date</label><input id="pubDate-input" name="pubDate" value="<?php echo date(DATE_RSS, time()); ?>" readonly>
	    <label for="link-input">URL Handle</label><input id="link-input" name="link" placeholder="my-post"
	    <label for="title-input">Title</label><input id="title-input" name="title" placeholder="Post Title">
	    <label for="post-input">Post</label><textarea id="post-input" name="description" placeholder="This post will say yaddaaa yaaadaa"></textarea>
	    <input name="submit" type="submit">;
    } else {
        echo '<label for="password-input">Password</label><input id="password-input" name="password">';
    }
?>
	</form>
    </body>
</html>
