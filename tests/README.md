# How to run tests?

## 1. Install Behat, Mink, and Selenium dependencies in the project

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
$ vendor/bin/behat --config tests/behat/behat.yml
Feature: Check admin index page content

  Scenario: Checking content on the admin homepage # features\admin\index.feature:3
    Given I am on "/core/index.php"                # FeatureContext::visit()
    Then I should see "Enter database information" # FeatureContext::assertPageContainsText()

1 scénario (1 succès)
2 étapes (2 succès)
0m0.06s (11.78Mb)
```