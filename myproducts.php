<?php
  /**
   * Author: shionphan
   * site: http://www.joomla178.com
  */
  use \Illuminate\Database\Capsule\Manager as Capsule;

  define("CLIENTAREA",true);
  require("init.php");

  define("FORCESSL",true); // Uncomment to force the page to use https://

  $ca = new WHMCS_ClientArea(); 
  $ca->initPage();

  //$ca->requireLogin(); // Uncomment this line to require a login to access this page

  # To assign variables to the template system use the following syntax.
  # These can then be referenced using {$variablename} in the template.

  $ca->assign('variablename', $value);
 
  if ($ca->isLoggedIn()) {

  // Get url params
  function is_set_param($param){
    $current_url = $_SERVER["QUERY_STRING"];
    $arr = explode('&',$current_url);
    $value = '';
    foreach ($arr as $k=>$v) {
      $left_c = explode('=',$v);
      if ($left_c[0] == $param) {
        $value = $left_c[1];
        break;
      }
    }
    return $value;
  }
  // print_r(is_set_param('page'));

  // Get Userid
  $userid = $ca->getUserID();

  // Get User's Product/Service list which status are Active and Completed
  $services = Capsule::table('tblhosting')
    ->where('userid', $userid)
    ->whereIn('domainstatus', ['Active', 'Completed'])
    ->orderBy('id', 'DESC')
    ->get();

  // Create custom Products/Services
  foreach ( $services as $key => $value ) {	
    $gid = Capsule::table('tblproducts')->where('id', $value->packageid)->first()->gid;
    $products[$key]['id']				= $value->id;
    $products[$key]['name'] 			= Capsule::table('tblproducts')->where('id', $value->packageid)->first()->name;
    $products[$key]['category'] = Capsule::table('tblproductgroups')->where('id', $gid)->first()->name;
    $products[$key]['amount']	= $value->amount;
    $products[$key]['domain']			= $value->domain;
    $products[$key]['nextduedate']	= $value->nextduedate;
    $products[$key]['billingcycle']	= $value->billingcycle;
  }

  // echo data as json
  echo json_encode($products);

} else {
  $result = array(
    'msg' => 'You were logout',
    'code' => 0,
    'data' => null
  );

  // echo error result
  echo (json_encode($result));
}

?>