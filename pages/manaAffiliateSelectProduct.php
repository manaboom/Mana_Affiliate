<?php
namespace WHMCS\Pages;
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Mana_affiliate\Services\Products;
use WHMCS\Module\Addon\Mana_affiliate\Services\Translate;
define('CLIENTAREA', true);

require __DIR__ . '/../init.php';

$translator = Translate::getInstance();

$ca = new ClientArea();
$ca->setPageTitle($translator->translate('Redirecting to Shopping Cart'));
$ca->initPage();
$ca->requireLogin(); // require a login to access this page
$ca->setTemplate('manaaffiliateselectproduct');

// Check login status
if ($ca->isLoggedIn()) {
    $client = $ca->getClient();
    if ($client) {
        $theme = intval($_REQUEST['theme'] ?? function(){
            $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'defaultTemplate')->first();
            if (!empty($setting)) {
                return $setting->value;
            }
            return 1;
        });
        Capsule::table('mod_manaaffiliatetemp')->where('clientId', '=', $client->id)->delete();
        Capsule::table('mod_manaaffiliatetemp')->insert(
            [
                'themeId' => $theme,
                'clientId' => $client->id,
                'date' => date("Y-m-d H:i:s", time()),
            ]
        );

        $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'productsGroup')->first();
        if (!empty($setting)) {
            $productsGroup = $setting->value;
        }
        global $CONFIG;
        $_SESSION['mana_theme_selected'] = true;
        $pid = intval($_REQUEST['pid'] ?? null);
        if($pid != null){
            header("Location: ".$CONFIG['SystemURL']."/cart.php?a=add&pid=$pid");
            exit;
        }
        header("Location: ".$CONFIG['SystemURL']."/cart.php?gid=$productsGroup");
        exit;
    }
} else {
    // User is not logged in
    $ca->assign('userFullname', 'Guest');
}
$ca->output();