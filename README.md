# Bar Sales

Bar Sales is free software designed to help people running a small bar.

It allows them to do some basic tasks that a small bar would need such 
as:
* log incoming stock
* record sales
* take stock
* record shrinkage

## Requirements
Webserver running:
* PHP 5
* MySQL

The application is developed on an apache webserver.

## Install
Set up a database - import the `database/bar_sales.sql` file into it.  
Place files on a webserver.  
Make a copy of `example_settings.php` and rename it `settings.php`  
Edit the database connection information in `settings.php`  
Make other edits as required in `settings.php`  
Point your browser to `index.php` and you should be up and running.  

## Configuration
You will need to **create some 'events'** against which to record sales.  
Currently you need to do this directly in your database in the `event_type`
table.

You should be able to **create some stock items** in the form:  
`<your-domain>/form_stock_items.php`

# How it works

The application has: 
* views (view, edit, delete data)
* forms (create, update data)
* processes (preocess form submissions)

We can enter data about:

* Incoming Stock records
* Sales

We can also perform stock taking. These are saved as:
* Stock Take records

When we stock take we also create a **shrinkage record**.


Generally, once you have entered some data you get a different page to
view it. From those *view pages* you can edit or delete the record.

When you edit a record you are sent back to the form where you first 
entered the data.

Most `form` pages, also have an associated `process_` file that deals with:
* creating the record
* updating the record

