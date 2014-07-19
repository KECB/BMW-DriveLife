<?php
include 'contentAdapter_c1.php';
use \com\bmw\developer\cloud\c1\data as sdk;
    // create manifest object
    $header = new sdk\manifest\Header("DriveLife", "1.0", "mainScreen", false, "icon-event.png");
    $manifest = new sdk\manifest\Manifest($header);
 
    // create main screen, a list of todo things
    $main = new sdk\manifest\LinksPageScreen(new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"),
                                            new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"),
                                            new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"));
    // create table configuration for table cells in a link
    $tableConfig = new sdk\TableConfigRelWidths(array(
                                                    new sdk\TableColRelWidth(30, "caption"),
                                                    new sdk\TableColRelWidth(50, "caption"),
                                                    new sdk\TableColRelWidth(20, "caption")));
    $main->addTableConfiguration($tableConfig);
    $manifest->addScreen("mainScreen", $main);
    // create detail page
    $detailPage = new sdk\manifest\TextWithToolbarPageScreen(new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"),
                                                   new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"),
                                                   new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"));
    
    $manifest->addScreen("detailPage", $detailPage);
    // create poi detail page
    $poiDetailPage = new sdk\manifest\BaiduMapPoiDetailPageScreen(new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"),
                                            new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"),
                                            new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/app/PageController.php"));
    $manifest->addScreen("poiDetailPage", $poiDetailPage);
    // create search result detail page
    // $searchResult = new sdk\manifest\BaiduMapPoiDetailPageScreen(new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/PageController.php"),
    //                                                              new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/PageController.php"),
    //                                                              new sdk\manifest\RemoteData("http://bmwdrive.duapp.com/PageController.php"));

    // $manifest->addScreen("searchResult", $searchResult);
 
    // write manifest file
    $h = fopen("manifest.json", "w");
    fwrite($h, $manifest->toJson());
    fclose($h);
    echo success;
?>