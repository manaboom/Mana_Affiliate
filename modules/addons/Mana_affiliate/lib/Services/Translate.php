<?php
namespace WHMCS\Module\Addon\Mana_affiliate\Services;
use WHMCS\Database\Capsule;

class Translate{
    private $_keys = array();

    private static $instance = null;
    private $language;

    private function __construct(){
        $dir = realpath(dirname(__FILE__) . "/../../lang");
        $this->language = $this->_getUserLanguage();
        $englishFile = $dir . "/english.php";
        $currentFile = $dir . "/" . $this->language . ".php";

        if (file_exists($englishFile)) {
            require $englishFile;
            $this->_keys = $_ADDONLANG;
        }

        if (file_exists($currentFile)) {
            require $currentFile;
            $this->_keys = array_merge($this->_keys, $_ADDONLANG);
        }
    }

    public function getInstance(){
        if (self::$instance == null)
        {
        self::$instance = new self();
        }
    
        return self::$instance;
    }

    public function translate($msg, $placeholders = array()){
        if (isset($this->_keys[$msg])) {
            $msg = $this->_keys[$msg];
            foreach ($placeholders as $key => $val)
                $msg = str_replace("@{$key}@", $val, $msg);
        }
        return $msg;
    }

    private function _getUserLanguage(){
        $language = $_SESSION["Language"];
        return is_null($language) ? 'english' : $language;
    }

    public function getUserLanguage(){
        return $this->language;
    }

    public function getDict(){
        return $this->_keys;
    }
}