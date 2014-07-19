<?php
include 'contentAdapter_c1.php';
use com\bmw\developer\cloud\c1\data as sdk;

 
@header('Content-type: application/json; charset=UTF-8');
 
define("SCREEN_ID_MAIN", "mainScreen");
define("SCREEN_ID_DETAILPAGE", "detailPage");
define("SCREEN_ID_POIDETAILPAGE", "poiDetailPage");

// define for http request
$userId='test';
define("SEARCH_LIST_URL", 'http://115.28.141.68/BMW/index.php/home/Index/index/userId/'. $userId);
define("DETAILPAGE_URL", 'http://115.28.141.68/BMW/index.php/home/Index/getDetailData/id/');
define("SEARCH_POI_LIST_URL", "http://api.map.baidu.com/place/v2/search");
define("SEARCH_POI_DETAIL_URL", 'http://api.map.baidu.com/place/v2/detail');


// get sid 
$sid = sessionID();
if ($sid != null) {
    session_id($sid);
    session_start();
}
 
// page control
try{
    switch ($_GET["screenID"]){
        case SCREEN_ID_MAIN:
            $main = getMainPage();
            echo $main -> toJson();
            break;
        case SCREEN_ID_DETAILPAGE:
            $eventsPage = getDetailPage($_GET['uid']);
            echo $eventsPage->toJson();
            break;
        case SCREEN_ID_POIDETAILPAGE:
            $poiPage = getPoiDetailPage($_GET['uid']);
            echo $poiPage->toJson();
            break;
        default:
            throw new Exception("Unexpected Screen ID" . $_GET["screenID"]);
            break;
    }
}
catch (Exception $e){
    $errorPage = new sdk\page\ErrorPage();
    $errorPage -> setNoContentAvailableError();
    echo "Error: " . $errorPage -> toJson() . "Screen : " . $_GET["screenID"];
}

// Main Page
function getMainPage()
{
	// $userId = "test";
 //    // send request and get json data
 //    $searchKeyWord = "宝马";
 //    $positionString = $latitude . ',' . $longitude;
 
 //    // baidu poi
 //    $postDataArray = array('query' => $searchKeyWord,
 //                           'location' => $positionString,
 //                           'radius' => '5000',
 //                           'output' => 'json',
 //                           'ak' => '869f0962811faf2b184ad35d4e485b27'
 //    );
     
    $resultData = getPageData(SEARCH_LIST_URL,"");
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $countArray = count($resultArray);
 
    // create Composite List Page
    $page = new sdk\page\LinksPage();
 
    for( $i = 0; $i < $countArray; $i++)
    {
        $uid=$resultArray[$i]['Id'];
        $startTime = $resultArray[$i]['StartTime'];
        $startTime = substr ($startTime, 5,11);
        $name=$resultArray[$i]['Contact'];
        $address=$resultArray[$i]['Location'];
        $telephone=$resultArray[$i]['Mobile']; 
        $Description=$resultArray[$i]['Description'];
         //only display result which has address and telephone
         // if($address == null)
         // {
         //     $address = '/';
         //     continue;
         // }
         // if($telephone == null)
         // {
         //     $telephone = '/';
         //     continue;    
         // }
        if ($address=='') {
            # code...
            $tempLink = new sdk\component\LinkColumns(SCREEN_ID_DETAILPAGE);
            $tempLink->addCell(new sdk\component\TableCell($startTime))->addCell(new sdk\component\TableCell($Description))->addCell(new sdk\component\TableCell($name));
        }else{
            $tempLink = new sdk\component\LinkColumns(SCREEN_ID_POIDETAILPAGE);
            $tempLink->addCell(new sdk\component\TableCell($startTime))->addCell(new sdk\component\TableCell($address))->addCell(new sdk\component\TableCell($name));
        }
        $tempLink->addReturnParam("uid", $uid);
        $page->addLink($tempLink);
    }
    return $page;
}

function getDetailPage($uid)
{
    //http://img0.tuicool.com/3AV3I3.jpg
    $resultData = getPageData(DETAILPAGE_URL.$uid,"");
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $countArray = count($resultArray);
    $name=$resultArray[0]['Contact'];
    $address=$resultArray[0]['Location'];
    $telephone=$resultArray[0]['Mobile']; 
    $Description=$resultArray[0]['Description'];

    $page = new sdk\page\TextWithToolbarPage();
    //
    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($name);
    $page->addParagraph($currentParagraph);

    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($address);
    $page->addParagraph($currentParagraph);

    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($telephone);
    $page->addParagraph($currentParagraph);

    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($Description);
    $currentParagraph->setTTS(true);
    $page->addParagraph($currentParagraph);

    if ($telephone!='') {
        $toolbarPhoneButton = new sdk\component\ToolbarPhoneButton($telephone,$telephone);
        $page->addToolbarItem($toolbarPhoneButton);
    }
    if ($address!='') {
        $coordinates = new sdk\component\Coordinates(doubleval('31.1632'),doubleval('112.1536'));
        $poiEntry = new sdk\component\PoiEntry($name,$coordinates);
        $toolbarNavButton = new sdk\component\ToolbarNavButton($poiEntry,$address);
        $page->addToolbarItem($toolbarNavButton);
    }
    
    
    return $page;
}

