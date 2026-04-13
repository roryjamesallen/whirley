# Whirley
A very simple set of files to allow anyone to host their own RSS blog.
## Quick Start
1. SSH into your web server or open the terminal in cPanel (if you don't know how to do this and aren't confident trying, sorry this probably isn't the project for you!).
2. Clone this repository using the directory name you want your feed to be accessible at e.g. to make a blog at `yoursite.com/blog`, navigate into your root directory (probably `public_html`) and run `git clone git@github.com:roryjamesallen/whirley.git blog`.
3. Open `admin/index.php` in a text editor of your choice.
4. Edit the value of `$blog_url` to the URL your blog will be accessed at (has to be the same as where it's cloned). This makes sure each post's URL in the feed XML is correct.
5. Edit the value of `$password_hash` to the hash output from a site like [onlinephp.io](https://onlinephp.io/password-hash). This means the site never 'knows' your real password, and even vulnerabilities allowing direct download won't allow people to access the admin page.
6. Save the file and exit.
7. Open `rss.xml` in a text editor of your choice.
8. Edit the `<title>Hog Feed</title>` tag to set the name of the blog itself.
9. Save the file and exit.
10. Check it all works by going to the blog URL you cloned into/set in the PHP file.
## The Files
- `LICENSE.md` (The license this project is under)
- `README.md` (This explanatory document)
- `index.php` (The page that people can read your blog at
- `style.css` (How index.php looks, you can customise this to your heart's content)
- `rss.xml` (The raw RSS feed that people can subscribe to, you shouldn't edit this manually!)
- `/admin/index.php` (The login/editor for you to submit new posts or edit old ones)
### Using Individual Files
You can also use individual files from this repository, for example only using `index.php` to display an existing RSS feed or only using `admin/index.php` to all GUI posting to an RSS feed which needs no HTML display or is displayed using another method.
