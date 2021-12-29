<?php

namespace WHMCS\Module\Addon\Mana_affiliate\Client;
use WHMCS\View\Menu\Item as MenuItem;
use WHMCS\Module\Addon\Mana_affiliate\Services\Products;
use WHMCS\Module\Addon\Mana_affiliate\Services\Translate;
use WHMCS\Module\Addon\Mana_affiliate\Services\Sites;
use WHMCS\Database\Capsule;

/**
 * Sample Client Area Controller
 */
class Controller {

    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return array
     */
    public function index($vars)
    {
        // Get common module parameters
        $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
        $version = $vars['version']; // eg. 1.0
        $LANG = $vars['_lang']; // an array of the currently loaded language variables

        // Get module configuration parameters
        $configTextField = $vars['Text Field Name'];
        $configPasswordField = $vars['Password Field Name'];
        $configCheckboxField = $vars['Checkbox Field Name'];
        $configDropdownField = $vars['Dropdown Field Name'];
        $configRadioField = $vars['Radio Field Name'];
        $configTextareaField = $vars['Textarea Field Name'];

        return array(
            'pagetitle' => 'Sample Addon Module',
            'breadcrumb' => array(
                'index.php?m=addonmodule' => 'Sample Addon Module',
            ),
            'templatefile' => 'publicpage',
            'requirelogin' => false, // Set true to restrict access to authenticated client users
            'forcessl' => false, // Deprecated as of Version 7.0. Requests will always use SSL if available.
            'vars' => array(
                'modulelink' => $modulelink,
                'configTextField' => $configTextField,
                'customVariable' => 'your own content goes here',
            ),
        );
    }

    /**
     * Secret action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return array
     */
    public function secret($vars)
    {
        // Get common module parameters
        $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
        $version = $vars['version']; // eg. 1.0
        $LANG = $vars['_lang']; // an array of the currently loaded language variables

        // Get module configuration parameters
        $configTextField = $vars['Text Field Name'];
        $configPasswordField = $vars['Password Field Name'];
        $configCheckboxField = $vars['Checkbox Field Name'];
        $configDropdownField = $vars['Dropdown Field Name'];
        $configRadioField = $vars['Radio Field Name'];
        $configTextareaField = $vars['Textarea Field Name'];

        return array(
            'pagetitle' => 'Sample Addon Module',
            'breadcrumb' => array(
                'index.php?m=addonmodule' => 'Sample Addon Module',
                'index.php?m=addonmodule&action=secret' => 'Secret Page',
            ),
            'templatefile' => 'secretpage',
            'requirelogin' => true, // Set true to restrict access to authenticated client users
            'forcessl' => false, // Deprecated as of Version 7.0. Requests will always use SSL if available.
            'vars' => array(
                'modulelink' => $modulelink,
                'configTextField' => $configTextField,
                'customVariable' => 'your own content goes here',
            ),
        );
    }

    public function createClientPanel($homePagePanels){
        $translator = Translate::getInstance();
        if(get_class($homePagePanels) != "WHMCS\View\Menu\Item"){
            return;
        }
        $summaryPanel = $homePagePanels->addChild('Mana affiliate panel', array(
            'label' => $translator->translate('Site Builder'),
            'icon' => 'fa-cloud',
            'extras' => array(
                'color' => 'blue',
            )
        ));
        $summaryPanel->addChild("Remaining sites", array(
            'label' => "",
            'icon' => 'fa-tools',
            'badge' => '<div class="pull-right">
                            <a href="submitticket.php" class="btn btn-default bg-color-blue btn-xs">
                                <i class="fa fas fa-question"></i> '.$translator->translate('Support').'
                            </a>
                            <a href="?action=services" class="btn btn-default bg-color-blue btn-xs">
                                <i class="fa fas fa-list"></i> '.$translator->translate('Sites List').'
                            </a>
                            <a href="pages/manaAffiliateNewSite.php" class="btn btn-default bg-color-blue btn-xs">
                                <i class="fa fas fa-plus"></i> '.$translator->translate('New Site').'
                            </a>
                        </div>',
            'order' => 1,
        ));
        $summaryPanel->moveToFront();
    }

