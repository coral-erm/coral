<?php

class ExportConfig extends DatabaseObject {

  // This getRawConf. function gets used to generate the admin
  // table. We could modify getAdminExportConfigDisplay.php
  // and the updateConfiguration function below to use the
  // getConfiguration() function here without too much effort.
  public function getRawConfiguration() {
    $query = "SELECT * FROM ExportConfig";
    $result = $this->db->processQuery($query, 'assoc');
    return $result;
  }
  public function getConfiguration() {
    $config = $this->getRawConfiguration();
    return array_reduce($config, function($a, $setting) {
      $a[$setting['shortName']] = (int)$setting['enabled'] > 0;
      return $a;
    }, []);
  }

  public function updateConfiguration($id, $enabled) {
    $query = "UPDATE `ExportConfig` SET `enabled`=$enabled WHERE `id`=$id";
    $result = $this->db->processQuery($query, 'assoc');
  }
}

?>
