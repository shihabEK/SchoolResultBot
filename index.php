<?php
ini_set("allow_url_open", 1);
$response = file_get_contents("php://input");
$update = json_decode($response, true);
function processMessage()
{
$response = file_get_contents("php://input");
$update = json_decode($response, true);
 $actionName=$update["result"]["action"];
    $AppNo = $update["result"]["parameters"]["AppNo"];
    $portal = $update["result"]["parameters"]["portal"];
    $AppList=$update["result"]["parameters"]["AppList"];
    $UserId=$update["result"]["parameters"]["UserId"];
    $Spon=$update["result"]["parameters"]["Spon"];
    $pnr = $update["result"]["parameters"]["pnr"];
    switch($actionName)
    {
        case 'VisaStatus' :   
            
                        $getStatusUrl="https://www.gdrfa.ae/portal/pls/portal/INIMM_DB.QRY_RESULT_VISA_PRINT.show?p_arg_names=_show_header&p_arg_values=YES&p_arg_names=app_dt1&p_arg_values=&p_arg_names=app_dt2&p_arg_values=&p_arg_names=app_id&p_arg_values=".$AppNo."&p_arg_names=spn&p_arg_values=";
                        $speech = "Status for ".$AppNo."\n".$getStatusUrl;
                        $displayText = "Status for ".$AppNo."\n".$getStatusUrl;
                        $source = "VisaStatus";
                        break;
        case 'PostList' :   
            
                        $AppList = rtrim($AppList);
                        $output = str_replace(PHP_EOL, ':', $AppList);
                        $output="https://www.gdrfa.ae/portal/pls/portal/INIMM_DB.DBPK_CALL.VISA_POSTING?p_idstring=".$output."&p_apptp=1&p_user_login=".$UserId."&p_spon=".$Spon."&p_rtm=1";

                        $speech = "Post Link for".$AppList." \n URL: ".$output;
                        $displayText = "Post Link for".$AppList." \n URL: ".$output;
                        $source = "PostList";
                        break;
     case 'otbStatus'   : 
                          $getStatusUrl="http://manage.otb-network.com/application/API/Status.php?pnr=".urlencode($pnr);
      $content=file_get_contents($getStatusUrl);
      
      $Obj=json_decode($content, true);
      $Status=$Obj['Status'];
      $noPax=$Obj['NoPax'];
      $Date=$Obj['Date'];
      $ErrorType=$Obj['ErrorType'];
      if($ErrorType=='200'){
                  if($Status=='Done'){
                        $speech = "OTB Updated for ".$noPax." Pax(s) against, PNR : ".$pnr;
                        $displayText = "OTB Updated for ".$noPax." Pax(s) against, PNR : ".$pnr;
                        }else if($Status=='Pending'){
                        $speech = "OTB Pending for ".$noPax." Pax(s) against, PNR : ".$pnr;
                        $displayText = "OTB Pending for ".$noPax." Pax(s) against, PNR : ".$pnr;
                  }else{
                   $speech = "OTB updation Failed for the PNR : ".$pnr;
                        $displayText ="OTB updation Failed for the PNR : ".$pnr;
                  }
      }else{
                        $speech = "OTB Request for the PNR : ".$pnr." not Found, Please check few minutes later";
                        $displayText = "OTB Request for the PNR : ".$pnr." not Found, Please check few minutes later";
      }
                         
                        $source = "OTBNetwork";
                        break;
            default :   $speech = $city." Something went wrong ! Try again...";
                        $displayText = $actionName."Something went wrong ! Try again...";
                        $source = "DGO-Server";
                        break;
    }
    
    sendMessage(array("source"=>$source,"speech"=>$speech,"displayText"=>$displayText,"contextOut"=> array()));
                        
    
      
    
}
function sendMessage($parameters)
{
    header('Content-type: application/json');
    echo json_encode($parameters);
}
if (isset($update["result"]["action"]))
{
     processMessage();
}
?>
