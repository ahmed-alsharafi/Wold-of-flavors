 ## The World of Flavors

A dynamic and user-friendly cooking recipes web application that allows users to explore, add, edit, and manage recipes with a clean interface and an admin dashboard for full system control.

![image alt](https://github.com/ahmed-alsharafi/Wold-of-flavors/blob/main/assets/images/logo-png.png)
# Overview

The World of Flavors is a PHP-based web application designed to help users browse and share cooking recipes.
The platform supports user accounts, recipe management, intelligent search, and a fully featured admin control panel.

This project also includes a structured MySQL database and secure authentication system, making it suitable as a real-world web application or graduation project.

![image alt](https://github.com/ahmed-alsharafi/Wold-of-flavors/blob/main/assets/images/Header.png)



# Features

👥 User Features

* Create an account & login securely
* Browse all recipes
* View detailed recipe pages
* Upload recipe images
* Smart recipe search (Recipe Builder)
* Add new recipes
* Mobile-friendly responsive UI



🛠️ Admin Features

* Accessible only to admin accounts:
* Full dashboard with key statistics
* Manage users (add, edit, delete)
* Manage all recipes (edit, delete)
* View recent users and recent recipes
* Daily analytics (new users, daily recipes)



📸 File Upload Support

* Users can upload recipe images
* Old images are auto-deleted when updated
* Secure file validation (type & size restrictions)



🧱 Project Structure

Below is the general structure of the project:

The-world-of-flavors/
│ 
├── admin/ 
│ ├── add-user.php 
│ ├── edit-user.php 
│ ├── manage-users.php 
│ ├── add-recipe.php 
│ ├── edit-recipe.php 
│ ├── manage-recipes.php 
│ ├── delete-user.php 
│ ├── delete-recipe.php 
│ ├── dashboard.php 
│ └── header.php 
│ 
├── includes/ 
│ ├── config.php 
│ ├── footer.php 
│ └── header.php 
│ 
├── assets/ 
│ ├── images/ 
│ ├── font-awesome/ 
│ └── jquery/ 
│ 
├── css/ 
│ └── style.css 
│ 
├── js/ 
│ └── script.js 
│ 
├── uploads/  
│ 
├── index.php
├── recipes.php 
├── recipe-detail.php 
├── recipe-builder.php 
├── add-recipe.php 
├── login.php 
├── register.php 
└── logout.php 




# Database Structure

* Database Name: cooking_project

* Main tables:

1. users

Column            Type               Description

id                INT(PK)            User ID
username          VARCHAR            Unique username
email             VARCHAR            Unique email
password          VARCHAR            Hashed password
is_admin          TINYINT            1 = admin, 0 = user
created_at        TIMESTAMP          Registration date 



2. recipes

Column            Type               Description

id                INT(PK)            Recipe ID
title             VARCHAR            Recipe title
description       TEXT               Short description
ingredients       TEXT               One ingredient per line
instructions      TEXT               One step per line
category          VARCHAR            Recipe category
prep_time         INT                Preparation time in minutes
difficulty        VARCHAR            Easy / Medium / Hard
image             VARCHAR            Uploaded image file
created_at        TIMESTAMP          Creation date
user_id           INT(FK)            Creator user 



🛠️ Technologies Used

PHP 7+
MySQL
HTML5, CSS3, JavaScript
jQuery
Font Awesome Icons
Google Fonts
Responsive Design



🔧 Installation & Setup

1. Clone or Extract the Project

extract the provided ZIP file.

2. Configure Database
Create a new MySQL database
database Name: cooking_project
Import the SQL file:
cooking_project.sql 


3. Run the Project

Place the project inside your server directory:
htdocs/
Then open:
http://localhost/The-world-of-flavors/ 



🔑 Default Admin Account

If provided in your SQL file, use:
Email: noor
Password: nooree


🤖 Smart Recipe Search (Recipe Builder)
Users can build recipes by selecting ingredients, and the system will search for matching recipes automatically.



🛡️ Security

The application includes:

Input sanitization
Password hashing with password_hash()
File validation for uploads
Admin-only routes protected by session logic


