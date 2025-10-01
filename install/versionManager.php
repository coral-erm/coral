<?php 
	require_once("common/Config.php");
    require_once("installer.php");
    class versionManager {
        private $installerVersion = "2025.10";
        private $currentlyInstalledVersion = FALSE;
        private $changeToBeMade = FALSE;
        private $needToUpgrade = FALSE;
        private $needToInstall = FALSE;
        private $usesBackslash = FALSE;
        private $validModules = [
            "auth", "licensing", "management", 
            "organizations", "reports", "resources", 
            "usage",
        ];
        private $validVersionOrder = [
            "1.9.0", 
            "2.0.0", 
            "3.0.0", "3.0.1", 
            "2020.02", "2020.09", 
            "2024.04", "2024.10", 
            "2025.04", "2025.04.01", "2025.04.02", "2025.04.03", "2025.04.04", "2025.04.05", "2025.04.06",
            "2025.10",
        ];
        public function __construct(){
            $this->setBackslash();
            $this->setCurrentlyInstalledVersion();
            $this->validateVersions();
            $this->setInstalledSettings();
        }

        public function checkForChange(){
            $this->needToInstall = ($this->currentlyInstalledVersion == FALSE);
            $latestVersionToInstall = array_slice($this->validVersionOrder, -1)[0];
            $versionsDoNotMatch = ($this->currentlyInstalledVersion !== $latestVersionToInstall);
            $this->needToUpgrade = (!$this->needToInstall && $versionsDoNotMatch);
            $this->changeToBeMade = ($this->needToInstall || $this->needToUpgrade);
            return $this->changeToBeMade;
        }

        public function getVersionsToLoad(){
            if($this->needToInstall){
                return $this->getInstallVersions();
            }
            if($this->needToUpgrade){
                return $this->getUpgradeVersions();
            }
        }

        public function getInstallerVersion(){
            return $this->installerVersion;
        }

        public function getCurrentlyInstalledVersion(){
            return $this->currentlyInstalledVersion;
        }

        private function getInstallVersions(){
            $versionInstallArray['action'] = Installer::VERSION_STRING_INSTALL;
            $versionInstallArray['versions'] = $this->validVersionOrder;
            return $versionInstallArray;
        }



        private function getUpgradeVersions(){
            $versionUpgradeArray['action'] = Installer::VERSION_STRING_UPGRADE;
            $currentVersionIndex = array_search($this->currentlyInstalledVersion, $this->validVersionOrder);
            $firstUpgradeIndex = $currentVersionIndex + 1;
            $versionUpgradeArray['versions'] = array_slice($this->validVersionOrder, $firstUpgradeIndex);
            return $versionUpgradeArray;
        }

        private function setBackslash(){
            $this->usesBackslash = (DIRECTORY_SEPARATOR == '\\');
        }

        private function setCurrentlyInstalledVersion(){
            try {
                $version = Config::getInstallationVersion();
            } catch (\Exception $e) {
                $version = false;
            }
            $this->currentlyInstalledVersion = $version;
        }

        private function setInstalledSettings(){
            $this->needToInstall = ($this->currentlyInstalledVersion == FALSE);
            $versionsDoNotMatch = ($this->currentlyInstalledVersion !== $this->installerVersion);
            $this->needToUpgrade = (!$this->needToInstall && $versionsDoNotMatch);
            $this->changeToBeMade = ($this->needToInstall || $this->needToUpgrade);
        }

        private function validateConfigurationsExist(){
            $configurationFilesExist = array_reduce($this->validModules, function($carry, $module){
                $moduleExists = file_exists("{$module}/admin/configuration.ini");
                return ($carry || $moduleExists);
            });
            return $configurationFilesExist;
        }

        private function validateInstalledVersion(){
            $currentVersion = ($this->currentlyInstalledVersion);
            $notInVersionArray = (!in_array($currentVersion, $this->validVersionOrder));

            $errorMsg = [
                "<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer."),
                "<br",
                _("The version currently installed is not a recognised version."),
                "<br>",
                _("The version currently installed is: ")."<strong>{$currentVersion}</strong>",
            ];
            $output = ($currentVersion && $notInVersionArray) ? $errorMsg : FALSE;
            return $output;
        }

        private function validateInstallerOrder(){
            //The installerVersion should be the last version in the validVersionOrder
            $lastValidVersion = array_slice($this->validVersionOrder, -1)[0];
            $currentInstallerVersion = $this->installerVersion;
            $notLastValidVersion = ($lastValidVersion !== $currentInstallerVersion);

            $errorMsg = [
                "<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer.")." ",
                _("Version of Installer does not match the last installation version in the Valid Version Order list."),
                "<br>",
                _("The installer version is: ")."<strong>{$currentInstallerVersion}</strong>.<br>",
                _("The last installation version is: ")."<strong>{$lastValidVersion}</strong>",
            ];

            $output = ($notLastValidVersion) ? $errorMsg : FALSE;
            return $output;
        }


        public function validateVersions(){
            $errorChecks = [
                $this->validateInstalledVersion(),
                $this->validateInstallerOrder(),
            ];

            foreach($errorChecks as $failedTest){
                if($failedTest){
                    return $failedTest;
                }
            }
            return false;
        }
    }
?>