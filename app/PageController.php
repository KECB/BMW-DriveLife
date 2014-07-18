<?php
include 'contentAdapter_c1.php';
use com\bmw\developer\cloud\c1\data as sdk;

 
@header('Content-type: application/json; charset=UTF-8');
 
define("SCREEN_ID_MAIN", "mainScreen");
define("SCREEN_ID_DETAILPAGE", "detailPage");

// define for http request
$userId='test';
define("SEARCH_LIST_URL", 'http://115.28.141.68/BMW/index.php/home/Index/index/userId/'. $userId);
define("DETAILPAGE_URL", 'http://115.28.141.68/BMW/index.php/home/Index/getDetailData/id/');
 
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
     
    $resultData = getPageData(SEARCH_LIST_URL,$postDataArray);
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $countArray = count($resultArray);
 
    // create Composite List Page
    $page = new sdk\page\LinksPage();
 
    for( $i = 0; $i < $countArray; $i++)
    {
        $uid=$resultArray[$i]['Id'];
        $startTime = $resultArray[$i]['StartTime'];
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
        $tempLink = new sdk\component\LinkColumns(SCREEN_ID_DETAILPAGE);
        if ($address='') {
            # code...
            $tempLink->addCell(new sdk\component\TableCell($startTime))->addCell(new sdk\component\TableCell($Description))->addCell(new sdk\component\TableCell($name));
        }else{
            $tempLink->addCell(new sdk\component\TableCell($startTime))->addCell(new sdk\component\TableCell($address))->addCell(new sdk\component\TableCell($name));
        }
        $tempLink->addReturnParam("uid", $uid);
        $page->addLink($tempLink);
    }
    return $page;
}

function getDetailPage($uid)
{
    // baidu poi detail
    // $postDataArray = array('ak' => '869f0962811faf2b184ad35d4e485b27',
    //         'output' => 'json',
    //         'scope' => '2',
    //         'uid' => $uid
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
 
    // $contactInformation = new sdk\component\ContactInformation($name, new sdk\component\Coordinates(doubleval($lat), doubleval($lon)));
    // $contactInformation->setAddress(new sdk\component\Address("", "", "", "ÖÐ¹ú", $address));
    // $contactInformation->setEmail("BMWBill@bmw.com");
    // $contactInformation->setPhone($telephone);
    // $contactInformation->setUrl("www.bmw.com");
    // $page = new sdk\page\PoiDetailPage($contactInformation, $_SESSION[KEYWORD]);
    //http://img0.tuicool.com/3AV3I3.jpg
    $resultData = getPageData(DETAILPAGE_URL.$uid,$postDataArray);
    $jsondecode = convertToJSON($resultData);
    $resultArray = end($jsondecode);
    $countArray = count($resultArray);
    $name=$resultArray[0]['Contact'];
    $address=$resultArray[0]['Location'];
    $telephone=$resultArray[0]['Mobile']; 
    $Description=$resultArray[0]['Description'];

    $page = new sdk\page\TextPage();
    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($name);
    $currentParagraph->setTTS(true);
    $page->addParagraph($currentParagraph);

    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($address);
    $currentParagraph->setTTS(true);
    $page->addParagraph($currentParagraph);

    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($telephone);
    $currentParagraph->setTTS(true);
    $page->addParagraph($currentParagraph);

    $currentParagraph = new sdk\component\Paragraph();
    $currentParagraph->setText($Description);
    $currentParagraph->setTTS(true);
    $page->addParagraph($currentParagraph);

    
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
    // $html = file_get_contents($resultURL);
    $html = file_get_contents($URLHeader);
    return  $html;
}
 
/*
 * Convert Data into JSON formate
 */
function convertToJSON($json)
{
	try{
    	return json_decode($json,true);
	}
	catch (Exception $e){
	    $errorPage = new sdk\page\ErrorPage();
	    $errorPage -> setNoContentAvailableError();
	    echo "Error: " . $errorPage -> toJson() . "Screen : " . $_GET["screenID"];
	}
}