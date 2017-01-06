# Setup
## Install composer
### Create /bin folder
`mkdir bin`
### Download composer
`wget -O bin/composer https://getcomposer.org/download/1.2.0/composer.phar && chmod +x bin/composer`
## Enable PHP extensions
Method + which are already there vary depending on your Linux distribution or if you're on Windows.

- bz2: used to extract PhantomJS
- curl: so WebDriver can communicate with PhantomJS
- mbstring: required by Codeception
- dom: required by Codeception/PHPUnit (package php-xml on Ubuntu)

## Install dependencies
`bin/composer install`

## Create the test databases
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

# Running the tests
## 1. Launch PhantomJS in a terminal
`bin/phantomjs --webdriver=4444 --webdriver-loglevel=DEBUG`
## 2. Run the test suite (provide the url of your local Coral instance)
`BASE_URL=http://localhost/coral/ bin/codecept run -vv`
### To run a specific test suite (replacing `suite` accordingly):
`BASE_URL=http://localhost/coral/ bin/codecept run suite -vv`
### To run a single test (replacing `suite` and `testnameCept.php` accordingly):
`BASE_URL=http://localhost/coral/ bin/codecept run suite testnameCept.php -vv`

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
