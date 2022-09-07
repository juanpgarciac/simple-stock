# simple-stock
simple stock project with a custom MVC framework. 


This is a re-boot of the old and ugly https://github.com/juanpgarciac/quarantine_stock

I wanted to apply better and clean code, and suddenly I was making a little MVC framework (with some resembles to Laravel <3).

You can see here: 

OOP, SOLID principles, Some design patterns, Unit testing, a router, etc. 
Tools PHP Stan, PHP CS fixer, PHPUnit. 
PHP v8 + Bootstrap v5. 

-- NOT any other tool/composer library was used --

DB: It works with MySQL/MariaDB 5+, SQLite 3 y PostgreSQL 8+. (Both native and PDO). 

To use the app, you will need a running php 8 server, and follow: 
1. Clone the repo, 
2. Create a DB with resources/stock.sql (Made with MySQL/MariaDB, but you can port it to SQLite or Postgres), 
3. Configure a .env file (using the .env.example) 
4. Then go to the public folder and run php -S ip:port command.
5. Have fun!


TO-DO: 

* Basically separate the framework layer to another repository and continue it (Session, authenticathion, file handling, ?). 

* Fill all with documentation. 

* Complete the "please CLI", check branch "command-feature". 

* Improve this README file. 
