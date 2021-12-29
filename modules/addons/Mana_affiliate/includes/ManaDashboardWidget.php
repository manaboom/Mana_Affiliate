<?php
namespace WHMCS\Module\Addon\Mana_Affiliate\Includes;

class ManaDashboardWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'Mana affiliate';
    protected $description = '';
    protected $weight = 150;
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
        return '<div class="widget-content-padded">Mana affiliate</div>';
    }
}