# Review Form with PHP & MySQL

## What is this?
A basic review form for customers to share their experiences about a product or service online. It requires minimal amount of details from the user, including email address which is not visible for the public.

After a review is left its status is automaticaly set to 'Pending'. The admin from the admin page will check the comment for moderation and able to change its status accordingly. Reviews can be deleted however they will stay visible at the back end but cannot be edited.

## Database

Database setup scripts are provided with sample data.

### SQL script

```
scripts/form-database.sql
```

The SQL script can be run on a database management program like phpMyAdmin.

The admin page is available from:
```url
/admin
```
Login credentials are displayed there.

