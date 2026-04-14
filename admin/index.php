<?php
$blog_url = 'https://yoursite.com/blog';
$password_hash = 'passwordhash';

session_start(); // Session data is used to store whether the user is logged in or not

function renderMessages(){ // Render error or success messages if present
    $error = $message = '';
    if (isset($_GET["error"])){ // An error message was passed as a GET parameter
        $error = $_GET["error"];
    } else if (isset($_GET["message"])){ // A success or other message was passed as a GET parameter
        $message = $_GET["message"];
    }
    return '<span class="error">'.$error.'</span>
    <span class="success-message">'.$message.'</span>'; // Display the error and/or success message(s)
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
    $article_xml = "<item><link>{$blog_url}?article={$_POST['link']}</link>"; // Generate the absolute link in XML form
    foreach(['title','description','pubDate'] as $tag){ // For each tag within the XML <item>
        $content = htmlspecialchars($_POST[$tag], ENT_QUOTES); // Make sure there's no HTML content or other weirdness
        $article_xml .= "<{$tag}>{$content}</{$tag}>"; // Use the input names as XML tag names
    }
    $article_xml .= '</item>';
    return $article_xml;
}
function submitArticle(){
    $xml = file_get_contents('../rss.xml'); // Get the raw RSS feed
    $article_xml = convertArticleToXML(); // Convert the POST article parameters into XML
    $new_post_position = strpos($xml, '<item>'); // Insert before the first (newest) item already there
    if ($new_post_position == 0){ // If there are no existing items in the feed
        $new_post_position = strpos($xml, '</channel>'); // Insert the new (first) item before the end of the channel
    }
    $xml = substr_replace($xml, convertArticleToXML(), $new_post_position, 0); // Insert the new post into the XML
    file_put_contents('../rss.xml', $xml); // Write the new feed back to the file
    header("Location: ?message=Post+submitted+successfully"); // GET redirect to prevent form resubmission
}

$show_login = true;
if (isset($_SESSION['logged_in'])){ // Already logged in
    $show_login = false;
    if (isset($_POST['submit'])){ // If the user has clicked submit to create a new post
        checkFieldsFilled(['title','description','link']); // Make sure all HTML form fields are filled
        submitArticle(); // Submit the new article content
    }
} else if (isset($_POST['login'])){ // Pressed login but not yet successful
    checkFieldsFilled(['password']); // Make sure the password field isn't blank
    global $password_hash;
    if (password_verify($_POST['password'], $password_hash)){ // Check the filled in password is correct
        $_SESSION['logged_in'] = true; // Setting true means that login form won't be shown on page reload
        header("Location: ?message=Logged+in+successfully"); // GET redirect to prevent resubmission
    } else {
        header("Location: ?error=Incorrect+password"); // GET redirect to prevent resubmission
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
