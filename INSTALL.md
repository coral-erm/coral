Installation can occur in one of two ways – either through the web installation
script or manually. Web installation will provide advantages over manual
installation because it will check MySQL privileges and PHP version.

# Requirements/Dependencies
You need PHP, a MySQL implementation and a web server. (continue reading for supported versions)

See "New to PHP?" section if needed.

## PHP extentions
- gettext
  - Ubuntu 16:04: already installed but otherwise you would have to install the `php-gettext` package
  - Some Linux distros like Arch Linux: uncomment gettext.so in `php.ini`
  - WAMP: enable `php_gettext`
- mysqli

## Supported versions of the dependencies
There is currently 3 levels of support for the dependencies.

### 1. These are the most tested and reliable
- Linux
- PHP 5.6 (recommended)
- PHP 5.5 (not recommended, it's an obsolete version that we would like to drop ASAP)
- Apache
- MySQL 5.5

### 2. Less tested but no issues to be expected
- MariaDB (MySQL implementation)
- Nginx (web server)
- Windows

### 3. Supported only on the master branch
Recommended for those who want to help Coral move forward by testing the master branch with these recent versions and report bugs if any.

- PHP 7: no known issue
- MySQL 5.7: Reports module installer broken, issue #110


# Web Install
## Download
### Latest release
http://coral-erm.org/download/
### From master: For developers and beta testers only
`git clone git@github.com:Coral-erm/Coral.git`

or

`git clone https://github.com/tuxayo/Coral.git`

## Create databases
Production instance: Change the passphrase because xxxxx is not a good one!
```sql
CREATE DATABASE coral_auth_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_auth_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
CREATE DATABASE coral_resources_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_resources_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
CREATE DATABASE coral_licensing_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_licensing_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
CREATE DATABASE coral_management_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_management_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
CREATE DATABASE coral_organizations_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_organizations_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
CREATE DATABASE coral_usage_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_usage_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
CREATE DATABASE coral_reporting_prod;
GRANT SELECT, INSERT, UPDATE, DELETE ON coral_reporting_prod.* TO 'coral'@'localhost' IDENTIFIED BY 'xxxxx';
```

## Create config files and set data directories permissions
**Warning: A production instance should have stricter permissions for the folders than the following.**
```shell
touch auth/admin/configuration.ini
chmod a+rw auth/admin/configuration.ini

touch resources/admin/configuration.ini
chmod a+rw resources/admin/configuration.ini
chmod a+w resources/attachments

touch licensing/admin/configuration.ini
chmod a+rw licensing/admin/configuration.ini

touch management/admin/configuration.ini
chmod a+rw management/admin/configuration.ini
chmod a+w management/documents/

touch organizations/admin/configuration.ini
chmod a+rw organizations/admin/configuration.ini

touch usage/admin/configuration.ini
chmod a+rw usage/admin/configuration.ini
chmod a+w usage/archive/
chmod a+w usage/sushistore/

touch reports/admin/configuration.ini
chmod a+rw reports/admin/configuration.ini
```
## Modules
The order is important.

We suppose that the coral base url is https://example.org/coral/

At the end of each module install, you will be told to remove `/MY_MODULE/install`, this step is optional for a dev instance.

### Auth
Go to https://example.org/coral/auth/install/

Follow the instructions.

### Resources
Go to https://example.org/coral/resources/install/

Follow the instructions.

Your Login ID: the same of the admin user that you created in the auth module.

#### Inter-operability settings for a dev instance
- Are you using the authentication module? yes
- If so, enter authentication database schema name: `coral_auth_prod`
- Are you using the licensing module? yes
- If so, enter the licensing database schema name: `coral_licensing_prod`
- Are you using the organizations module? yes
- If so, enter the organizations database schema name: `coral_organizations_prod`
- Are you using the usage module? yes

#### Additional config settings
- Would you like to enable alerts? yes
- Feedback Email Address - used for email address link on Resource page and for workflow notifications: `your-own-email-for-a-dev-instance@example.org`

### Licensing
Go to https://example.org/coral/licensing/install/

Follow the instructions.

Your Login ID: the same of the admin user that you created in the auth module.

#### Inter-operability settings for a dev instance
- Are you going to use the Terms Tool Add-On? yes
- Are you using the authentication module? yes
- If so, enter authentication database schema name: `coral_auth_prod`
- Are you using the organizations module? yes
- If so, enter organizations database schema name: `coral_organizations_prod`
- Are you using the resources module? yes
- If so, enter resources database schema name: `coral_resources_prod`
- Are you using the usage module? yes

### Management
Go to https://example.org/coral/management/install/

Your Login ID: the same of the admin user that you created in the auth module.

#### Inter-operability and other config settings
- Are you using the authentication module? yes
- If so, enter authentication database schema name: `coral_auth_prod`

##### Manually edit `management/admin/configuration.ini`
So the consortiums are used in the category list, the setup doesn't show this option.

Update the following options:
```
organizationsModule=Y
organizationsDatabaseName=coral_organizations_prod
```
### Organizations
Go to https://example.org/coral/organizations/install/

Follow the instructions.

Your Login ID: the same of the admin user that you created in the auth module.

#### Inter-operability and other config settings
- Are you using the authentication module? yes
- If so, enter authentication database schema name: `coral_auth_prod`
- Are you using the resource module? yes
- If so, enter the resource database schema name: `coral_resources_prod`
- Are you using the licensing module? yes
- If so, enter the licensing database schema name: `coral_licensing_prod`
- Are you using the usage module? yes


### Usage
Go to https://example.org/coral/usage/install/

Follow the instructions.

Your Login ID: the same of the admin user that you created in the auth module.

#### Inter-operability and other config settings
- Are you using the authentication module? yes
- If so, enter authentication database schema name: `coral_auth_prod`
- Are you using the licensing module? yes
- Are you using the organizations module? yes
- If so, enter the organizations database schema name: `coral_organizations_prod`
- Are you using the resource module? yes
- Are you going to use the outlier flagging feature when importing statistics? yes


### Reports
Go to https://example.org/coral/reports/install/

Follow the instructions.

## After modules install
### Production instance
- Remove write permissions on the config files
- Delete all the `/MY_MODULE/install`
- Ensure that all the `/MY_MODULE/admin/*` are not accessible from the web. (e.g. setup an .htaccess)

## Next
See section "Additional setup + customization"


# Manual Installation 
Install database tables 

1. Create new database schema (recommended name is `coral_modulename_prod`) 
1. Apply the SQL file to your new database
1. Manually add an admin user into User table – this is required for the first person to administer 
users.   
    1. Open your MySQL client 
    1. Navigate to your database (`coral_modulename_prod`) and to the User table 
    1. Insert  your LoginID (your externally authenticated login ID) with a  Privilege ID of 1 
(equates to admin) and your first and last name, if desired. 
    1. This is the user who will have access to the Admin page to first administer other users.  
From the Admin Page, you can designate another user with the admin privilege for that 
person to also administer users. 

Update `/admin/configuration.ini`

1. Rename `/admin/configuration_sample.ini` to `/admin/configuration.ini`
1. Under [settings] 
    1. organizationsModule=, cancellationModule=, licensingModule=, resourcesModule=, 
usageModule=  
These switches define whether you have the other CORAL modules installed and would like the interoperability turned on.  The switch will also turn on/off the link to other modules (the Change Module drop down menu in the upper right-hand corner).  Valid values are Y or N.  Interoperability currently 
occurs between: 
        1. Resources to Licensing and Organizations 
        1. Organizations and Licensing (both directions) 
        1. Usage Statistics to Organizations
    1. organizationsDatabaseName=, licensingDatabaseName=  
When you have interoperability turned on between Organizations and Licensing database or Organizations 
and Usage Statistics you must supply the database name (e.g. `coral_organizations_prod`) for the connection.  Also note that the user you connect with must have select, insert, 
update and delete privileges to this database.  
    1. [resources only] defaultCurrency=  
This will set the currency that is selected by default in the Resource Payment section.  Default currency needs to be a valid Code in the Currency tab on the Admin page.  
    1. [resources only] enableAlerts=  
This will turn on the alerting checkbox on the Acquisitions tab on the Resource page for subscription end dates.  The days in advance 
and email addresses can be set in the Alert Settings on the Admin page.  Also note this 
requires a scheduled job, see below on Resources Alerts Scheduling. 
    1. [resources only] catalogURL=  
If you would like to include a link directly into your catalog 
from the Resource page you can place your URL with the parameter for the System 
Number at the very end.  For example, ours is: 
"http://alephprod.library.nd.edu/F/?func=direct&doc_number=" 
    1. [resources only] feedbackEmailAddress=  
This is the global email address for all 
workflow notifications - starting, queue entries and completion.  Emails for specific 
steps can be set in the User Group section on Workflow/User Group tab of the Admin 
page.  The feedback email address also shows on the Resource Right Panel.  It is 
optional. 
    1. [licensing only] useSFXTermsToolFunctionality=  
This will turn on/off the SFX tab and 
Terms Tool Report in the licensing module.  Valid values are Y/N.  
    1. [usage only] reportingModule=  
This will turn on/off the reporting link in the usage 
statistics module.  Set to Y if you are using the reporting module add-on (this is a 
separate install).  Valid values are Y/N. 
    1. [usage only] useOutliers=  
This will turn on/off the outlier flagging feature.  This feature 
will calculate abnormal spikes in usage and color code them red, orange or yellow based 
on the formula defined in the admin tab.  Valid values are Y/N.  
    1. [usage only] baseURL=  
This will turn on or off a created link within the module to your 
open URL. 
    1. remoteAuthVariableName=  
This is the server variable used by php to determine the 
user authorized to log in.  For example, we use `$HTTP_SERVER_VARS['REMOTE_USER']`, 
`$_SERVER['PHP_AUTH_USER']`, or `$_SERVER['WEBAUTH_USER']` may be other names.  
    1. [resources only] defaultsort=  
Changes the default sort order for the resources.  Example: defaultsort=\"TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) asc\"  It is 
optional.  
1. Under [database] 
    1. type=mysql  
currently only MySQL is supported but other database types could be 
supported if modifications to the generic database classes are made 
    1. host=  
MySQL server - could be localhost as well 
    1. name=  
Database name (e.g. coral_resources_prod) 
    1. username=  
recommended to have a single user with select, update, insert, delete 
access to all CORAL modules 
    1. password=  
password for above mentioned user 
1. Under \[ldap] (optional – not for Licensing Module or Usage Statistics Module) 
    1. Host=  
server name 
    1. Search key =  
filter parameter used in `ldap_search` call – for more info refer to 
http://php.net/manual/en/function.ldap-search.php 
    1. Base DN=  
The Base DN for the directory(`base_dn` parameter used in `ldap_search` call) 
    1. First Name Field=  
Name of the field that LDAP uses for first name – for us it’s 
“givenname” 
    1. Last Name Field=  
Name of the field that LDAP uses for last name – for us it’s “sn” 
1. For security reasons, remove the `/install/` directory once the installation is complete
1. If you are using an external authentication system, you will need to require it in the `/coral/[module_name]/.htaccess` file
1. If you are installing more than one module for interoperability you may see errors from either 
side until both modules are installed.
1. Join the listserv used for product updates, support, and general discussion by sending an email to listserv@listserv.nd.edu. Leave the subject line blank and include 'SUBSCRIBE CORAL-ERM Your Name' in the body; ex. SUBSCRIBE CORAL-ERM John Smith.



# Additional setup + customization
## Additional setup before actually using some modules
### Resource
Create a resource type in the admin before creating a resource.

### Management
Create a category in the admin before creating a document.

## Resources Alerts Scheduling 
The Resources module includes a script called `sendAlerts.php` that can be run nightly to send out 
subscription alerts. To set it up via cron job, you will need to invoke php. You need to access the alerts page via curl or wget in order to generate emails with functional links:

`0 0 * * * /usr/bin/wget http://YOUR_PATH…/coral/resources/sendAlerts.php`

You will also need to verify that enableAlerts is set to Y in `/resources/admin/configuration.ini`

Note that this will send an email to the feedbackEmailAddress also set in `configuration.ini`.  For 
temporary testing purposes you may wish to change or remove this email address.  The number of days 
in advance and other email addresses can be modified in the Alert Settings tab of the Admin page. 


## Authentication 
Authentication is required for CORAL since all users are given specific privilege levels (for more on
privilege levels refer to the user guide). CORAL has two choices for authentication – either to tie in to
your university/library’s authentication system or to install the CORAL Authentication Module. For more
information on the CORAL Authentication module, please refer to the [Authentication Documentation](https://github.com/Coral-erm/Coral/wiki/Authentication-Module-Technical-Documentation).

To utilize your library’s existing authentication system, you will need to know the Server Variable name
to use within PHP. This is a setting within configuration.ini called “remoteAuthVariableName” which will
be set in the installation process (see Installation, below). The remoteAuthVariableName indicates the
PHP server variable name that is set in apache upon authentication and so CORAL will recognize the
user's login. Different server setups may have different variable names. Assuming you're using an
authentication system already, you can do a phpinfo script to see if this variable is being set and what it is. (just make a .php file and put `<?php phpinfo(); ?>` in it ) Probably the easiest way to find the variable
name would be to search for your user ID.

If you still have problems using your existing authentication system, we would recommend subscribing
to the listserv and looking at the archives to see how other institutions have made it work, or searching the forum.
As a last resort, each module has ‘user.php’ in the root directory which can be modified to retrieve your
users’ login ID.

Also, the `.htaccess` file should require your authentication system, or you should have another way to
require authentication. If you have questions about this, contact your system administrator.
CORAL has limited use directly with LDAP. Within all of the modules (except for Licensing) any user
allowed by the `.htaccess` may visit the site; if LDAP has been set up in the `configuration.ini` it will display
their name in the upper right hand corner. This connection is optional.


## Additional CORAL Resources customization options 

### Payment formatting 
To change the format of the display of amounts (e.g. use a comma in the place of a period), you can 
modify the `/resources/directory.php` file, `integer_to_cost()` function.  The `number_format` function is a
php function – refer to PHP documentation for more information: 

https://php.net/manual/en/function.number-format.php

### Date formatting 
To change the format of the date from the US standard MM/DD/YYYY you can modify the 
`/resources/directory.php` file, `format_date()` function.  The `date()` function is a php function – refer to 
PHP documentation for more information: 

https://php.net/manual/en/function.date.php

The date will need to be convertible by the php `strtotime` function.  For European dates the `strtotime` 
function requires dashes be used instead of slashes.  Also for the year, be sure to use Y (uppercase) for 4 
digit year and y (lowercase) for 2 digit year. 

Additionally for date formatting you will need to update a javascript file for the date picker calendar 
functionality.  Be sure this format matches the format you set in `directory.php`! 
`/resources/js/common.js`, line 35: 

`Date.format = 'mm/dd/yyyy';`

or 

`Date.format = 'dd-mm-yyyy';`

### Email customization 
The templates for the workflow notification and alert emails are located in `/admin/emails/`

You can modify the text in these as needed, leaving the words inside brackets `< >` as is – you can change
where they appear but leave the format alone.

# Troubleshooting
## New to PHP?
If your institution is new to the PHP/MySQL setup, installation may be a bit more tricky. 

First off, make sure that you’re both installing php with the `-with-mysql` option and installing the phpmysql package.  To verify that php and mysql are installed and working correctly, you can make a test 
php script with only the following: 

`test.php` contents:  

`<?php phpinfo(); ?>`

To debug issues with authentication you can override authentication in the 
`/coral/modulename/user.php` script. 

You may also wish to change the error reporting level in `php.ini` if you are getting blank pages in the
install or with the CORAL pages themselves. 

## Possible issues during installation (or - things to verify if CORAL is not working at this point) 
verify you’re running PHP >= 5.5

verify your database user can connect to the mysql database and that all tables were created properly 

if your login isn’t working, verify that you have required your authentication service in the `.htaccess` file.  
you should be prompted with your institutions default login screen before getting to the coral screens. 

if you’re not sure what your remoteauthvariablename, you can try the following: 
create a file called test.php on your web server with the following line:  
`<?php phpinfo(); ?>`  
then, bring this up in your web browser.  search for your login id and it should display the server 
variable in the lefthand column.

if interoperability isn’t working (you’ll know it’s not working when you get red errors), verify that you 
have the database setting and that your database user can connect to that database.  also, be sure to complete installation of both modules involved.

if your remote user auth variable isn’t working, verify that you have `register_globals = on`
