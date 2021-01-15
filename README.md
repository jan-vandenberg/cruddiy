# CRUDDIY

Cruddiy is a free **no-code**  PHP Bootstrap 4 CRUD generator (with foreign key support).

With Cruddiy you can easily generate some simple, but beautiful, PHP Bootstrap CRUD pages (Create, Read, Update and Delete) with search, pagination and foreign key awareness.

### Notes
* Does not support MyISAM for Foreign Keys and Cascades. Will need InnoDB type ENGINE. MyISAM can be used, but Foreign Keys and Cascades will not be available.
* Although PHP 7 is recommended, it currently works with PHP 5.4 too.
* Capable of string ID Primary Keys as well as integer ones.

# How it works

Cruddiy is written in PHP and will run on any PHP 7+ webserver. Just drop the folder with the code somewhere on your webserver and navigate to the folder (core/index.php). Cruddiy will first ask for your **database connection** information. Most of the time the database server is on the same machine (localhost): but it can be anywhere, and as long as you can connect to it, Cruddiy can generate the code for you.

[![N|Cruddiy](https://j11g.com/cruddiy/bs4-cruddiy-start.png)](https://cruddiy.com)

Once the connection parameters are entered correctly, Cruddiy will display all existing table relations (aka Foreign Keys). This only works for InnoDB tables.

[![N|Cruddiy](https://j11g.com/cruddiy/bs4-cruddiy-relations.png)](https://cruddiy.com)

It is possible to add or delete database relations and define actions (e.g. ON DELETE CASCADE).
It is absolutely safe to SKIP this step (press the big green button) and continue if you please. However, having well-defined relations will result in prepopulated input fields on the create forms, and it will make sure child records are deleted correctly etc. When you use MyISAM you need to skip this step anyway.

For the next step, Cruddiy will display all available **tables** in your database.

[![N|Cruddiy](https://j11g.com/cruddiy/bs4-cruddiy-tables.png)](https://cruddiy.com)

From there you can select tables you wish to generate CRUD pages for (maybe you just need a couple and not all tables in your database), and you can give them proper readable names (with capitalization or spaces etc.). Selecting a table will generate CRUD pages for that specific table. If you leave them unselected, NO pages will be created.
After you have selected your tables, Cruddiy will present all **columns** from the previously selected tables. Again: you can give proper names and select which tablefields (columns) should be visible in the Index page. In this example you can see two tables, each have three columns selected to be visible on the index page. 

[![N|Cruddiy](https://j11g.com/cruddiy/bs4-cruddiy-columns.png)](https://cruddiy.com)

And that's it!

Click Generate pages and Cruddiy will generate a complete PHP app for you in a separate folder, with config file, startpage and errorfile. You can directly navigate to your app, it will open in a new tab.

[![N|Cruddiy](https://j11g.com/cruddiy/bs4-cruddiy-app.png)](https://cruddiy.com)

This is the startpage. For every selected table, Cruddiy has created 5 pages: Index, Create, Read, Update and Delete. These pages are fully functional: the Index page has pagination and can be sorted by clicking on the columnname. You can also use the search box to quickly find records for that table.

[![N|Cruddiy](https://j11g.com/cruddiy/bs4-cruddiy-app-index.png)](https://cruddiy.com)

You can rename or move the generated 'app' folder anywhere. It is completely self-contained. And you can delete Cruddiy, it is not needed anymore. Or you can can run it many more times (use the back button in the browser), until you get your pages just the way you like them. Each time it will overwrite your existing app.
