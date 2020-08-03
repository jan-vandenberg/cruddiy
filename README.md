# CRUDDIY

Cruddiy is a free **no-code**  PHP Bootstrap 4 CRUD generator.

If you have a MySQL database and want to generate some simple, but beautiful, PHP Bootstrap CRUD pages (Create, Read, Update and Delete) but you don't want to handcode these pages for the umpteenth time, Cruddiy is for you.

# How it works

Cruddiy is written in PHP and will run on any PHP 7+ webserver. Just drop the folder with the code somewhere on your webserver and navigate to the folder. Cruddiy will first ask for your **database connection** information. Most of the time the database server is on the same machine (localhost): but it can be anywhere, and as long as you can connect to it, Cruddiy can generate the code for you.

[![N|Cruddiy](https://j11g.com/cruddiy/20200409-cruddiy-start.png)](https://cruddiy.com)

Once the connection parameters are entered correct, Cruddiy will display all available **tables** in your database.

[![N|Cruddiy](https://j11g.com/cruddiy/20200409-cruddiy-tables.png)](https://cruddiy.com)

From there you can select tables you wish to generate CRUD pages for, and you can give them proper readable names (with capitalization or spaces etc.). Selecting a table will generate CRUD pages for that specific table. After you have selected your tables Cruddiy presents all **columns** from the selected tables. Once again: you can give proper names and select which tablefields (columns) should be visible in the Index page. In this example you can see two tables, who each have three columns selected for their index page. 

[![N|Cruddiy](https://j11g.com/cruddiy/20200409-cruddiy-columns.png)](https://cruddiy.com)

And that's it!

Click Generate pages and Cruddiy will generate a complete PHP app for you in a separate folder, with config file, startpage and errorfile. And you can directly navigate to your app.

[![N|Cruddiy](https://j11g.com/cruddiy/20200409-cruddiy-app.png)](https://cruddiy.com)

This is the startpage. For every selected table, Cruddiy has created 5 pages (Index, Create, Read, Update and Delete pages). These pages are fully functional: the Index page has pagination and can be sorted by clicking on the columnname. You can also search globally in that specific table to quickly find records.

[![N|Cruddiy](https://j11g.com/cruddiy/20200409-cruddiy-app-index.png)](https://cruddiy.com)

You can rename or move the 'app' folder anywhere. It is completely self-contained. And you can delete Cruddiy, it is not needed anymore. Or you can can run it many more times, until you get your pages just the way you like them. Each time it will overwrite your existing app.
