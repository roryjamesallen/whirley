# Whirley
A very simple set of files to allow anyone to host their own RSS blog.
## The Files
- `LICENSE.md` (The license this project is under)
- `README.md` (This explanatory document)
- `index.php` (The page that people can read your blog at
- `style.css` (How index.php looks, you can customise this to your heart's content)
- `rss.xml` (The raw RSS feed that people can subscribe to, you shouldn't edit this manually!)
- `/admin/index.php` (The login/editor for you to submit new posts or edit old ones)
## Using Whirley
The easiest way to use Whirley if you're familiar with Git is to clone this repository into the root directory e.g. `public_html` of your website files. If you want it to be accessible at a different url, just use that url in the clone command.
For example:
1. Access your web server's files using SSH or through the cPanel terminal
2. `cd` into `public_html` or whatever the root directory is
3. Run `git clone git@github.com:roryjamesallen/whirley.git blog`
4. This will clone this repository into a new directory called `blog` meaning if you navigate to `yourwebsite.com/blog`, you will be served `index.php` from inside this repository, which will present a pretty HTML representation of the contents of `rss.xml`
### Using Individual Files
You can also use individual files from this repository, for example only using `index.php` to display another RSS feed (you can even edit the `$feed_url` variable to set it to an external url), or only using `/admin/index.php` to edit an RSS feed which needs no HTML display or is displayed using another method.
