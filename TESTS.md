# Setup
## Install composer
### Create /bin folder
`mkdir bin`
### Download composer
`wget -O bin/composer https://getcomposer.org/download/1.2.0/composer.phar && chmod +x bin/composer`
## Enable PHP extensions
Method + which are already there vary depending you're on Linux distribution or if you're on Windows.
### bz2: used to extract PhantomJS
### curl: so WebDriver can communicate with PhantomJS
### mbstring: required by Codeception
### dom: required by Codeception/PHPUnit (package php-xml on Ubuntu)

## Install dependencies
`bin/composer install`

# Running the tests
## 0. Important things to know
As of today, the tests don't run in a separate database.
It's just automating some checks that you would do manually on your local instance.
Nothing more. This means that when a test fails, it might leave some test data which could make fail subsequent test runs.
Therefore manual cleanup might be required.
This is a big limitation, hopefully database separation for the tests will be implemented soon enough.
## 1. Launch PhantomJS in a terminal
`bin/phantomjs --webdriver=4444`
## 2. Run the test suite (provide the url and credentials for your local Coral instance)
`BASE_URL=http://localhost/coral/ CORAL_LOGIN=my_login CORAL_PASS="my passphrase" bin/codecept run -vv`


# Guidelines writing new tests

## The tests must clean their own data
As mentioned earlier, there is currently no database isolation for tests.
Therefore they need to clean any created data that might interfere with further test runs.
Like when doing manual checking one have to clean it's data.
The tests should be designed be ran as many times as we wish without causing trouble.

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
