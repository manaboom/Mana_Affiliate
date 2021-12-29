<?php
use WHMCS\Module\Addon\Mana_affiliate\Client\ClientDispatcher;
use WHMCS\Module\Addon\Mana_affiliate\Services\Products;
use WHMCS\Module\Addon\Mana_affiliate\Services\Agent;
use WHMCS\Module\Addon\Mana_affiliate\Services\Translate;
use WHMCS\Database\Capsule;

/**
 * WHMCS SDK Sample Addon Module Hooks File
 *
 * Hooks allow you to tie into events that occur within the WHMCS application.
 *
 * This allows you to execute your own code in addition to, or sometimes even
 * instead of that which WHMCS executes by default.
 *
 * @see https://developers.whmcs.com/hooks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.
$clientDispacher = new ClientDispatcher();

add_hook('OrderPaid', 1, function($vars) use ($clientDispacher){
    $order = localAPI('GetOrders', ['id' => $vars['orderId']])['orders']['order'][0];
    foreach($order['lineitems']['lineitem'] as $service){
        $service = localAPI('GetClientsProducts', ['serviceid' => $service['relid']])['products']['product'][0];
        $product = localAPI('GetProducts', ['pid' => $service['pid']])['products']['product'][0];
        $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'productsGroup')->first();
        if (!empty($setting)) {
            $manaProductsGroup = $setting->value;
        }
        if($product['gid'] == $manaProductsGroup){
            $result = $clientDispacher->dispatch('addSite', ['product' => $product, 'service' => $service, 'order' => $order]);
            if($result['ok']){
                $clientDispacher->dispatch('sendNewServiceEmail', ['product' => $product, 'user_name' => $result['user_name'], 'user_password' => $result['user_password'], 'url' => $result['data']['url'], 'userId' => $service['clientid']]);
            }
        }
    }
});

add_hook('CartSubdomainValidation', 1, function($vars) {
    $productsService = Products::getInstance();
    $translator = Translate::getInstance();
    $result = $productsService->checkDomain($vars['subdomain'].$vars['domain']);
    if(!$result['ok']){
        return [$translator->translate('Domain already taken, select another domain.')];
    }
});

add_hook('ClientAreaPageCart', 1, function() {
    if($_REQUEST['a'] == 'add' && isset($_REQUEST['pid'])){
        if($_SESSION['mana_theme_selected']){
            $_SESSION['mana_theme_selected'] = false;
        }
        else{
            $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'productsGroup')->first();
            if (!empty($setting)) {
                $manaProductsGroup = $setting->value;
            }
            $group = localAPI('GetProducts', ['pid' => $_REQUEST['pid']])['products']['product'][0]['gid'];
            if($group == $manaProductsGroup){ // check if it is a mana product
                header("Location: http://".$_SERVER[HTTP_HOST]."/whmcs/pages/manaAffiliateNewSite.php?pid=".$_REQUEST['pid']);
                exit;
            }
        }
        
    }
});

add_hook('ClientAreaHomepagePanels', 1, function($input) use ($clientDispacher) {
    $clientDispacher->dispatch('createClientPanel', $input);
});

add_hook('ClientAreaSidebars', 1, function($vars) use ($clientDispacher)
{
    if($_SERVER['PHP_SELF'] === "/whmcs/pages/manaAffiliateNewSite.php"){
        $clientDispacher->dispatch('showThemeCategories', $vars);
    }
});


class ManaDashboardWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'Site builder';
    protected $description = '';
    protected $weight = 2;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;
    protected $requiredPermission = '';

    public function getData()
    {
        return array();
    }

    public function generateOutput($data)
    {
        $remainingSites = "Error";
        $remainingDisk = "Error";
        $agentService = Agent::getInstance();
        $credit = $agentService->getCredit();
        if($credit['result'] == 'success'){
            $remainingDisk = round($credit['message']['disc']/1024, 2);
            $remainingSites = $credit['message']['sites'];
        }

        $productsService = Products::getInstance();
        $ids = $productsService->getProductIds();
        $services = array_reduce($ids, function($allServices, $pid){
            return array_merge($allServices, localAPI('GetClientsProducts', ['pid' => $pid])['products']['product']);
        }, []);
        $numberOfActiveServices = array_reduce($services, function($total, $service){
            if($service['status'] == 'Active'){
                $total++;
            }
            return $total;
        }, 0);
        
        return '
        <div class="widget-content-padded">
            <div class="feed-element">
                <div>
                    <strong class="pull-right text-navy">'.$remainingDisk.' GB</strong>
                    <div>Remaining Disk</div>
                </div>
            </div>
            <div class="feed-element" style="padding: 10px 0;border-bottom: 1px solid #eee;">
                <div>
                    <strong class="pull-right text-navy">'.$remainingSites.'</strong>
                    <div>Remaining Sites</div>
                </div>
            </div>
            <div class="row" style="padding: 10px 0;">
                <div class="col-sm-7">
                    <div class="item">
                        <div class="data">
                            <div class="note">
                                Active sites
                            </div>
                            <div class="number">
                                <span style="color:#49a94d;">'.$numberOfActiveServices.'</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 text-right">
                    <a href="/whmcs/pages/manaAffiliateAdminDashboard.php" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-right"></i> View List
                    </a>
                </div>
            </div>
            <div class="text-center">
                <a href="/whmcs/pages/manaAffiliateAdminDashboard.php?newSite" class="btn btn-primary btn-sm">New Site</a>
                <br>
            </div>
        </div>
        ';
    }
}
