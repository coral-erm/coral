<?php

class EbscoKbService {

/*
 * Vars
 */
    protected $config;
    public $error;
    protected $response;
    private $queryPath = [];
    public $queryParams = [];
    public $type;

    static $apiUrl = 'https://api.ebsco.io/rm/rmaccounts/';

    static $defaultSearchParameters = [
        'search' => '',
        'orderby' => 'relevance',
        'offset' => 1,
        'count' => 20,
        'type' => 'titles',
        'searchfield' => 0,
        'selection' => 0,
        'resourcetype' => 0,
        'contenttype' => 0,
        'vendorId' => null,
        'packageId' => null,
    ];

    static $queryTypes = [
        'packages' => [
            'selectDisplay' => 'Packages',
            'class' => 'EbscoKbPackage',
            'listKey' => 'packagesList',
            'params' => ['search','orderby','offset','count','searchfield','selection','contenttype'],
        ],
        'titles' => [
            'selectDisplay' => 'Titles',
            'class' => 'EbscoKbTitle',
            'listKey' => 'titles',
            'params' => ['search','orderby','offset','count','searchfield','selection','resourcetype'],
        ],
        'vendors' => [
            'selectDisplay' => 'Vendors',
            'class' => 'EbscoKbVendor',
            'listKey' => 'vendors',
            'params' => ['search','orderby','offset','count'],
        ],
    ];

    static $titleSearchFieldOptions = [
        0 => 'Title Name',
        1 => 'Publisher',
        2 => 'ISXN',
        3 => 'Subject',
    ];

    static $titleResourceTypeOptions = [
        0 => 'All',
        1 => 'Journal',
        2 => 'Newsletter',
        3 => 'Report',
        4 => 'Proceedings',
        5 => 'Website',
        6 => 'Newspaper',
        7 => 'Unspecified',
        8 => 'Book',
        9 => 'Book Series',
        10 => 'Database',
        11 => 'Thesis & Dissertation',
        12 => 'Streaming Audio',
        13 => 'Streaming Video',
        14 => 'Audio Book',
    ];

    static $selectionOptions = [
        0 => 'All',
        1 => 'Selected in EBSCO Kb',
        2 => 'Not Selected',
        3 => 'Ordered through EBSCO',
    ];

    static $packageContentTypeOptions = [
        0 => 'All',
        1 => 'Aggregated Fulltext',
        2 => 'Abstract & Index',
        3 => 'ebook',
        4 => 'ejournal',
        5 => 'Print',
        6 => 'Unknown',
        7 => 'Online Reference',
    ];

/*
 * General purpose
 */

