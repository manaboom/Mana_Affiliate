<?php

namespace WHMCS\Module\Addon\Mana_affiliate\Services;
use WHMCS\Module\Addon\Mana_affiliate\Models\Manaboom;
use WHMCS\Module\Addon\Mana_affiliate\Services\Translate;
use WHMCS\Database\Capsule;

class Products{
  private static $instance = null;

  private function __construct(){
    $this->manaboom = Manaboom::getInstance();
    $this->translator = Translate::getInstance();
  }

  public function getInstance(){
    if (self::$instance == null)
    {
      self::$instance = new self();
    }
 
    return self::$instance;
  }
 
  function getThemes(int $categoryId = 0):array{
    $params = $categoryId == 0 ? [] : ['category_id' => $categoryId];
    // $params['language'] = '$this->translator->getUserLanguage()';
    return $this->manaboom->request('GET', 'themes', $params)['data'] ?? [];
  }

  function getThemeCategories(): array
  {
    return $this->manaboom->request('GET', 'categories')['data'];
  }

  function checkDomain(string $domain): array{
    return $this->manaboom->request('GET', 'sites/domains/', ['check_domain' => $domain]);
  }

  function getProductsGroupId(){
    $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'productsGroup')->first();
    if (!empty($setting)) {
      return intval($setting->value);
    }
  }

  function getProductIds(){
    $results = localAPI('GetProducts', ['gid' => $this->getProductsGroupId()]);
    if($results['result'] == 'success'){
      return array_column($results['products']['product'], 'pid');
    }
  }

  function createSampleProducts($groupId){
    $products = [];
    
    $currencies = array_column(localAPI('GetCurrencies', [])['currencies']['currency'], 'id');

    $options = array(
      'type' => 'hostingaccount',
      'gid' => $groupId,
      'paytype' => 'recurring',
      'showdomainoptions' => true,
      'description' => 'this is a sample product',
      'subdomain' => '.ov2.com',
      'autosetup' => 'payment',
      'module' => 'autorelease',
    );
    $options['pricing'] = [];
    foreach($currencies as $cur){
      $options['pricing'][$cur] = ['monthly' => '-1',  'quarterly' => '-1', 'semiannually' => '150000.00', 'annually' => '-1', 'biennially' => '-1', 'triennially' => '-1'];
    }
    $options['name'] = 'Sample Product (6 month)';
    $products[] = localAPI('AddProduct', $options)['pid'];

    $options['pricing'] = [];
    foreach($currencies as $cur){
      $options['pricing'][$cur] = ['monthly' => '-1',  'quarterly' => '-1', 'semiannually' => '-1', 'annually' => '200000.00', 'biennially' => '-1', 'triennially' => '-1'];
    }
    $options['name'] = 'Sample Product (1 year)';
    $products[] = localAPI('AddProduct', $options)['pid'];

    $options['pricing'] = [];
    foreach($currencies as $cur){
      $options['pricing'][$cur] = ['monthly' => '-1',  'quarterly' => '-1', 'semiannually' => '-1', 'annually' => '-1', 'biennially' => '250000.00', 'triennially' => '-1'];
    }
    $options['name'] = 'Sample Product (2 years)';
    $products[] = localAPI('AddProduct', $options)['pid'];

    foreach($products as $pid){
      Capsule::table('tblcustomfields')->insert(
        [
            'type' => 'product',
            'relid' => $pid,
            'fieldname' => 'Site title',
            'fieldtype' => 'text',
            'description' => 'title of site in Boom Site Builder',
            'required' => 'on',
            'showorder' => 'on',
            'showinvoice' => 'on'
        ]
      );
      
      Capsule::table('tblcustomfields')->insert(
        [
            'type' => 'product',
            'relid' => $pid,
            'fieldname' => 'Disk',
            'fieldtype' => 'dropdown',
            'description' => 'Volume of disk specified to this site in Megabytes',
            'fieldoptions' => '2048',
            'required' => 'on',
            'showorder' => 'on',
            'showinvoice' => 'on'
        ]
      );

      Capsule::table('tblcustomfields')->insert(
        [
            'type' => 'product',
            'relid' => $pid,
            'fieldname' => 'Username',
            'fieldtype' => 'text',
            'description' => 'Username of site in Boom Site Builder',
        ]
      );

      Capsule::table('tblcustomfields')->insert(
        [
            'type' => 'product',
            'relid' => $pid,
            'fieldname' => 'Password',
            'fieldtype' => 'text',
            'description' => 'Password of site in Boom Site Builder',
        ]
      );

      Capsule::table('tblcustomfields')->insert(
        [
            'type' => 'product',
            'relid' => $pid,
            'fieldname' => 'Note',
            'fieldtype' => 'text'
        ]
      );
    }
  }
}