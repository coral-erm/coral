<?php
session_start();
require_once('../directory.php');

if (!function_exists('debug')) {
  function debug($value) {
    echo '<pre>'.print_r($value, true).'</pre>';
  }
}

class CORALInstaller {

  public $db; // because CORALInstaller::query does unwanted things with result
  public $error;
  protected $statusNotes;
  protected $config;
  protected $updates = array(
    "1.1" => array(
      "privileges" => array("ALTER","CREATE"),
      "installedTablesCheck" => array("CatalogingStatus"),
      "description" => "<p>The 1.1 update to the CORAL Resources module includes a number of enhancements:</p>
      <ul>
        <li>Added a cataloging tab to resource records, allowing tracking of cataloging details and notes.</li>
        <li>Search resources by active routing steps and cataloging status, as well as some minor performance enhancements to the search listings.</li>
        <li>The export file has been completely revamped.  Clicking the Excel icon on the resources search page now downloads a CSV file which includes many more fields and should open much more quickly.</li>
      </ul> 
      <p>This upgrade will connect to MySQL and run the CORAL Resources structure changes. No changes to the configuration file are required.  Database structure changes include:</p>
    	<ul>
    		<li>Create table CatalogingStatus and CatalogingType (configurable in the admin)</li>
    		<li>Add cataloging columns to the Resource table</li>
    		<li>Numerous indexes to improve search performance</li>
    	</ul>"
    ),
    "1.2" => array(
      "privileges" => array("ALTER","CREATE"),
      "installedTablesCheck" => array("ResourceSubject"),
      "description" => "<p>The 1.2 update to the CORAL Resources module includes a number of enhancements:</p>
      <ul>
        <li>Added coverage to the resource record.</li>
        <li>Added an alternative URL to the resources record.</li>
        <li>Add subject terms to the resource record.</li>
        <li>Changed how the related products are displayed for the resource.</li>		
        <li>Added defaultsort to the configuration.ini.  If used this changes the default sort order for the resources.  Example: defaultsort=\"TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) asc\"</li>				
      </ul> 
      <p>This upgrade will connect to MySQL and run the CORAL Resources structure changes. No changes to the configuration file are required.  Database structure changes include:</p>
    	<ul>
    		<li>Create subjects tables: GeneralSubject, DetailedSubject, GeneralDetailSubjectLink, and ResourceSubject. (subjects are configurable in the admin)</li>
    		<li>Add coverageText, resourceAltURL columns to the Resource table.</li>
    	</ul>"
    ),	
    "1.3" => array(
      "privileges" => array("ALTER","CREATE"),
      "installedTablesCheck" => array("CostDetails"),
      "description" => "<p>The 1.3 update to the CORAL Resources module includes new cost history features that enable the tracking of cost information throughout the lifetime of a subscription.</p>
      <p>This upgrade will connect to MySQL and run the CORAL Resources structure changes. Database structure changes include:</p>
    	<ul>
    		<li>Create the CostDetails table</li>
		<li>Rename subscriptionStartDate and subscriptionEndDate columns in the Resource table to currentStartDate and currentEndDate, respectively</li>
		<li>Add year, subscriptionStartDate, subscriptionEndDate, costDetailsID, costNote, and invoiceNum columns to the ResourcePayment table</li>
    	</ul>
      <p>After upgrading, you must change the <b>enhancedCostHistory</b> setting in the configuration file in order to turn on the new cost history features."
    ),
    "1.4" => array(
      "privileges" => array("ALTER","CREATE"),
      "installedTablesCheck" => array("Issue"),
      "description" => "<p>The 1.4 update to the CORAL Resources module includes the new Issue and Downtime tracking features.</p>
      <p>This upgrade will connect to MySQL and run the CORAL Resources structure changes. Database structure changes include:</p>
      <ul>
        <li>Create the Issue and associated tables</li>
        <li>Create the Downtime and associated tables</li>
      </ul>"
    ),
    "1.41" => array(
      "privileges" => array("ALTER"),
      "installedTablesCheck" => array("Issue","Downtime"),
      "tablePropertiesCheck" => array(array("table"=>"Downtime","requirements"=>"`Field`='endDate' AND `Null`='YES'")),
      "description" => "<p>The 1.41 update to the CORAL Resources module includes bug fixes and enhancements for the new Issue and Downtime tracking features.</p>
      <p>This upgrade will connect to MySQL and run the CORAL Resources structure changes. Database structure changes include:</p>
      <ul>
        <li>Alter the Downtime table</li>
      </ul>"
    ) 
  );
  
