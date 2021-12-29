<?php

namespace WHMCS\Module\Addon\Mana_affiliate\Services;
use WHMCS\Module\Addon\Mana_affiliate\Models\Manaboom;

class Sites{
  private static $instance = null;

  private function __construct(){
    $this->manaboom = Manaboom::getInstance();
  }

  public function getInstance(){
    if (self::$instance == null)
    {
      self::$instance = new self();
    }
 
    return self::$instance;
  }
 
  function addSite($args): array{
    return $this->manaboom->request('POST', 'sites', $args);
  }

}