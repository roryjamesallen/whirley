# Whirley
A very simple set of files to allow anyone to host their own RSS blog. The files here are meant to be 'hacked' to display/edit the feed exactly how you want - its aim is to be a quick start way of adding an RSS compatible blog feed to your website which can then be customised to any extent.
## Quick Start
1. Download this repository as a `.zip` and extract it.
3. Open `admin/index.php` in a text editor of your choice.
4. Edit the value of `$blog_url` to the URL your blog will be accessed at (has to be the same as where it's actually placed in the later step). This makes sure each post's URL in the feed XML is correct.
5. Edit the value of `$password_hash` to the hashed version of your chosen password using a site like [onlinephp.io](https://onlinephp.io/password-hash). This means the site never 'knows' your real password, and even vulnerabilities allowing direct download won't allow people to access the admin page.
6. Save the file and exit.
7. Open `rss.xml` in a text editor of your choice.
8. Edit the `<title>Blog</title>` tag to set the name of the blog itself e.g. `<title>Rory's Blog</title>`.
9. Save the file and exit.
10. Copy (`scp` or other method) the files into the directory you want to access your feed at e.g. `public_html/blog` to access at `yoursite.com/blog`.
11. Check it all works by going to the blog URL you cloned into/set in the PHP file.
## The Files
- `LICENSE.md` (The license this project is under)
- `README.md` (This explanatory document)
- `index.php` (The page that people can read your blog at in HTML form)
- `style.css` (How index.php looks, you can customise this to your heart's content or edit `index.php` to use a stylesheet you already have)
- `rss.xml` (The raw RSS feed that people can subscribe to, you shouldn't edit this manually other than to set the channel `<title>`!)
- `/admin/index.php` (An HTML GUI to submit new posts through)
### Using Individual Files
You can also use individual files from this repository, for example only using `index.php` to display an existing RSS feed or only using `admin/index.php` to all GUI posting to an RSS feed which needs no HTML display or is displayed using another method.
### The Name
Whirley is the part of [Macclesfield](https://en.wikipedia.org/wiki/Macclesfield) that I'm from, and I went to Whirley Primary School, I just thought it was a cute name!
