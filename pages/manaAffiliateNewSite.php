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
$ca->setPageTitle($translator->translate('Select Template'));
$ca->initPage();
$ca->requireLogin(); // require a login to access this page
$ca->setTemplate('manaaffiliate');
\Menu::primarySidebar('serviceList');

// Check login status
if ($ca->isLoggedIn()) {
    $client = $ca->getClient();
    if ($client) {
        $productsService = Products::getInstance();
        $pid = intval($_REQUEST['pid'] ?? null);
        $category = intval($_REQUEST['category'] ?? 0);
        $themes = $productsService->getThemes($category);
        $currentPage = intval($_REQUEST['page'] ?? 1);
        $pageThemes = array_slice($themes, max(0, ($currentPage-1)*9), 9);
        $ca->assign('productId', $pid);
        $ca->assign('dict', $translator->getDict());
        $ca->assign('numOfPages', ceil(count($themes)/9));
        $ca->assign('currentPage', $currentPage);
        $ca->assign('themes', $pageThemes);
        $ca->assign('category', $category);
    }
} else {
    // User is not logged in
    $ca->assign('userFullname', 'Guest');
}
$ca->output();