function getPoiDetailPage($userid)
{
    
    //http://img0.tuicool.com/3AV3I3.jpg
    $resultData = getPageData(DETAILPAGE_URL.$userid,'');
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $countArray = count($resultArray);
    $name=$resultArray[0]['Contact'];
    $address=$resultArray[0]['Location'];
    $telephone=$resultArray[0]['Mobile']; 
    $Description=$resultArray[0]['Description'];

    // baidu poi
    $positionString = $latitude . ',' . $longitude;
    $postDataArray = array('query' => $address,
                           'location' => $positionString,
                           'radius' => '500000',
                           'output' => 'json',
                           'ak' => '869f0962811faf2b184ad35d4e485b27'
    );
    $resultData = getPageData(SEARCH_POI_LIST_URL,$postDataArray);
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $countArray = count($resultArray);
    $poiUid = '';
    for( $i = 0; $i < $countArray; $i++)
    {
        $temp = each($resultArray);
        $itemDataArray = $temp['value'];
        $name = $itemDataArray['name'];
        $address = $itemDataArray['address'];
        $telephone = $itemDataArray['telephone'];
        //only display result which has address and telephone
        // if($address == null)
        // {
        //     $address = '/';
        //     continue;
        // }
        // if($telephone == null)
        // {
        //     $telephone = '/';
        //     continue;    
        // }
        $poiUid = $itemDataArray['uid'];
        $location = $itemDataArray['location'];
        $lat = $location['lat'];
        $lon = $location['lng'];
        if ($poiUid!='') {
            break;
        }
    }
    // // baidu poi detail
    $postDataArray = array('ak' => '869f0962811faf2b184ad35d4e485b27',
            'output' => 'json',
            'scope' => '2',
            'uid' => $poiUid
    );
 
    $resultData = getPageData(SEARCH_POI_DETAIL_URL,$postDataArray);
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $itemDataArray = $resultArray;
 
    // $name = $itemDataArray['name'];
    $address = $itemDataArray['address'];
    // $telephone = $itemDataArray['telephone'];
    $location = $itemDataArray['location'];
    $lat = $location['lat'];
    $lon = $location['lng'];
    // baidu poi detail
    // $postDataArray = array('ak' => '869f0962811faf2b184ad35d4e485b27',
    //         'output' => 'json',
    //         'scope' => '2',
    //         'uid' => '8ee4560cf91d160e6cc02cd7'
    // );
 
    // $resultData = getPageData(SEARCH_POI_DETAIL_URL,$postDataArray);
    // $jsondecode = convertToJSON($resultData);
    // $resultArray = end($jsondecode);
    // $itemDataArray = $resultArray;
 
    // $name = $itemDataArray['name'];
    // $address = $itemDataArray['address'];
    // $telephone = $itemDataArray['telephone'];
    // $location = $itemDataArray['location'];
    // $lat = $location['lat'];
    // $lon = $location['lng'];
 
    $contactInformation = new sdk\component\ContactInformation($name, new sdk\component\Coordinates(doubleval($lat), doubleval($lon)));
    $contactInformation->setAddress(new sdk\component\Address("", "", "", "", $address));
    $contactInformation->setEmail("BMWBill@bmw.com");
    $contactInformation->setPhone("133333333");
    $contactInformation->setUrl("www.bmw.com");
    $page = new sdk\page\PoiDetailPage($contactInformation);
    return $page;
}

// Utilities Function
 
/*
 * Get Html Page Data (JSON)
 */
function getPageData($URLHeader,$postDataArray)
{
    $resultURL = $URLHeader . '?';
    $postPart = '';
    $arrayCount =  count($postDataArray);
 
    for($i = 0; $i < $arrayCount; $i++)
    {
        $temp= each($postDataArray);
        $postPart = $postPart . $temp['key'] . '=' . $temp['value'];
        if($i != $arrayCount - 1)
        {
            $postPart = $postPart . '&';
        }
    }
    $resultURL = $resultURL . $postPart;
    $html = file_get_contents($resultURL);
    // $html = file_get_contents($URLHeader);
    return  $html;
}
 
/*
 * Convert Data into JSON formate
 */
function convertToJSON($json)
{
	// try{
    	return json_decode($json,true);
	// }
	// catch (Exception $e){
	    // $errorPage = new sdk\page\ErrorPage();
	    // $errorPage -> setNoContentAvailableError();
	    //secho "Error: " . $errorPage -> toJson() . "Screen : " . $_GET["screenID"];
	// }
}