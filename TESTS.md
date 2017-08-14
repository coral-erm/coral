# Automated Testing

## Initial setup
These steps only need to be run the first time you set up a testing environment.

### Install composer

#### Create `bin` folder
From within the document root, run:
```
mkdir bin
```

#### Get latest composer executable
Download the latest composer to the `bin` directory and make it executable:
```
wget -O bin/composer https://getcomposer.org/composer.phar && chmod +x bin/composer
```

### Enable PHP extensions
Make sure the following extensions are available on your system. They may vary depending on your Linux distribution or if you're on Windows.

- curl: required by Codeception
- mbstring: required by Codeception (PHP7 and up)
- dom: required by Codeception/PHPUnit (package php-xml on Ubuntu)

### Make sure Chromium or Chrome is installed
On Ubuntu:
```
sudo apt-get install chromium-browser
```

### Install dependencies with composer
Use only one of the following commands.

Install set versions of dependencies from the `composer.lock` file. This set of dependencies should work with PHP 5.6 and above.
```
bin/composer install
```
Download the newest versions and install the dependencies listed in the `composer.json` file. This will take the installed version of PHP into account and get the required dependencies intelligently.
```
bin/composer update
```

### Create the test databases
```
CREATE DATABASE coral_auth_test;
CREATE DATABASE coral_resources_test;
CREATE DATABASE coral_licensing_test;
CREATE DATABASE coral_management_test;
CREATE DATABASE coral_organizations_test;
CREATE DATABASE coral_usage_test;
CREATE DATABASE coral_reports_test;
```

Your DBMS should be configured to accept connections only from localhost.
So having a static passphrase and user for the test DBs shouldn't be an issue.
```
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_auth_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_resources_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_licensing_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_management_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_organizations_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_usage_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
GRANT CREATE, DROP, ALTER, SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON coral_reports_test.* TO 'coral_test'@'localhost' IDENTIFIED BY 'coral_test';
```

## Running tests
These steps can be followed repeatedly to run various tests.

**Note:** Replace all instances of *http://localhost/* in these commands with the URL to your local CORAL installation.

### Launch ChromeDriver in a terminal
In a separate terminal window start `chromedriver` with some options:
```
bin/chromedriver --url-base=/wd/hub --port=9515
```

### Run the all the tests
The `--debug` parameter is optional, but you must override the URL with that of your local CORAL installation:
```
bin/codecept run --debug --override="modules: config: WebDriver: url: http://localhost/"
```

#### Run a specific test suite
Replace `[<suite>]` with the name of a suite of tests, for example `acceptance`, to run only the acceptance tests:
```
bin/codecept run [<suite>] --debug --override="modules: config: WebDriver: url: http://localhost/"
```

#### Run a single test
Replace `[<suite>]` and `[<test>]` with the name of a suite and a test, respectively, for example `acceptance` and `LoginCept.php`, to run only the Login test:
```
bin/codecept run [<suite>] [<test>] --debug --override="modules: config: WebDriver: url: http://localhost/"
```

# Writing new tests
## Generate test stub
`bin/codecept generate:cept suite testname`

Where `suite` is the test suite you want to create a new test (e.g. acceptance). This will create a new test scenario `tests/suite/testnameCept.php`

# Guidelines writing new tests
## Be careful when checking that something is not here
Such checks are prone to false negative (passing when they shouldn't).
For example, checking that something deleted is no more listed somewhere.

A simple check that some string or some CSS selector is not here might always
succeed because of the page not having fully loaded yet. (Ajax)

For these cases, the helper `$I->waitForPageToBeReady();` should do the trick
without requiring a specific check like `waitForText` or `waitForElement`.
That being said, you should always check that you test is correct by either
- sabotaging the test or Coral to make appear the thing that should not be here and see if your test catches it.
  This is the most reliable approach.
- taking a screenshot before the `$I->dontSee('something');` to see if the page
is fully loaded. (after your confident, remove the screenshot before committing)

In either case, you should run the tests various times to double check that it
consistently works (sabotage is always caught or screenshot always shows the page fully loaded) because these issues are sporadic.

# Test architecture
![stack-experimental-codeception](https://cloud.githubusercontent.com/assets/2678215/17975154/ee52bdfc-6ae8-11e6-97f7-f45ff43b6e7d.png)
[source (ODG format)](https://github.com/Coral-erm/Coral/files/437395/stack-experimental-codeception.zip)

## Codeception
- Built on top of PHPUnit: syntactic sugar, helper functions and scaffolders(create test files and folders skeletons) to limit the amount of boilerplate code to write.
- Can also run PHPUnit tests.
- Provides facilities to shared code between tests and create custom functions to interact with the pages.

http://codeception.com/docs/06-ModulesAndHelpers

http://codeception.com/docs/06-ReusingTestCode

- Supports BDD (Behavior Driven Development) style tests: written in a syntax close to natural language that if embraced will be readable by librarians.

http://codeception.com/docs/07-BDD
