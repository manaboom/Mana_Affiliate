<?php

namespace WHMCS\Module\Addon\Mana_affiliate\Services;

class Agent{
  private static $instance = null;

  private function __construct(){
  }

  public function getInstance(){
    if (self::$instance == null)
    {
      self::$instance = new self();
    }
 
    return self::$instance;
  }
 
  function getCredit(): array{
    // Next version
    return [];
  }

}