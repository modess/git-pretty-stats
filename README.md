[![Build Status](https://api.travis-ci.org/modess/git-pretty-stats.png)](https://api.travis-ci.org/modess/git-pretty-stats.png)

You know those cool graphs and statistics you can get for a repository on github? The things is that (unfortunately) not all git repositories are hosted on github for various reasons. This is the tool for rendering graphs for your repository that can be hosted anywhere, and it looks great.

## Features (v0.1.0)
Graphs that are included:

* Commits by date
* Commits by hour of day
* Commits by day of week
* Commits by contributor

## Requirements

* PHP 5.3
* Git

## Installation
Start by clone this repository and setup your web server

Install dependencies using [Composer](http://getcomposer.org/), e.g. `php composer.phar install`

Clone the repository you want to graph in the root of the folder for git-pretty-stats. Using folder name **repository** is recommended since it's the default path, e.g. `git clone <repository-url> repository`
>  To manually set the repository path, copy `config.example.php` to `config.php`. Then change the value of `repositoryPath` in the config file relative to the folder where your desired git repository is located.

Now go to your web browser and go to the URL where you've set everything up.

*Disclaimer: the tool has been tested to work on repositories up to 10 000 commits. If your repository have a higher number of commits than that you might experience issues.*

## Contribute
1. Check for [open issues](https://github.com/modess/git-pretty-stats/issues) or open a fresh issue to start a discussion around a feature idea or a bug.
2. Fork the [git-pretty-stats](https://github.com/modess/git-pretty-stats) repository on Github to start making your changes.
3. Write a test which shows that the bug was fixed or that the feature works as expected. Make sure you have **100% code coverage**.
4. Send a pull request. Make sure to add yourself to CONTRIBUTORS.md.


## Screenshots

### Commit activity
![](http://www.codingswag.com/wp-content/uploads/2013/06/commit-activity.png)

***

### Commit activity
![](http://www.codingswag.com/wp-content/uploads/2013/06/contributors.png)

***

## Powered by
* [php-git-repo](https://github.com/ornicar/php-git-repo)
* [Silex](https://github.com/fabpot/Silex)
* [Twig](https://github.com/fabpot/Twig)
* [Flat UI](https://github.com/designmodo/Flat-UI)
* [Highcharts JS](https://github.com/highslide-software/highcharts.com)
* [Handlebars.JS](https://github.com/wycats/handlebars.js)
* [Font Awesome](https://github.com/FortAwesome/Font-Awesome)

## TODO
* Cache for large repositories
* Handle multiple repositories
* More graphs/statistics
* Additions / deletions for contributors

