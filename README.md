# Address book app - Seedstars challenge 2

**Full App**

Make an application which stores names and email addresses in a database (SQLite is fine).

a) Has welcome page in http://localhost/
- this page has links to list and create functions

b) Lists all stored names / email address in http://localhost/list

c) Adds a name / email address to the database in http://localhost/add
- should validate input and show errors


**Installation**

```
# clone repository
$ git clone https://github.com/barmmie/address-book.git

# change directory
$ cd address-book

# install composer packages
$ composer install

# change directory
$ cd public

# run server
$ php -S localhost:8000
```
Visit url [http://localhost:8000](http://localhost:8000) to view the app.

**Third-party libraries used**

- [FastRoute](https://github.com/nikic/FastRoute) -  A fast request router for PHP
- [League Container](http://container.thephpleague.com/) - Dependency injection container 
- [Twig](http://twig.sensiolabs.org/) - Templating engine for PHP
- [IRCMaxwell Random-Lib](https://github.com/ircmaxell/RandomLib) - A library for securely generating random numbers and strings of various strengths.
- [Valitron](https://github.com/vlucas/valitron) - A validation library
- [Symfony HttpFoundation](http://symfony.com/doc/current/components/http_foundation.html) - An object-oriented layer for the HTTP specification

