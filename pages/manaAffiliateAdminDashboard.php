<?php
namespace WHMCS\Pages;
use WHMCS\ClientArea;
use WHMCS\Module\Addon\Mana_affiliate\Services\Products;
use WHMCS\Module\Addon\Mana_affiliate\Services\Translate;

require __DIR__ . '/../init.php';

$translator = Translate::getInstance();

$ca = new ClientArea();
if ($_SESSION['adminid']){
    $ca->setTemplate('manaaffiliateAdminDashboard');
}
else {
	$ca->setTemplate('content_restricted');
}
$ca->setPageTitle($translator->translate('Manage Sites'));
$ca->initPage();
// Check login status
if ($ca->isLoggedIn()) {
    $client = $ca->getClient();
    if ($client) {
        $productsService = Products::getInstance();
        $ids = $productsService->getProductIds();
        $services = array_reduce($ids, function($allServices, $pid){
            return array_merge($allServices, localAPI('GetClientsProducts', ['pid' => $pid])['products']['product']);
        }, []);
        $ca->assign('services', $services);
    }
} else {
    // User is not logged in
    $ca->assign('userFullname', 'Guest');
}
$ca->output();