  public function __construct() {
    $this->statusNotes = array();
    if (is_file($this->configFilePath())) {
      $this->statusNotes['config_file'] = "Configuration file found: ". $this->configFilePath();
      $this->config = new Configuration();
      $this->connect();
    } else {
      $this->statusNotes['config_file'] = "Configuration file not present: ". $this->configFilePath();
    }
  }
  
  public function connect($username = null, $password = null) {
    $this->error = '';
		$host = $this->config->database->host;
		if ($username === null) {
		  $username = $this->config->database->username;
		}
		if ($password === null) {
		  $password = $this->config->database->password;
	  }
		$this->db = new mysqli($host, $username, $password);
		if ($this->db->connect_error) {

		  $this->error = $this->db->connect_error;
		  if (!$this->error) {
		    $this->error = "Access denied for user '$username'";
		  }
	  } else {
  		$databaseName = $this->config->database->name;
  		$this->db->select_db($databaseName);
  		$this->error = $this->db->error;
		}
		
		if ($this->error) {
      $this->statusNotes['database_connection'] = "Database connection failed: ".$this->error;
		  $this->db = null;
		} else {
      $this->statusNotes['database_connection'] = "Database connection successful";
    }
	}

    public function query($sql) {
        $result = $this->db->query($sql);
        $this->checkForError();
        return $result;
    }

	public function processQuery($sql) {
		$result = $this->db->query($sql);

		$this->checkForError();
		$data = array();

		if ($result instanceof mysqli_result) {
			while ($row = $result->fetch_array()) {
				array_push($data, $row);
			}
		} else if ($result) {
			$data = $this->db->insert_id;
		}

		return $data;
	}
	
	protected function checkForError() {
		if ($this->error = $this->db->error) {
			throw new Exception("There was a problem with the database: " . $this->error);
		}
	}
	
	public function getDatabaseName() {
	  return $this->config->database->name;
	}
  
  public function addErrorMessage($error) {
    if (!$this->hasErrorMessages()) {
      $_SESSION['installer_error_messages'] = array();
    }
    $_SESSION['installer_error_messages'] []= $error;
  }
  
  public function hasErrorMessages() {
    return isset($_SESSION['installer_error_messages']);
  }
  
  public function displayErrorMessages() {
    if ($this->hasErrorMessages()) {
			echo "<div style='color:red'><p><b>The following errors occurred:</b></p><ul>";
			foreach ($_SESSION['installer_error_messages'] as $err) {
				echo "<li>" . $err . "</li>";
			}
			echo "</ul></div>";
			unset($_SESSION['installer_error_messages']);
		}
  }
  
  public function addMessage($msg) {
    if (!$this->hasMessages()) {
      $_SESSION['installer_messages'] = array();
    }
    $_SESSION['installer_messages'] []= $msg;
  }
  
  public function hasMessages() {
    return isset($_SESSION['installer_messages']);
  }
  
  public function displayMessages() {
    if ($this->hasMessages()) {
			echo "<div style='color:green'><ul>";
			foreach ($_SESSION['installer_messages'] as $msg) {
				echo "<li>" . $msg . "</li>";
			}
			echo "</ul></div>";
			unset($_SESSION['installer_messages']);
		}
  }
  
  public function modulePath() {
    //returns file path for this module, i.e. /coral/resources/
    $replace_path = preg_quote(DIRECTORY_SEPARATOR."install");
    return preg_replace("@$replace_path$@", "", dirname(__FILE__));
  }

  public function configFilePath() {
    return $this->modulePath().'/admin/configuration.ini';
  }
  
  public function isDatabaseConfigValid() {
    return $this->config && $this->db;
  }
  
