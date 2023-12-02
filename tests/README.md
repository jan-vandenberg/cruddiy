# 1. How to run tests?

## 1.1. Install Behat, Mink, and other dependencies in the project

Go to the root of the project and run:

```
composer install
```



## 1.2. Edit mock database credentials

- We use the file `tests/.env` to store the database credentials for the tests.
- This file is specific to your environment and is not committed.
- To create one, duplicate `tests/.env.dist` and edit credentials to suit your need.
- You don't have to create the mock database (default: `cruddiy_tests`), creating it is part of the tests.
- If your test user does not have the privileges to create a database, just create it and fill the credentials in the `.env`. If the database exists, we will not attempt to re-create it.


## 1.3. Run the tests

Go to the root directory of the project and run `vendor/bin/behat --config tests/behat/behat.yml --suite admin`

```
germain@nuc13 UCRT64 /d/Sites/cruddiy
$ vendor/bin/behat --config tests/behat/behat.yml --suite admin
   (...)

19 scénarios (19 succès)
180 étapes (180 succès)
0m3.54s (13.02Mb)
```

### 1.3.1. Admin suite

To run all tests in the "admin" suite (test the generator):
```
vendor/bin/behat --config tests/behat/behat.yml --suite admin
```

### 1.3.2. Public suite

To run all tests in the "public" suite (test the generated pages):
```
vendor/bin/behat --config tests/behat/behat.yml --suite public
```

### 1.3.3. Specific test

To run a specific test:
```
vendor/bin/behat --config tests/behat/behat.yml tests/behat/features/admin/relations/schema.feature
```





# 2. How to update the coverage list?

## 2.1. Admin test suite for generated CRUD pages:

Read the coverage at [tests/coverage/admin.md](tests/coverage/admin.md)

Update the coverage by executing:

```
vendor/bin/behat --config tests/behat/behat.yml --suite admin --dry-run --no-snippets \
| grep -e '^Feature:' -e '^  Scenario:' \
| grep -o '^[^#]*' \
| sed 's/[[:space:]]*$//' \
| sed '/^Feature:/s/^/- /' \
| sed '/^  Scenario:/s/^/  - /' > tests/coverage/admin.md
```


## 2.2. Public test suite for generated CRUD pages:

Read the coverage at [tests/coverage/public.md](tests/coverage/public.md)

Update the coverage by executing:

```
vendor/bin/behat --config tests/behat/behat.yml --suite public --dry-run --no-snippets \
| grep -e '^Feature:' -e '^  Scenario:' \
| grep -o '^[^#]*' \
| sed 's/[[:space:]]*$//' \
| sed '/^Feature:/s/^/- /' \
| sed '/^  Scenario:/s/^/  - /' > tests/coverage/public.md
```






# 3. Test database schema

- For the tests we have a small structure with Products, Brands, and Suppliers
- `schema/Tests - Admin.sql` is the (nearly) blank structure, it is used to reset the test database every time you run the Admin test suite. It is also used to self-test the "Import Database Schema" feature.
- `schema/Tests - Public.sql` is a dump of the test DB after the Admin test suite has been run. It cleans up the DB every time the Public test suite is run (because the suite is performing CRUD operations, which would invalidate some tests when re-run).






# 4. Adding new tests

- To find a list of predefined assertions from the Mink extension, open `vendor\behat\mink-extension\src\Behat\MinkExtension\Context\MinkContext.php`
- Test your `.feature` files individually
- Then add them to the queue in `behat.yml`





# 5. Log tests

- Every time a test fails, a .html dump of the response is saved in `tests/behat/logs`.
- You can open the files in your browser to view the state of the application when the test ran, including PHP error messages and exceptions.
- The logs directory is deleted every time a new batch of tests is ran.
- The name of the log file mimics the Behat files in the `features/` directory, eg.:
  - Step fails in tests\behat\features\admin\relations\schema.feature line 16
  - Name of the log file: admin_relations_schema-16.html