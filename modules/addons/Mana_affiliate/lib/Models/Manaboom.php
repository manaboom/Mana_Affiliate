<?php
namespace WHMCS\Module\Addon\Mana_affiliate\Models;
use WHMCS\Database\Capsule;
class Manaboom{
  private static $instance = null;
  private $token;
  private $manaURL;
  private function __construct(){
    $this->manaURL = 'https://ov2.com/wp-json/mana/v1/';

    $setting = Capsule::table('tbladdonmodules')->select('value')->where('module', 'Mana_Affiliate')->where('setting', 'API_key')->first();
    if (!empty($setting)) {
      $this->token = $setting->value;
    }
  }

  public static function getInstance(){
    if (self::$instance == null)
    {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function request(string $method, string $route, array $request = []): array
    {
        $request['token'] = $this->token;
        $ch = curl_init();
        $url = $this->manaURL . $route;
        if(strtoupper($method) === "GET"){
          $url .= '?'.http_build_query($request);
        }
        else{
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($ch);
        if (curl_error($ch)) {
          die('Unable to connect: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        // Decode response
        $r = json_decode($response, true);
        if ($r == null) {
          die('Service Provider error.');
        }
        return $r;
    }
}