    public function showThemeCategories($vars):void{
        $primarySidebar = \Menu::primarySidebar();
        $translator = Translate::getInstance();
        $category = intval($_REQUEST['category'] ?? 0);
        // Remove prevoius panel
        while($primarySidebar->count() > 0){
            $primarySidebar->removeChild($primarySidebar->getLastChild());
        }
        $primarySidebar->addChild('Mana affiliate theme categories', array(
            'label' => $translator->translate('Categories'),
            'order' => '0',
            'icon' => 'fa-list'
        ));
        $primarySidebar->getChild('Mana affiliate theme categories')
                ->addChild('همه', array(
                'label' => 'همه',
                'uri' => $_SERVER['PHP_SELF'].'?category=0',
                'order' => 10
        ))->setClass(( $category == 0 ? 'active' : ''));
        $productsService = Products::getInstance();
        $themeCategories = $productsService->getThemeCategories();
        foreach($themeCategories as $themeCategory){
            $primarySidebar->getChild('Mana affiliate theme categories')
                ->addChild($themeCategory['name'], array(
                'label' => $themeCategory['name'],
                'uri' => $_SERVER['PHP_SELF'].'?category='.$themeCategory['category_id'],
                'order' => 20,
        ))->setClass(( $category == $themeCategory['category_id'] ? 'active' : ''));
        }
    }

    public function addSite($args): array{
        $product = $args['product'];
        $service = $args['service'];
        $order = $args['order'];
        $client = localAPI('GetClientsDetails', ['clientid' => $service['clientid']])['client'];
        $setting = Capsule::table('mod_manaaffiliatetemp')->select('themeId')->where('clientId', $client['id'])->first();
        if (!empty($setting)) {
            $theme = $setting->themeId;
        }
        $size = 100; // Default size
        $title = 'Site title'; // Default title
        foreach($service['customfields']['customfield'] as $field){
            if($field['name'] == 'Disk'){
                $size = $field['value'];
            }
            if($field['name'] == 'Site title'){
                $title = $field['value'];
            }
        }
        $args = [];
        $args['title'] = $title;
        if(strpos($service['domain'], '.ov2.com') !== false){ // Its a subdomain
            $args['subdomain'] = strstr($service['domain'], '.ov2.com', true);
        }
        else{
            $args['domain'] = $service['domain'];
        }

        // Generate password
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars),0,10);
        
        $dateTobeExpired = [
            'One Time' => '+10 year',
            'Monthly' => 'next month',
            'Quarterly' => '+3 month',
            'Semi-Annually' => '+6 month',
            'Annually' => '+12 month',
            'Biennially' => '+24 month',
            'Triennially' => '+36 month'
        ];
        $args['expire_date'] = date('Y-m-d H:i:s', strtotime($dateTobeExpired[$service['billingcycle']]));
        $args['user_email'] = $client['email'];
        $args['max_size'] = $size;
        $args['theme_id'] = $theme;
        $args['is_test'] = 0;
        $args['category_id'] = 0;
        $args['user_name'] = $client['firstname'];
        $args['user_password'] = $password;
        $sitesService = Sites::getInstance();
        $result = $sitesService->addSite($args);
        if($result['ok']){
            $postData = array(
                'serviceid' => $service['id'],
                'customfields' => base64_encode(serialize(["Username" => $args['user_name'], 'Password' => $args['user_password'], 'Note' => 'It is suggested to change the site password after service activation'])));
            $r = localAPI('UpdateClientProduct', $postData);
        }
        $result['theme'] = $theme;
        $result['user_password'] = $args['user_password'];
        $result['user_name'] = $args['user_name'];
        return $result;
    }

    function sendNewServiceEmail($args){
        $translator = Translate::getInstance();
        $product = $args['product'];
        $productName = $product['name'];
        $username = $args['user_name'];
        $password = $args['user_password'];
        $url = $args['url'];
        $userId = $args['userId'];
        
        $data = array(
            'customtype' => 'general',
            'id' => $userId,
            'customsubject' => $translator->translate('Service Activated').' ['.$url.']',
            'customvars' => base64_encode(
                serialize(
                        [
                        "username"=>$username,
                        "password"=>$password,
                        "productName"=>$product['name'],
                        "groupId"=>$product['gid'],
                        "url"=>$url,
                        "productName"=>$productName,
                        ]
                    )
                ),
        );
        if($translator->getUserLanguage() == 'farsi'){
            $data['custommessage'] = file_get_contents(realpath(__DIR__).'/../../templates/serviceActivatedEmailFa.tpl');
        }
        else{
            $data['custommessage'] = file_get_contents(realpath(__DIR__).'/../../templates/serviceActivatedEmailEn.tpl');
        }
        $results = localAPI('SendEmail', $data);
    }
}
