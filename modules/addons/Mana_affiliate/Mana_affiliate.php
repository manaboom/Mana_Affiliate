<?php

/**
 * Require any libraries needed for the module to function.
 * require_once __DIR__ . '/path/to/library/loader.php';
 *
 * Also, perform any initialization required by the service's library.
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Mana_affiliate\Admin\AdminDispatcher;
use WHMCS\Module\Addon\Mana_affiliate\Client\ClientDispatcher;
use WHMCS\Module\Addon\Mana_affiliate\Services\Products;
use WHMCS\Module\Addon\Mana_affiliate\Services\Translate;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


function Mana_Affiliate_config(){
    $productsService = Products::getInstance();
    $translator = Translate::getInstance();
    $themesList = $productsService->getThemes();
    $themes = [];
    foreach($themesList as $theme){
        $themes[$theme['theme_id']] = $theme['name'];
    }

    // If fields are being saved
    if(isset($_REQUEST['fields']['Mana_affiliate']['manaAffiliateCreateSampleProducts'])){
        $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'manaAffiliateCreateSampleProducts')->first();
        if (!empty($setting)) {
            $lastValue = $setting->value;
            $curValue = $_REQUEST['fields']['Mana_affiliate']['manaAffiliateCreateSampleProducts'];
            if($lastValue != 'on' && $curValue == 'on'){
                $productsService->createSampleProducts($_REQUEST['fields']['Mana_affiliate']['productsGroup']);
            }
        }
    }

    return [
        'name' => 'Mana_Affiliate',
        'description' => 'This module provides the basic functions of mana site builder.',
        'author' => 'Mana Andishe',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.0',
        'fields' => [
            // a text field type allows for single line text input
            'API_key' => [
                'FriendlyName' => $translator->translate('API Key'),
                'Type' => 'password',
                'Size' => '25',
                'Description' => $translator->translate('Site Builder API Key'),
            ],
            'productsGroup' => [
                'FriendlyName' => $translator->translate('Group Code'),
                'Type' => 'text',
                'Size' => '25',
                'Description' => $translator->translate('Create a group for site builder products and enter its code here'),
            ],
            // the dropdown field type renders a select menu of options
            'defaultTemplate' => [
                'FriendlyName' => $translator->translate('Default Template'),
                'Type' => 'dropdown',
                'Options' => $themes,
                'Default' => '1',
                'Description' => $translator->translate('Default Sites Template (you must first enter the Site Builder API Key.)'),
            ],
            'manaAffiliateCreateSampleProducts' => [
                'FriendlyName' => $translator->translate('Create Sample Products'),
                'Type' => 'yesno',
                'Description' => $translator->translate('Create 3 sample products as template for further products'),
            ]
        ]
    ];
}

/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 *
 * @return array Optional success/failure message
 */
function Mana_Affiliate_activate(){
    $translator = Translate::getInstance();
    try {
        Capsule::schema()->create(
                'mod_manaaffiliatetemp', // mod is short for Module
                function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->integer('clientId');
                    $table->integer('themeId');
                    $table->dateTime('date');
                }
            );

        return [
            'status' => 'info',
            'description' => $translator->translate('Plugin Activated. Enter the required data in the "Configure" section to get started.'),
        ];
    } catch (\Exception $e) {
        return [
            'status' => "error",
            'description' => 'Unable to create "mod_manaresellerlog" table: ',
        ];
    }
}

/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 *
 * @return array Optional success/failure message
 */
function Mana_Affiliate_deactivate(){
    try {
        Capsule::schema()
            ->dropIfExists('mod_manaaffiliatetemp');

        return [
            'status' => 'success',
            'description' => '',
        ];
    } catch (\Exception $e) {
        return [
            "status" => "error",
            "description" => "Unable to drop mod_manaaffiliatetemp: {$e->getMessage()}",
        ];
    }
}

/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 *
 * @return void
 */
function Mana_Affiliate_upgrade($vars){
    $currentlyInstalledVersion = $vars['version'];
}

/**
 * Admin Area Output.
 *
 * Called when the addon module is accessed via the admin area.
 *
 * @return string
 */
function Mana_Affiliate_output($vars){
    // Get common module parameters
    $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=Mana_Affiliate
    $version = $vars['version']; // eg. 1.0
    $_lang = $vars['_lang']; // an array of the currently loaded language variables

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $dispatcher = new AdminDispatcher();
    $response = $dispatcher->dispatch($action, $vars);
    echo $response;
}

/**
 * Admin Area Sidebar Output.
 *
 * Used to render output in the admin area sidebar.
 *
 * @param array $vars
 *
 * @return string
 */
function Mana_Affiliate_sidebar($vars){
    // Get common module parameters
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $_lang = $vars['_lang'];

    $sidebar = '';
    return $sidebar;
}

/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 *
 * @return array
 */
function Mana_Affiliate_clientarea($vars){
    $modulelink = $vars['modulelink']; // eg. index.php?m=Mana_Affiliate
    $version = $vars['version']; // eg. 1.0
    $_lang = $vars['_lang']; // an array of the currently loaded language variables
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $dispatcher = new ClientDispatcher();
    return $dispatcher->dispatch($action, $vars);
}
