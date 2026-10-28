# Recipe Management Website

A recipe management web app built with plain PHP and MySQL. Users can browse, upload, rate and bookmark recipes, search by name, ingredient or category, and keep a shopping list.

![All recipes](screenshots/recipes.png)

## Features

- Sign up, log in and log out (passwords are stored hashed)
- Browse recipes and view details, ingredients and average ratings
- Rate and comment on recipes; bookmark favourites
- Search by title, ingredient or category
- Upload, edit and delete your own recipes, with images
- Personal shopping list

**Tech stack:** PHP, MySQL (8 related tables), HTML, CSS

## Run locally (XAMPP)

1. Copy the project into `xampp/htdocs/` and start Apache and MySQL.
2. In phpMyAdmin, create a database called `recipe_db` and import `recipe_db.sql`.
3. Copy `config/db.example.php` to `config/db.php`.
4. Open http://localhost/Recipe-Management-Website

**Demo login:** `demo` / `demo123`
