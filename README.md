# PHP chat application backend using Slim framework and SQLite

* Slim Framework 4.* + SQLite

# Steps to execute in console

* php -S localhost:8080 -t public public/index.php

# Table details

* user table		: user_id Integer autoincrement, username String.

* messages table	: message_id Integer autoincrement, message_sender Integer, message_receiver Integer, message_sender_name String, message_content String, message_time String.

# Reading the application
* The application starts from the /public/index.php file.

* API endpoints are declared in /src/routes/messageController.php.

* Database class and methods are declared in /src/database/database.php.

* A simple validation method is added in /src/validation/validation.php

* Comments are added in each class and methods for easy understanding.