    /**
     * @var EbscoKbService (Pattern Singleton)
     */
    private static $instance = null;
// -------------------------------------------------------------------------
    /**
     * Return the unique instance of class (design pattern singleton)
     */
    public static function getInstance(){
        if (is_null(self::$instance)){
            self::$instance = new EbscoKbService();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->config = new Configuration;
        $this->checkForError();
        $this->queryPath = [
            $this->config->settings->ebscoKbCustomerId,
        ];
    }

/*
 * Error handling
 */
    protected function checkForError() {
        $errors = [];
        if($this->config->settings->ebscoKbEnabled != 'Y'){
            $errors = 'EBSCO Kb is not enabled';
        }
        $customer_id = $this->config->settings->ebscoKbCustomerId;
        if(empty($customer_id)){
            $errors[] = 'There is no EBSCO Customer ID';
        }
        $apiKey = $this->config->settings->ebscoKbApiKey;
        if(empty($apiKey)){
            $errors[] = 'There is no EBSCO Kb API key';
        }

        if(!empty($errors)){
            $this->error = $errors;
            throw new Exception(_("There was a problem with the EBSCO Kb configuration: ") . implode(', ', $errors));
        }
    }

    protected function checkParams($params) {
        if(empty($params['type'])){
            throw new Exception(_("There was a problem with the EBSCO Kb Service Query: No search type was selected."));
        }
        if(!in_array($params['type'], array_keys(self::$queryTypes))){
            throw new Exception(_("There was a problem with the EBSCO Kb Service Query: The provided search type (".$params['type'].") could not be found"));
        }
    }

    protected function checkResponseErrors() {
        if(!empty($this->response['Errors'])){
            $errors = array_map(function($e){
                return $e['Message'];
            }, $this->response['Errors']);
            throw new Exception(_("There was a problem with the EBSCO Kb Service Query: ".implode(', ',$errors)));
        }

        if(!empty($this->response['message'])){
            throw new Exception(_("There was a problem with the EBSCO Kb Service Query: ".$this->response['message']));
        }

    }

/*
 * Session helpers
 */

    public static function setSearch($search) {
        $search = self::generateSearch($search);
        CoralSession::set('ebscoKbSearch', $search);
    }

    public static function resetSearch() {
        EbscoKbService::setSearch(array());
    }

    public static function getSearch() {
        if (!CoralSession::get('ebscoKbSearch')) {
            EbscoKbService::resetSearch();
        }
        return CoralSession::get('ebscoKbSearch');
    }

/*
 * Query Functions
 */
    public function createQuery($search)
    {
        // validate necessary params
        $this->checkParams($search);
        $this->queryPath = [];
        $search = self::generateSearch($search);

        // set the query type
        $this->type = $search['type'];

        // if vendor id is set, this is a LEAST a packages query
        if(!empty($search['vendorId'])) {
            $this->type = 'packages';
            $this->queryPath[] = 'vendors';
            $this->queryPath[] = $search['vendorId'];
        }

        // if package id is set, this MUST be a titles query
        if(!empty($search['packageId'])) {
            $this->type = 'titles';
            $this->queryPath[] = 'packages';
            $this->queryPath[] = $search['packageId'];
        }

        // add the path parameter last
        $this->queryPath[] = $this->type;

        $this->queryParams = self::cleanParams($search);
        if(empty($this->queryParams['search'])){
            $this->queryParams['orderby'] = substr($this->type,0,-1).'name';
        }
    }

    public static function generateSearch($search = []){
        $providedParamKeys = array_keys($search);
        foreach(self::$defaultSearchParameters as $key => $value){
            if(in_array($key, $providedParamKeys)){
                $search[$key] = trim($search[$key]);
            } else {
                $search[$key] = $value;
            }
        }
        return $search;
    }

    private function cleanParams($search = [])
    {
        foreach(array_keys($search) as $key){
            if(!in_array($key, self::$queryTypes[$this->type]['params'])){
                unset($search[$key]);
            }
        }
        return $search;
    }

    public function getTitle($titleId)
    {
        $this->queryPath = ['titles', $titleId];
        $this->execute();
        return new EbscoKbTitle($this->response);
    }

    public function getVendor($vendorId)
    {
        $this->queryPath = ['vendors', $vendorId];
        $this->execute();
        return new EbscoKbVendor($this->response);
    }

    public function getPackage($vendorId, $packageId)
    {
        $this->queryPath = ['vendors', $vendorId, 'packages', $packageId];
        $this->execute();
        return new EbscoKbPackage($this->response);
    }

    public function execute($method = 'GET')
    {
        array_unshift($this->queryPath,$this->config->settings->ebscoKbCustomerId);
        $url = self::$apiUrl.implode('/',$this->queryPath);

        $ch = curl_init();
        $headers = [
            'Accept: application/json',
            'x-api-key: '.$this->config->settings->ebscoKbApiKey,
        ];

        if ($method != 'GET') {
            $headers[] = 'Content-Type: application/json';
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            if ($method == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            }
        }

        if(!empty($this->queryParams)){
            if ($method === 'GET') {
                $url .= '?'.http_build_query($this->queryParams);
            } else {
                $params = json_encode($this->queryParams);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                $headers[] = 'Content-Length: '.strlen($params);
            }
        }
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close ($ch);

        try {
            $this->response = json_decode($response, true);
            $this->checkResponseErrors();
        } catch(Exception $e) {
            $this->response = [];
            $this->error = $e->getMessage();
        }
    }

    public function numResults()
    {
        return empty($this->response) ? 0 : $this->response['totalResults'];
    }

    public function results()
    {
        $class = self::$queryTypes[$this->type]['class'];
        $listKey = self::$queryTypes[$this->type]['listKey'];

        return array_map(function($e) use ($class){
            return new $class($e);
        }, $this->response[$listKey]);
    }

/*
 * Selection Functions
 */

    public function setPackage($vendorId, $packageId, $selected = true) {
        $this->queryPath = ['vendors', $vendorId, 'packages', $packageId];
        $this->queryParams = ['isSelected' => $selected];
        $this->execute('PUT');
        return $this->response;
    }

    public function setTitle($vendorId, $packageId, $titleId, $selected = true) {
        $this->queryPath = ['vendors', $vendorId, 'packages', $packageId, 'titles', $titleId];
        $this->queryParams = ['isSelected' => $selected];
        $this->execute('PUT');
        return $this->response;
    }
}