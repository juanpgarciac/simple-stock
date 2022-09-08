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

### Screenshots:

![image](https://user-images.githubusercontent.com/6114482/189206352-8929c6cb-c229-43f3-97d7-22710ff88053.png)
![image](https://user-images.githubusercontent.com/6114482/189206440-4a0f598a-4869-4613-ae7f-0d1477168d08.png)
![image](https://user-images.githubusercontent.com/6114482/189206480-177d61d1-3341-4e37-95bb-58d764378928.png)
![image](https://user-images.githubusercontent.com/6114482/189206562-30ee90b0-57af-43f1-9849-bfdce4b94059.png)
![image](https://user-images.githubusercontent.com/6114482/189206591-a01bc06d-b67a-4a80-97fa-3f34cb306f12.png)
![image](https://user-images.githubusercontent.com/6114482/189206645-c2408dfd-a687-4f9f-b34e-ae7ceb70f86c.png)
![image](https://user-images.githubusercontent.com/6114482/189206781-f4fa8fbe-d893-41bb-bf1d-4ecb60a598ba.png)
![image](https://user-images.githubusercontent.com/6114482/189206843-dcf76795-831f-4b91-aa29-9ff488b0f9e5.png)



