# GainCMS

A PHP blog engine I wrote for my website and tech blog. I tried Jekyll and GravCMS which were close but not quite what I wanted in the end, but their influence can be seen on this work. If all you need is a quick and simple CMS/blog with pagination and comments supports or a simple responsive web site, this might be a good base. Not by any means "user-friendly" in the modern dummy end user sense, if you have some dev skills or experience with CLI, GainCMS should be trivial to use and takes only a few minutes to get into.

## Feature list

* Flat file blog CMS
* Responsive design (Bootstrap)
* Markdown articles (Parsedown)
* Disqus comments
* Pagination
* Tags
* PHP routing
* Simple and easy to modify

## Project goals

I made this CMS to have a simple yet flexible platform to write about things I've wanted to share. Some of these are for a very small audience (Linux audio for example), and some are just for my personal fun. Professionally, I wanted to have some experience on modern responsive web design. This CMS uses Bootstrap along with custom styles (e.g. responsive YouTube-video containers). I also wanted to see how routing works in practice.

## Structure explained

I keep my site on a server where I also host other stuff, therefore I wanted to have a tidy base directory structure. As I'm very familiar with Linux and the CLI world, I think using good old files and directories to your advantage is a good and clear idea. Below is a diagram of the structure, where + denotes a directory and - denotes a file. The first line of the diagram is the root directory.

```
+ /
 \
 |- .htaccess
 |- index.php
 |+ site/
   \
   |- about.php
   |- blog.php
   |- error.php
   |- home.php
   |- post.php
   |- projects.php
   |+ css/
   |+ files/
   |+ graphics/
   |+ includes/
   |+ tags/
   | \
   | |- tag_example
   | |- tag_software
   |
   |+ posts/
     \
     |+ 2015-08-26_example/
      \
      |- article.md
      |- intro.md
      |- tag_example
      |- tag_software
```

The *index.php* works only as a router. It breaks down the URL and shows the correct page (*about.php*, *blog.php* etc.). All requests are directed to *index.php* in *.htaccess*. Adding and removing subpages is therefore really simple and there's no magic involved. Basically one can write a new page from scratch and it will be shown just like that.

In the *includes* directory there are the common header (which also contains the navigation bar) and footer along with other utilities (e.g. a Parsedown extension). The *css*, *graphics* and *files* directories contain the style definitions, images and icons, and various other files respectively.

The *tags* directory contains tags which the website reader can use to filter blog content. They are just empty files, what matters is their name: `tag_<tag name>`.

Blog articles go to the *posts* directory. Every article is contained in its own subdirectory, whose name must begin with a date in the `YYYY-MM-DD` format. The article's directory name is also used as the ID for Disqus comments.

Each blog article consists of the article itself in *article.md*, a short intro in *intro.md* (shown in the blog post listing, *blog.php*), tags, and files/images if referred to in the article. The tags are again just empty files whose names should match those in the *tags* directory.

## License

GainCMS uses Parsedown and Bootstrap, which are [MIT](http://opensource.org/licenses/MIT) licensed. The social media share icons are CC by [Aha-Soft Team](http://www.aha-soft.com/free-icons/). GainCMS itself is [GPL-3.0](http://www.gnu.org/licenses/gpl-3.0.txt).
