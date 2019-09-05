# Review Form with PHP & MySQL

## What is this?
A basic review form for customers to share their experiences about a product or service online. It requires minimal amount of details from the user, including email address which is not visible for the public.

After a review is left its status is automaticaly set to 'Pending'. The admin from the admin page will check the comment for moderation and able to change its status accordingly. Reviews can be deleted however they will stay visible at the back end but cannot be edited.

## Database

Database setup scripts are provided with sample data.

### SQL Script

```
scripts/form-database.sql
```

The SQL script can be run on a database management program like phpMyAdmin.

### Shell Script

```
scripts/create-form-db.sh
```

After copying the file on to the database server we need to make it executable.

```sh
# chmod 0744 create-form-db.sh
```

To create the database run the script.

```sh
# create-form-db.sh -h localhost -u username -d database_name
```

### Database Connection

The configuration file containing the login credentials to the database is:

```
Classes/Db.php
```

```php
const HOST = "localhost";   // Domain/IP address
const USER = "";            // Username
const PASS = "";            // Password
const DB   = "";            // Database name
```    

## Admin Page

The admin page is available from:

```url
/admin
```

Login credentials are displayed there.

## Demo

[Frontend](http://projects.gellai.com/simple-review-form-with-php-&-mysql)

[Admin Backend](http://projects.gellai.com/simple-review-form-with-php-&-mysql/admin)

The database resets in every hour.