  public function hasPermission($permission) {
    if ($this->isDatabaseConfigValid()) {
      $grants = array();
      $permission = "(ALL PRIVILEGES|".strtoupper($permission).")";
      foreach ($this->query("SHOW GRANTS FOR CURRENT_USER()") as $row) {
        $grant = array_values($row)[0];
        if (strpos(str_replace('\\', '', $grant), $this->config->database->name) || strpos($grant, "ON *.*")) {
          if (preg_match("/(GRANT|,) $permission(,| ON)/i",$grant)) {
            return true;
          }
        }
      }
    }
    return false;
  }
  
  public function hasPermissions($permissions) {
    foreach($permissions as $permission) {
      if (!$this->hasPermission($permission)) {
        return false;
      }
    }
    return true;
  }
  
  public function tableExists($table) {
    $query = "SELECT count(*) count FROM information_schema.`COLUMNS` WHERE table_schema = '" . $this->config->database->name . "' AND table_name='". $table ."'";
    foreach ($this->query($query) as $row) {
      if ($row['count'] > 0) {
        return true;
      }
    }
    return false;
  }


  public function tableIsCurrent($table,$check) {
    $query = "SHOW COLUMNS FROM {$this->config->database->name}.{$table} WHERE {$check}";
    $results = $this->query($query);
    if ($results->num_rows > 0) {
      return true;
    }
    return false;
  }
  
  public function indexExists($table, $index) {
    $result = $this->query("SHOW INDEXES FROM $table WHERE Key_name = '$index'");
    return count($result) > 0;
  }
  
  public function installed() {
    if ($this->isDatabaseConfigValid()) {
      $installedTablesCheck = array("Resource","Workflow");
      foreach ($installedTablesCheck as $table) {
        if (!$this->tableExists($table)) {
          $this->statusNotes["installed"] = "Module not installed. Could not find table: ".$table;
          return false;
        }
      }
      $this->statusNotes["installed"] = "Module already installed. Found tables: ".implode(", ", $installedTablesCheck);
      return true;
    }
    return false;
  }

  public function debuggingNotes() {
    return "<h4>Installation Debugging:</h4><p>".implode('<br/>', $this->statusNotes)."</p>";
  }
  
  public function getNextUpdateVersion() {
    foreach($this->updates as $version => $details) {
      if (!$this->isUpdateInstalled($version)) {
        return $version;
      }
    }
  }
  
  public function isUpdateReady($version) {
    return $this->installed() && $this->getNextUpdateVersion() == $version;
  }
  
  public function isUpdateInstalled($version) {
    if ($this->installed()) {
      $installedTablesCheck = $this->updates[$version]["installedTablesCheck"];
      $tablePropertiesCheck = (!empty($this->updates[$version]["tablePropertiesCheck"])) ? $this->updates[$version]["tablePropertiesCheck"]:null;
      $isUpdated = true;
      if ($installedTablesCheck) {
        foreach ($installedTablesCheck as $table) {
          if (!$this->tableExists($table)) {
            $this->statusNotes["version_".$version] = "Version $version not installed. Could not find table: ".$table;
            $isUpdated = false;
          }
        }
        if ($tablePropertiesCheck) {
          foreach ($tablePropertiesCheck as $propertyCheck) {
            if (!$this->tableIsCurrent($propertyCheck['table'],$propertyCheck['requirements'])) {
              $this->statusNotes["version_".$version] = "Version $version not installed. Outdated table: ".$propertyCheck['table'];
              $isUpdated = false;
            }
          }
        }
        if ($isUpdated) {
          $this->statusNotes["version_".$version] = "Version $version already installed. Found tables: ".implode(", ", $installedTablesCheck);
          return true;
        }
      }
    }
    return false;
  }
  
  public function getUpdate($version) {
    return $this->updates[$version];
  }

  public function upToDate() {
    if (!$this->installed() || $this->getNextUpdateVersion()) {
      return false;
    } else {
      return true;
    }
  }
  
  public function header($title = 'CORAL Installation') {
    include('header.php');  
  }
  
  public function footer() {
    $installer = $this;
    include('footer.php');  
  }
}
?>
