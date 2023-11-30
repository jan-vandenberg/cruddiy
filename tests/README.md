# How to run tests?

_Note: I was planning to use Selenium for some tests, but I didn't need it._

_We're only using the Goutte driver at the moment, but I'm keeping the existing doc as reference for future improvements._

> **You can safely bypass steps 2 and 3.**

## 1. Install Behat, Mink, and other dependencies in the project

Go to the root of the project and run:

```
composer install
```

## 2. Install the Selenium server on your computer

### 2.1 JSE

https://www.oracle.com/java/technologies/downloads/#java11-windows

Download and install `jdk-21_windows-x64_bin.exe`

Test in a command prompt (`cmd`):
```
C:\Users\germain>java --version
java 21.0.1 2023-10-17 LTS
Java(TM) SE Runtime Environment (build 21.0.1+12-LTS-29)
Java HotSpot(TM) 64-Bit Server VM (build 21.0.1+12-LTS-29, mixed mode, sharing)
```


### 2.2 Selenium

https://www.selenium.dev/downloads/

Download and install Selenium Server (Grid) `selenium-server-4.15.0.jar`


## 3. Run Selenium Grid (the Selenium server)

Open cmd:
`Win` + `R`, `cmd.exe`

Run:
```
java -jar selenium-server-4.15.0.jar standalone --selenium-manager true
```

This should bring the Selenium Grid.
You can check:
- http://192.168.100.14:4444/ui
- http://localhost:4444/wd/hub/status


## 4. Edit mock database credentials

- We use the file `tests/.env` to store the database credentials for the tests.
- This file is specific to your environment and is not committed.
- To create one, duplicate `tests/.env.dist` and edit credentials to suit your need.
- You don't have to create the mock database (default: `cruddiy_tests`), creating it is part of the tests.
- If your test user does not have the privileges to create a database, just create it and fill the credentials in the `.env`. If the database exists, we will not attempt to re-create it.


## 5. Run the tests

```
germain@nuc13 UCRT64 /d/Sites/cruddiy
$ vendor/bin/behat --config tests/behat/behat.yml --suite admin
Feature: Check admin index page content

  Scenario: Checking content on the admin homepage # features\admin\index.feature:3
    Given I am on "/core/index.php"                # FeatureContext::visit()
    Then I should see "Enter database information" # FeatureContext::assertPageContainsText()

1 scénario (1 succès)
2 étapes (2 succès)
0m0.06s (11.78Mb)
```

To run all tests in the "admin" suite (test the generator):
```
vendor/bin/behat --config tests/behat/behat.yml --suite admin
```

To run all tests in the "public" suite (test the generated pages):
```
vendor/bin/behat --config tests/behat/behat.yml --suite public
```

To run a specific test:
```
vendor/bin/behat --config tests/behat/behat.yml tests/behat/features/admin/relations/schema.feature
```

# What is tested now, and how to update the coverage list?

## Admin test suite for generated CRUD pages:

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


## Public test suite for generated CRUD pages:

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