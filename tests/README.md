# How to run tests?

## 1. Install PHPUnit and Selenium

Go to the root of the project and run:

```
composer install
```

## 2. Install JSE

https://www.oracle.com/java/technologies/downloads/#java11-windows

Download and install `jdk-21_windows-x64_bin.exe`


## 3. Install Selenium

https://www.selenium.dev/downloads/

Download and install Selenium Server (Grid) `selenium-server-4.15.0.jar`


## 4. Open cmd

`Win` + `R`, `cmd.exe`

```
java -jar selenium-server-4.15.0.jar standalone --selenium-manager true
```

http://192.168.100.14:4444/ui

This should bring the Selenium Grid.


## 5. Back to the project shell

```
php vendor/bin/phpunit tests/HelloWorldTest.php

PHPUnit 10.4.2 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.0

.                                                                   1 / 1 (100%)

Time: 00:06.333, Memory: 8.00 MB

OK (1 test, 1 assertion)
```