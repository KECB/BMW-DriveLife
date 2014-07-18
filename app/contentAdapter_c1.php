<?php

namespace {
    class _ContentAdapter {
        function toJson() {
            $this->cleanJson();
            return toJson($this);
        }

        function cleanJson() {
        }

    }

    function sessionID() {
        if (!isset($_GET["sessionID"])) {
            return null;
        }
        else {
            $hex = "";
            for($i = 0; $i < strlen($_GET["sessionID"]); $i++) {
                $hex .= sprintf("%02x", ord($_GET["sessionID"][$i])); 
            }
            return $hex;
        }
    }
    function filterNull($o) {
        return $o !== null;
    }
    
    function json_filter($o) {
        if (is_object($o)) {
            $o->cleanJson();
            $o = (array) $o;
        }
        // primitives end the recursion
        if(!is_array($o)) {
            return $o;
        }
        // special handling for dictionaries, which are marked by a dummy entry
        if (isset($o["_DICTMAINTAINER"])) {
            if(sizeof($o) == 1) { // make sure empty dicts are returned as empty objects not lists
                return (object) array();
            }
            else {
                unset($o["_DICTMAINTAINER"]);
            }
        }
        // filter out null values
        $filtered = array_filter(array_map('json_filter', $o), 'filterNull');
        return $filtered;
    }
    
    function toJson($o) {
        return json_encode(json_filter($o));
    }
}

namespace com\bmw\developer\cloud\c1\data {
    class AbstractListItem extends \_ContentAdapter {
        function __construct() {
            $this->bottom = null;
            $this->top = null;
        }

        function setBottomSeparator() {
            $this->bottom = true;
            return $this;
        }

        function setTopSeparator() {
            $this->top = true;
            return $this;
        }

    }

    class AbstractCheckbox extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($caption, $value) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            parent::__construct();
            $this->selected = null;
            $this->value = $value;
            $this->caption = $caption;
        }

        function _getCompositeListItemType() {
            return 'checkbox';
        }

        function setSelected($selected) {
            if (!is_bool($selected)) throw new \Exception('$selected must be of type boolean');
            $this->selected = $selected;
            return $this;
        }

    }

    class AbstractIcon extends \_ContentAdapter {
        function __construct($image = null, $cssClass = null) {
            if ($image != null && !($image instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$image must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            if ($cssClass != null && !is_string($cssClass)) throw new \Exception('$cssClass must be of type string');
            $this->image = $image;
            $this->cssClass = $cssClass;
        }

    }

    class AbstractLink extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct() {
        }

    }

    class AbstractToolbarButton extends \_ContentAdapter {
        function __construct($type, $toolTip = null) {
            if (!is_string($type)) throw new \Exception('$type must be of type string');
            if ($toolTip != null && !is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            $this->toolTip = $toolTip;
            $this->type = $type;
            $this->disabled = null;
        }

        function disable() {
            $this->disabled = true;
            return $this;
        }

    }

    class InternalLink extends \com\bmw\developer\cloud\c1\data\AbstractLink {
        function __construct($screen) {
            if (!is_string($screen)) throw new \Exception('$screen must be of type string');
            $this->compositeItem = null;
            $this->icon = null;
            $this->captionText = null;
            $this->captionTextOnFocus = null;
            $this->linkTarget = new \com\bmw\developer\cloud\c1\data\component\LinkTarget($screen);
        }

        function _getCompositeListItemType() {
            return 'internalLink';
        }

        function setVariableContent($compositeItem) {
            if (!method_exists($compositeItem, '_getCompositeItemType')) throw new \Exception('$compositeItem must be a composite item type');
            $this->compositeItem = array('type' => $compositeItem->_getCompositeItemType(), 'data' => $compositeItem);
            return $this;
        }

        function addReturnParam($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->linkTarget->addReturnParam($name, $value);
            return $this;
        }

    }

    class TableColConfig extends \_ContentAdapter {
        function __construct($type, $allowedType, $floatingRow) {
            if (!is_string($type)) throw new \Exception('$type must be of type string');
            if (!in_array($allowedType, array('text','caption','appImage','remoteImage','doubleCaption','imageMultiCaption','any'))) throw new \Exception('value given in $allowedType not found in enumeration');
            if (!in_array($floatingRow, array('noDisplay','firstRow','secondRow','span'))) throw new \Exception('value given in $floatingRow not found in enumeration');
            $this->allowedType = $allowedType;
            $this->align = null;
            $this->textAlign = null;
            $this->valign = null;
            $this->floatingRow = $floatingRow;
            $this->type = $type;
            $this->paddingLeft = null;
            $this->paddingRight = null;
        }

        function setTextAlignment($textAlign) {
            if (!in_array($textAlign, array('left','center','right','justify'))) throw new \Exception('value given in $textAlign not found in enumeration');
            $this->textAlign = $textAlign;
            return $this;
        }

        function setPaddingLeft($paddingLeft) {
            if (!is_string($paddingLeft)) throw new \Exception('$paddingLeft must be of type string');
            $this->paddingLeft = $paddingLeft;
            return $this;
        }

        function setPaddingRight($paddingRight) {
            if (!is_string($paddingRight)) throw new \Exception('$paddingRight must be of type string');
            $this->paddingRight = $paddingRight;
            return $this;
        }

        function setAlignment($align) {
            if (!in_array($align, array('left','center','right'))) throw new \Exception('value given in $align not found in enumeration');
            $this->align = $align;
            return $this;
        }

        function setVerticalAlignment($valign) {
            if (!in_array($valign, array('top','middle','bottom'))) throw new \Exception('value given in $valign not found in enumeration');
            $this->valign = $valign;
            return $this;
        }

    }

    class TableColPrioritized extends \com\bmw\developer\cloud\c1\data\TableColConfig {
        function __construct($priority, $type, $width, $allowedType, $floatingRow = null) {
            if (!in_array($priority, array('Visible','OptionalHigh','OptionalMediumn','OptionalLow'))) throw new \Exception('value given in $priority not found in enumeration');
            if (!is_string($type)) throw new \Exception('$type must be of type string');
            if (!is_int($width)) throw new \Exception('$width must be of type integer');
            if (!in_array($allowedType, array('text','caption','appImage','remoteImage','doubleCaption','imageMultiCaption','any'))) throw new \Exception('value given in $allowedType not found in enumeration');
            if (!in_array($floatingRow, array('noDisplay','firstRow','secondRow','span'))) throw new \Exception('value given in $floatingRow not found in enumeration');
            parent::__construct($type, $allowedType, $floatingRow);
            $this->priority = $priority;
            $this->width = $width;
        }

    }

    class TableColFixedWidth extends \com\bmw\developer\cloud\c1\data\TableColPrioritized {
        function __construct($widthPixels, $allowedType, $priority = 1, $floatingRow = 'firstRow') {
            if (!is_int($widthPixels)) throw new \Exception('$widthPixels must be of type integer');
            if (!in_array($allowedType, array('text','caption','appImage','remoteImage','doubleCaption','imageMultiCaption','any'))) throw new \Exception('value given in $allowedType not found in enumeration');
            if (!in_array($priority, array('Visible','OptionalHigh','OptionalMediumn','OptionalLow'))) throw new \Exception('value given in $priority not found in enumeration');
            if (!in_array($floatingRow, array('noDisplay','firstRow','secondRow','span'))) throw new \Exception('value given in $floatingRow not found in enumeration');
            parent::__construct($priority, 'fixedWidth', $widthPixels, $allowedType, $floatingRow);
        }

    }

    class TableColRelWidth extends \com\bmw\developer\cloud\c1\data\TableColConfig {
        function __construct($widthPercent, $allowedType, $floatingRow = 'firstRow') {
            if (!is_int($widthPercent)) throw new \Exception('$widthPercent must be of type integer');
            if (!in_array($allowedType, array('text','caption','appImage','remoteImage','doubleCaption','imageMultiCaption','any'))) throw new \Exception('value given in $allowedType not found in enumeration');
            if (!in_array($floatingRow, array('noDisplay','firstRow','secondRow','span'))) throw new \Exception('value given in $floatingRow not found in enumeration');
            parent::__construct('relWidth', $allowedType, $floatingRow);
            $this->width = $widthPercent;
        }

    }

    class TableColVarWidth extends \com\bmw\developer\cloud\c1\data\TableColPrioritized {
        function __construct($minWidthPixels, $allowedType, $priority = 1, $floatingRow = 'firstRow') {
            if (!is_int($minWidthPixels)) throw new \Exception('$minWidthPixels must be of type integer');
            if (!in_array($allowedType, array('text','caption','appImage','remoteImage','doubleCaption','imageMultiCaption','any'))) throw new \Exception('value given in $allowedType not found in enumeration');
            if (!in_array($priority, array('Visible','OptionalHigh','OptionalMediumn','OptionalLow'))) throw new \Exception('value given in $priority not found in enumeration');
            if (!in_array($floatingRow, array('noDisplay','firstRow','secondRow','span'))) throw new \Exception('value given in $floatingRow not found in enumeration');
            parent::__construct($priority, 'minWidth', $minWidthPixels, $allowedType, $floatingRow);
        }

    }

    class TableConfiguration extends \_ContentAdapter {
        function __construct($type, $columnConfig) {
            if (!is_string($type)) throw new \Exception('$type must be of type string');
            if (!is_array($columnConfig)) throw new \Exception('$columnConfig must be of type array');
            $this->rowConfig = array();
            $this->columnConfig = $columnConfig;
            $this->type = $type;
        }

        function setRowConfig($rowConfig) {
            if (!is_array($rowConfig)) throw new \Exception('$rowConfig must be of type array');
            foreach($rowConfig as $i) {
                if (!($i instanceof \com\bmw\developer\cloud\c1\data\TableRowConfig)) throw new \Exception('$i must be of type \com\bmw\developer\cloud\c1\data\TableRowConfig');
            }
            $this->rowConfig = $rowConfig;
            return $this;
        }

    }

    class TableConfigRelWidths extends \com\bmw\developer\cloud\c1\data\TableConfiguration {
        function __construct($configs) {
            if (!is_array($configs)) throw new \Exception('$configs must be of type array');
            foreach($configs as $i) {
                if (!($i instanceof \com\bmw\developer\cloud\c1\data\TableColRelWidth)) throw new \Exception('$i must be of type \com\bmw\developer\cloud\c1\data\TableColRelWidth');
            }
            parent::__construct('relWidths', $configs);
        }

    }

    class TableConfigVarWidths extends \com\bmw\developer\cloud\c1\data\TableConfiguration {
        function __construct($configs) {
            if (!is_array($configs)) throw new \Exception('$configs must be of type array');
            foreach($configs as $i) {
                if (!($i instanceof \com\bmw\developer\cloud\c1\data\TableColPrioritized)) throw new \Exception('$i must be of type \com\bmw\developer\cloud\c1\data\TableColPrioritized');
            }
            parent::__construct('varWidths', $configs);
        }

    }

    class TableRowConfig extends \_ContentAdapter {
        function __construct($allowedType) {
            if (!in_array($allowedType, array('text','caption','appImage','remoteImage','doubleCaption','imageMultiCaption','any'))) throw new \Exception('value given in $allowedType not found in enumeration');
            $this->allowedType = $allowedType;
        }

    }

    class TextConfig extends \_ContentAdapter {
        function __construct($smallScreenImageAlignment) {
            if (!in_array($smallScreenImageAlignment, array('TOP','BOTTOM','HIDE'))) throw new \Exception('value given in $smallScreenImageAlignment not found in enumeration');
            $this->smallScreenImageAlignment = null;
        }

    }

}

namespace com\bmw\developer\cloud\c1\data\component {
//     class  extends \_ContentAdapter {
//         function __construct() {
//             $this->$SwitchMap$com$bmw$developer$cloud$c1$data$component$Date$Format$Mode = null;
//         }

//     }

//     class  extends \_ContentAdapter {
//         function __construct() {
//         }

//     }

//     class  extends \_ContentAdapter {
//         function __construct() {
//         }

//     }

//     class  extends \_ContentAdapter {
//         function __construct() {
//         }

//     }

//     class  extends \_ContentAdapter {
//         function __construct() {
//             $this->$SwitchMap$com$bmw$developer$cloud$c1$data$component$Instant$Relation = null;
//         }

//     }

//     class  extends \_ContentAdapter {
//         function __construct() {
//             $this->$SwitchMap$com$bmw$developer$cloud$c1$data$component$Time$Mode = null;
//         }

//     }

    class Address extends \_ContentAdapter {
        function __construct($country = null, $state = null, $city = null, $zip = null, $street = null) {
            if ($country != null && !is_string($country)) throw new \Exception('$country must be of type string');
            if ($state != null && !is_string($state)) throw new \Exception('$state must be of type string');
            if ($city != null && !is_string($city)) throw new \Exception('$city must be of type string');
            if ($zip != null && !is_string($zip)) throw new \Exception('$zip must be of type string');
            if ($street != null && !is_string($street)) throw new \Exception('$street must be of type string');
            $this->zip = $zip;
            $this->street = $street;
            $this->state = $state;
            $this->city = $city;
            $this->country = $country;
        }

        function setState($state) {
            if (!is_string($state)) throw new \Exception('$state must be of type string');
            $this->state = $state;
            return $this;
        }

        function setStreet($street) {
            if (!is_string($street)) throw new \Exception('$street must be of type string');
            $this->street = $street;
            return $this;
        }

        function setCity($city) {
            if (!is_string($city)) throw new \Exception('$city must be of type string');
            $this->city = $city;
            return $this;
        }

        function setZip($zip) {
            if (!is_string($zip)) throw new \Exception('$zip must be of type string');
            $this->zip = $zip;
            return $this;
        }

        function setCountry($country) {
            if (!is_string($country)) throw new \Exception('$country must be of type string');
            $this->country = $country;
            return $this;
        }

    }

    class Image extends \_ContentAdapter {
        function __construct($type) {
            if (!is_string($type)) throw new \Exception('$type must be of type string');
            $this->width = null;
            $this->type = $type;
        }

        function setWidth($width) {
            if (!is_int($width)) throw new \Exception('$width must be of type integer');
            $this->width = $width;
            return $this;
        }

    }

    class NonRemoteImage extends \com\bmw\developer\cloud\c1\data\component\Image {
        function __construct($type) {
            if (!is_string($type)) throw new \Exception('$type must be of type string');
            parent::__construct($type);
        }

    }

    class AppImage extends \com\bmw\developer\cloud\c1\data\component\NonRemoteImage {
        function __construct($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            parent::__construct('appImage');
            $this->name = $name;
        }

        function _getCompositeItemType() {
            return 'appImage';
        }

    }

    class Link extends \com\bmw\developer\cloud\c1\data\InternalLink {
        function __construct($screen) {
            if (!is_string($screen)) throw new \Exception('$screen must be of type string');
            parent::__construct($screen);
        }

        function setCaption($captionText) {
            if (!is_string($captionText)) throw new \Exception('$captionText must be of type string');
            $this->captionText = $captionText;
            return $this;
        }

        function setCaptionOnFocus($captionTextOnFocus) {
            if (!is_string($captionTextOnFocus)) throw new \Exception('$captionTextOnFocus must be of type string');
            $this->captionTextOnFocus = $captionTextOnFocus;
            return $this;
        }

        function setIcon($icon) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            $this->icon = $icon;
            return $this;
        }

        function setDoubleCaption($caption) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\DoubleCaption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\DoubleCaption');
            $this->setVariableContent($caption);
            return $this;
        }

        function setMultiCaption($multiCaption) {
            if (!($multiCaption instanceof \com\bmw\developer\cloud\c1\data\component\MultiCaption)) throw new \Exception('$multiCaption must be of type \com\bmw\developer\cloud\c1\data\component\MultiCaption');
            $this->setVariableContent($multiCaption);
            return $this;
        }

    }

    class BackLink extends \com\bmw\developer\cloud\c1\data\component\Link {
        function __construct() {
            parent::__construct('__back__');
        }

        function _getCompositeListItemType() {
            return 'backLink';
        }

    }

    class Caption extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($captionText = null, $icon = null) {
            if ($captionText != null && !is_string($captionText)) throw new \Exception('$captionText must be of type string');
            if ($icon != null && !($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            $this->iconPlaceholder = null;
            $this->icon = $icon;
            $this->tts = null;
            $this->captionText = $captionText;
            $this->textAlign = null;
            $this->colored = null;
        }

        function _getCompositeItemType() {
            return 'caption';
        }

        function _getCompositeListItemType() {
            return 'caption';
        }

        function setIcon($icon) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            $this->icon = $icon;
            return $this;
        }

        function setCaptionText($captionText) {
            if (!is_string($captionText)) throw new \Exception('$captionText must be of type string');
            $this->captionText = $captionText;
            return $this;
        }

        function setTextAlignment($textAlign) {
            if (!in_array($textAlign, array('left','center','right','justify'))) throw new \Exception('value given in $textAlign not found in enumeration');
            $this->textAlign = $textAlign;
            return $this;
        }

        function setIconPlaceholder($iconPlaceholder) {
            if (!is_bool($iconPlaceholder)) throw new \Exception('$iconPlaceholder must be of type boolean');
            $this->iconPlaceholder = $iconPlaceholder;
            return $this;
        }

        function setColored() {
            $this->colored = true;
            return $this;
        }

        function setTTS($tts) {
            if (!is_bool($tts)) throw new \Exception('$tts must be of type boolean');
            $this->tts = $tts;
            return $this;
        }

    }

    class CaptionLeftRight extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($captionLeft, $captionRight) {
            if (!($captionLeft instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$captionLeft must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!($captionRight instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$captionRight must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            parent::__construct();
            $this->captionLeft = $captionLeft;
            $this->captionRight = $captionRight;
        }

        function _getCompositeListItemType() {
            return 'captionLeftRight';
        }

    }

    class Checkbox extends \com\bmw\developer\cloud\c1\data\AbstractCheckbox {
        function __construct($caption, $name, $value) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            parent::__construct($caption, $value);
            $this->name = null;
        }

    }

    class CheckboxGroup extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            parent::__construct();
            $this->name = $name;
            $this->checkboxes = array();
        }

        function _getCompositeListItemType() {
            return 'checkboxGroup';
        }

        function addCheckbox($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\GroupedCheckbox)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\GroupedCheckbox');
            $this->checkboxes[] = $item;
            return $this;
        }

    }

    class CompositeList extends \_ContentAdapter {
        function __construct() {
            $this->componentList = array();
        }

        function addItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->componentList[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

    }

    class CollapsableGroup extends \com\bmw\developer\cloud\c1\data\component\CompositeList {
        function __construct($caption) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            parent::__construct();
            $this->caption = $caption;
        }

        function _getCompositeListItemType() {
            return 'collapsableGroup';
        }

    }

    class PoiEntry extends \_ContentAdapter {
        function __construct($name, $coordinate) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!($coordinate instanceof \com\bmw\developer\cloud\c1\data\component\Coordinates)) throw new \Exception('$coordinate must be of type \com\bmw\developer\cloud\c1\data\component\Coordinates');
            $this->distance = null;
            $this->coordinate = $coordinate;
            $this->name = $name;
            $this->bearing = null;
            $this->returnParams = array('_DICTMAINTAINER' => true);
        }

        function setName($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            $this->name = $name;
            return $this;
        }

        function setDistance($distance) {
            if (!is_string($distance)) throw new \Exception('$distance must be of type string');
            $this->distance = $distance;
            return $this;
        }

        function setBearing($bearing) {
            if (!is_float($bearing)) throw new \Exception('$bearing must be of type float');
            $this->bearing = $bearing;
            return $this;
        }

        function addReturnParameter($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->returnParams[$name] = $value;
            return $this;
        }

    }

    class ContactInformation extends \com\bmw\developer\cloud\c1\data\component\PoiEntry {
        function __construct($name, $coordinates) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!($coordinates instanceof \com\bmw\developer\cloud\c1\data\component\Coordinates)) throw new \Exception('$coordinates must be of type \com\bmw\developer\cloud\c1\data\component\Coordinates');
            parent::__construct($name, $coordinates);
            $this->phone = null;
            $this->email = null;
            $this->address = null;
            $this->web = null;
        }

        function setUrl($web) {
            if (!is_string($web)) throw new \Exception('$web must be of type string');
            $this->web = $web;
            return $this;
        }

        function setPhone($phone) {
            if (!is_string($phone)) throw new \Exception('$phone must be of type string');
            $this->phone = $phone;
            return $this;
        }

        function setAddress($address) {
            if (!($address instanceof \com\bmw\developer\cloud\c1\data\component\Address)) throw new \Exception('$address must be of type \com\bmw\developer\cloud\c1\data\component\Address');
            $this->address = $address;
            return $this;
        }

        function setEmail($email) {
            if (!is_string($email)) throw new \Exception('$email must be of type string');
            $this->email = $email;
            return $this;
        }

    }

    class ContinuousText extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            parent::__construct();
            $this->tts = null;
            $this->text = $text;
        }

        function _getCompositeItemType() {
            return 'continuousText';
        }

        function _getCompositeListItemType() {
            return 'continuousText';
        }

        function setTTS($tts) {
            if (!is_bool($tts)) throw new \Exception('$tts must be of type boolean');
            $this->tts = $tts;
            return $this;
        }

    }

    class Coordinates extends \_ContentAdapter {
        function __construct($latitude, $longitude) {
            if (!is_float($latitude)) throw new \Exception('$latitude must be of type float');
            if (!is_float($longitude)) throw new \Exception('$longitude must be of type float');
            $this->longitude = $longitude;
            $this->latitude = $latitude;
        }

        function setLatitude($latitude) {
            if (!is_float($latitude)) throw new \Exception('$latitude must be of type float');
            $this->latitude = $latitude;
            return $this;
        }

        function setLongitude($longitude) {
            if (!is_float($longitude)) throw new \Exception('$longitude must be of type float');
            $this->longitude = $longitude;
            return $this;
        }

    }

    class Date extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($format) {
            if (!in_array($format, array('day:month:year','day:month:year','day:month:year','month:day:year','year:month:day','year:month:day','year:month:day'))) throw new \Exception('value given in $format not found in enumeration');
            $this->text = null;
            $this->modifiable = null;
            $this->timemax = null;
            $this->name = null;
            $this->SoyType = null;
            $this->timemin = null;
            $this->left = null;
            $this->format = $format;
            $this->right = null;
            $this->middle = null;
            $this->separator = null;
        }

        function _getCompositeListItemType() {
            return 'dateinput';
        }

        function setName($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            $this->name = $name;
            return $this;
        }

        function setText($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->text = $text;
            return $this;
        }

        function setModifiable($modifiable) {
            if (!is_bool($modifiable)) throw new \Exception('$modifiable must be of type boolean');
            $this->modifiable = $modifiable;
            return $this;
        }

    }

    class DoubleCaption extends \_ContentAdapter {
        function __construct($caption1, $caption2) {
            if (!($caption1 instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption1 must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!($caption2 instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption2 must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            $this->caption2 = $caption2;
            $this->caption1 = $caption1;
        }

        function _getCompositeItemType() {
            return 'doubleCaption';
        }

    }

    class EmailLink extends \com\bmw\developer\cloud\c1\data\AbstractLink {
        function __construct($email) {
            if (!is_string($email)) throw new \Exception('$email must be of type string');
            parent::__construct();
            $this->email = $email;
        }

        function _getCompositeListItemType() {
            return 'emailLink';
        }

    }

    class Form extends \com\bmw\developer\cloud\c1\data\component\CompositeList {
        function __construct($linkTarget) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            parent::__construct();
            $this->linkTarget = $linkTarget;
        }

        function addReturnParam($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->linkTarget->addReturnParam($name, $value);
            return $this;
        }

    }

    class GroupedCheckbox extends \com\bmw\developer\cloud\c1\data\AbstractCheckbox {
        function __construct($caption, $value) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            parent::__construct($caption, $value);
        }

    }

    class HiddenInput extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            parent::__construct();
            $this->name = $name;
            $this->value = $value;
        }

        function _getCompositeListItemType() {
            return 'hiddenInput';
        }

    }

    class Icon extends \com\bmw\developer\cloud\c1\data\AbstractIcon {
        function __construct($image) {
            if (!($image instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$image must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            parent::__construct($image, null);
        }

    }

    class IconCss extends \com\bmw\developer\cloud\c1\data\AbstractIcon {
        function __construct($cssClass) {
            if (!is_string($cssClass)) throw new \Exception('$cssClass must be of type string');
            parent::__construct(null, $cssClass);
        }

    }

    class ImageMultiCaption extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($image, $multiCaption) {
            if (!($image instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$image must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            if (!($multiCaption instanceof \com\bmw\developer\cloud\c1\data\component\MultiCaption)) throw new \Exception('$multiCaption must be of type \com\bmw\developer\cloud\c1\data\component\MultiCaption');
            parent::__construct();
            $this->imageOnRight = null;
            $this->imageSpanLines = null;
            $this->multiCaption = $multiCaption;
            $this->image = $image;
        }

        function _getCompositeItemType() {
            return 'imageMultiCaption';
        }

        function _getCompositeListItemType() {
            return 'imageMultiCaption';
        }

        function setImageSpanLines($imageSpanLines) {
            if (!is_int($imageSpanLines)) throw new \Exception('$imageSpanLines must be of type integer');
            $this->imageSpanLines = $imageSpanLines;
            return $this;
        }

        function setImageOnRight() {
            $this->imageOnRight = true;
            return $this;
        }

    }

    class ImageThreeLines extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($userImage, $firstLine, $secondLine, $thirdLine) {
            if (!($userImage instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$userImage must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            if (!($firstLine instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$firstLine must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!($secondLine instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$secondLine must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!($thirdLine instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$thirdLine must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            parent::__construct();
            $this->userImage = $userImage;
            $this->thirdLine = $thirdLine;
            $this->firstLine = $firstLine;
            $this->secondLine = $secondLine;
        }

        function _getCompositeListItemType() {
            return 'imageThreeLines';
        }

    }

    class Instant extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($date, $time) {
            if (!($date instanceof \com\bmw\developer\cloud\c1\data\component\Date)) throw new \Exception('$date must be of type \com\bmw\developer\cloud\c1\data\component\Date');
            if (!($time instanceof \com\bmw\developer\cloud\c1\data\component\Time)) throw new \Exception('$time must be of type \com\bmw\developer\cloud\c1\data\component\Time');
            $this->time = $time;
            $this->SoyType = null;
            $this->constraint = null;
            $this->date = $date;
            $this->identifier = null;
        }

        function _getCompositeListItemType() {
            return 'timeinput';
        }

    }

    class LinkColumns extends \com\bmw\developer\cloud\c1\data\InternalLink {
        function __construct($screen) {
            if (!is_string($screen)) throw new \Exception('$screen must be of type string');
            parent::__construct($screen);
            $this->table = new \com\bmw\developer\cloud\c1\data\component\Table();
            $this->row = new \com\bmw\developer\cloud\c1\data\component\TableRow();
            $this->table->setWidth('100%');
            $this->table->addRow($this->row);
            $this->setVariableContent($this->table);
        }

        function setWidth($width) {
            if (!is_string($width)) throw new \Exception('$width must be of type string');
            $this->table->setWidth($width);
            return $this;
        }

        function addCell($cell) {
            if (!($cell instanceof \com\bmw\developer\cloud\c1\data\component\TableCell)) throw new \Exception('$cell must be of type \com\bmw\developer\cloud\c1\data\component\TableCell');
            $this->row->addCell($cell);
            return $this;
        }

        function cleanJson() {
            parent::cleanJson();
            unset($this->table);
            unset($this->row);
            return $this;
        }

    }

    class LinkTarget extends \_ContentAdapter {
        function __construct($screen) {
            if (!is_string($screen)) throw new \Exception('$screen must be of type string');
            $this->screen = $screen;
            $this->returnParams = array('_DICTMAINTAINER' => true);
        }

        function addReturnParam($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->returnParams[$name] = $value;
            return $this;
        }

    }

    class MessageLink extends \com\bmw\developer\cloud\c1\data\AbstractLink {
        function __construct($linkTarget, $sender, $subject, $dateTime, $isRead) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if (!is_string($sender)) throw new \Exception('$sender must be of type string');
            if (!is_string($subject)) throw new \Exception('$subject must be of type string');
            if (!is_string($dateTime)) throw new \Exception('$dateTime must be of type string');
            if (!is_bool($isRead)) throw new \Exception('$isRead must be of type boolean');
            parent::__construct();
            $this->sender = $sender;
            $this->dateTime = $dateTime;
            $this->subject = $subject;
            $this->image = null;
            $this->linkTarget = $linkTarget;
            $this->isRead = $isRead;
        }

        function _getCompositeListItemType() {
            return 'messageLink';
        }

        function setImage($image) {
            if (!($image instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$image must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            $this->image = $image;
            return $this;
        }

    }

    class MultiCaption extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($captions) {
            if (!is_array($captions)) throw new \Exception('$captions must be of type array');
            parent::__construct();
            $this->captions = $captions;
        }

        function _getCompositeItemType() {
            return 'multiCaption';
        }

        function _getCompositeListItemType() {
            return 'multiCaption';
        }

        function addCaption($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            $this->captions[] = $item;
            return $this;
        }

    }

    class MultiCaptionLeftRight extends \_ContentAdapter {
        function __construct($captionLeftRights) {
            if (!is_array($captionLeftRights)) throw new \Exception('$captionLeftRights must be of type array');
            $this->captionLeftRights = $captionLeftRights;
        }

        function addCaptionLeftRight($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\CaptionLeftRight)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\CaptionLeftRight');
            $this->captionLeftRights[] = $item;
            return $this;
        }

    }

    class NavigationLink extends \com\bmw\developer\cloud\c1\data\AbstractLink {
        function __construct($coordinate) {
            if (!($coordinate instanceof \com\bmw\developer\cloud\c1\data\component\Coordinates)) throw new \Exception('$coordinate must be of type \com\bmw\developer\cloud\c1\data\component\Coordinates');
            parent::__construct();
            $this->twoLines = null;
            $this->address = null;
            $this->coordinate = $coordinate;
            $this->name = null;
        }

        function _getCompositeListItemType() {
            return 'navigationLink';
        }

        function setName($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            $this->name = $name;
            return $this;
        }

        function setAddress($address) {
            if (!($address instanceof \com\bmw\developer\cloud\c1\data\component\Address)) throw new \Exception('$address must be of type \com\bmw\developer\cloud\c1\data\component\Address');
            $this->address = $address;
            return $this;
        }

        function setTwoLines($twoLines) {
            if (!is_bool($twoLines)) throw new \Exception('$twoLines must be of type boolean');
            $this->twoLines = $twoLines;
            return $this;
        }

    }

    class Paragraph extends \_ContentAdapter {
        function __construct($text = null) {
            if ($text != null && !is_string($text)) throw new \Exception('$text must be of type string');
            $this->tts = null;
            $this->text = $text;
            $this->imageCaption = null;
            $this->image = null;
            $this->html = null;
            $this->textLines = array();
        }

        function setText($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->text = $text;
            return $this;
        }

        function setTTS($tts) {
            if (!is_bool($tts)) throw new \Exception('$tts must be of type boolean');
            $this->tts = $tts;
            return $this;
        }

        function setImage($image) {
            if (!($image instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$image must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            $this->image = $image;
            return $this;
        }

        function setHTML($html) {
            if (!is_string($html)) throw new \Exception('$html must be of type string');
            $this->html = $html;
            return $this;
        }

        function setImageCaption($imageCaption) {
            if (!is_string($imageCaption)) throw new \Exception('$imageCaption must be of type string');
            $this->imageCaption = $imageCaption;
            return $this;
        }

        function addTextLine($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\Text)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\Text');
            $this->textLines[] = $item;
            return $this;
        }

    }

    class PasswordInput extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($name, $value = null) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if ($value != null && !is_string($value)) throw new \Exception('$value must be of type string');
            parent::__construct();
            $this->icon = null;
            $this->captionText = null;
            $this->name = $name;
            $this->value = $value;
        }

        function _getCompositeListItemType() {
            return 'passwordInput';
        }

        function setInitialValue($value) {
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->value = $value;
            return $this;
        }

        function setIcon($icon) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            $this->icon = $icon;
            return $this;
        }

        function setCaptionText($captionText) {
            if (!is_string($captionText)) throw new \Exception('$captionText must be of type string');
            $this->captionText = $captionText;
            return $this;
        }

    }

    class PhoneLink extends \com\bmw\developer\cloud\c1\data\AbstractLink {
        function __construct($phone) {
            if (!is_string($phone)) throw new \Exception('$phone must be of type string');
            parent::__construct();
            $this->phone = $phone;
        }

        function _getCompositeListItemType() {
            return 'phoneLink';
        }

    }

    class PoiEntryCaption extends \_ContentAdapter {
        function __construct($multiCaption, $coordinate) {
            if (!($multiCaption instanceof \com\bmw\developer\cloud\c1\data\component\MultiCaptionLeftRight)) throw new \Exception('$multiCaption must be of type \com\bmw\developer\cloud\c1\data\component\MultiCaptionLeftRight');
            if (!($coordinate instanceof \com\bmw\developer\cloud\c1\data\component\Coordinates)) throw new \Exception('$coordinate must be of type \com\bmw\developer\cloud\c1\data\component\Coordinates');
            $this->coordinate = $coordinate;
            $this->multiCaption = $multiCaption;
            $this->returnParams = array('_DICTMAINTAINER' => true);
        }

        function addReturnParameter($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->returnParams[$name] = $value;
            return $this;
        }

    }

    class PoiEntryListItem extends \_ContentAdapter {
        function __construct($name, $latitude, $longitude) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_float($latitude)) throw new \Exception('$latitude must be of type float');
            if (!is_float($longitude)) throw new \Exception('$longitude must be of type float');
            $this->iconPlaceholder = false;
            $this->icon = null;
            $this->distance = null;
            $this->displayInsideList = true;
            $this->coordinate = new \com\bmw\developer\cloud\c1\data\component\Coordinates($latitude, $longitude);
            $this->name = $name;
            $this->bearing = null;
            $this->returnParams = array('_DICTMAINTAINER' => true);
            $this->displayInsideMap = true;
        }

        function _getCompositeListItemType() {
            return 'poiEntryListItem';
        }

        function setIcon($icon) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\component\Icon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\component\Icon');
            $this->icon = $icon;
            return $this;
        }

        function setIconPlaceholder() {
            $this->iconPlaceholder = true;
            return $this;
        }

        function disableDisplayInsideMap() {
            $this->displayInsideMap = false;
            return $this;
        }

        function disableDisplayInsideList() {
            $this->displayInsideList = false;
            return $this;
        }

    }

    class RadioButton extends \_ContentAdapter {
        function __construct($caption, $value) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->selected = null;
            $this->value = $value;
            $this->caption = $caption;
        }

        function _getCompositeListItemType() {
            return 'radioButton';
        }

        function setSelected($selected) {
            if (!is_bool($selected)) throw new \Exception('$selected must be of type boolean');
            $this->selected = $selected;
            return $this;
        }

    }

    class RadioButtonGroup extends \_ContentAdapter {
        function __construct($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            $this->name = $name;
            $this->radioButtons = array();
        }

        function _getCompositeListItemType() {
            return 'radioButtonGroup';
        }

        function addRadioButton($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\RadioButton)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\RadioButton');
            $this->radioButtons[] = $item;
            return $this;
        }

    }

    class RemoteImage extends \com\bmw\developer\cloud\c1\data\component\Image {
        function __construct($url) {
            if (!is_string($url)) throw new \Exception('$url must be of type string');
            parent::__construct('remoteImage');
            $this->maxWidth = null;
            $this->maxHeight = null;
            $this->url = $url;
        }

        function _getCompositeItemType() {
            return 'remoteImage';
        }

        function setMaxWidth($maxWidth) {
            if (!is_int($maxWidth)) throw new \Exception('$maxWidth must be of type integer');
            $this->maxWidth = $maxWidth;
            return $this;
        }

        function setMaxHeight($maxHeight) {
            if (!is_int($maxHeight)) throw new \Exception('$maxHeight must be of type integer');
            $this->maxHeight = $maxHeight;
            return $this;
        }

    }

    class SDKImage extends \com\bmw\developer\cloud\c1\data\component\NonRemoteImage {
        function __construct($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            parent::__construct('sdkImage');
            $this->name = $name;
        }

        function _getCompositeItemType() {
            return 'sdkImage';
        }

    }

    class SubmitLink extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($caption) {
            if (!($caption instanceof \com\bmw\developer\cloud\c1\data\component\Caption)) throw new \Exception('$caption must be of type \com\bmw\developer\cloud\c1\data\component\Caption');
            parent::__construct();
            $this->caption = $caption;
            $this->linkTarget = null;
        }

        function _getCompositeListItemType() {
            return 'submitButton';
        }

        function target($linkTarget) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            $this->linkTarget = $linkTarget;
            return $this;
        }

    }

    class Table extends \_ContentAdapter {
        function __construct() {
            $this->width = null;
            $this->rows = array();
        }

        function _getCompositeItemType() {
            return 'table';
        }

        function _getCompositeListItemType() {
            return 'table';
        }

        function setWidth($width) {
            if (!is_string($width)) throw new \Exception('$width must be of type string');
            $this->width = $width;
            return $this;
        }

        function addRow($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\TableRow)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\TableRow');
            $this->rows[] = $item;
            return $this;
        }

    }

    class TableCell extends \_ContentAdapter {
        function __construct($caption = null) {
            if ($caption != null && !is_string($caption)) throw new \Exception('$caption must be of type string');
            $this->compositeItem = null;
            $this->limited = null;
            $this->ttsText = null;
            $this->tts = null;
            $this->colSpan = 1;
            $this->rowSpan = 1;
            $this->caption = $caption;
        }

        function setCaption($caption) {
            if (!is_string($caption)) throw new \Exception('$caption must be of type string');
            $this->caption = $caption;
            return $this;
        }

        function setVariableContent($compositeItem) {
            if (!method_exists($compositeItem, '_getCompositeItemType')) throw new \Exception('$compositeItem must be a composite item type');
            $this->compositeItem = array('type' => $compositeItem->_getCompositeItemType(), 'data' => $compositeItem);
            return $this;
        }

        function setTTS($tts) {
            if (!is_bool($tts)) throw new \Exception('$tts must be of type boolean');
            $this->tts = $tts;
            return $this;
        }

        function setLimited($limited) {
            if (!is_bool($limited)) throw new \Exception('$limited must be of type boolean');
            $this->limited = $limited;
            return $this;
        }

        function setRowSpan($rowSpan) {
            if (!is_int($rowSpan)) throw new \Exception('$rowSpan must be of type integer');
            $this->rowSpan = $rowSpan;
            return $this;
        }

        function setColSpan($colSpan) {
            if (!is_int($colSpan)) throw new \Exception('$colSpan must be of type integer');
            $this->colSpan = $colSpan;
            return $this;
        }

        function setTTSText($ttsText) {
            if (!is_string($ttsText)) throw new \Exception('$ttsText must be of type string');
            $this->tts = true;
            $this->ttsText = $ttsText;
            return $this;
        }

    }

    class TableRow extends \_ContentAdapter {
        function __construct() {
            $this->cells = array();
            $this->lineHeight = null;
            $this->isHeader = null;
        }

        function setHeader($isHeader) {
            if (!is_bool($isHeader)) throw new \Exception('$isHeader must be of type boolean');
            $this->isHeader = $isHeader;
            return $this;
        }

        function setLineHeight($lineHeight) {
            if (!is_string($lineHeight)) throw new \Exception('$lineHeight must be of type string');
            $this->lineHeight = $lineHeight;
            return $this;
        }

        function addCell($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\TableCell)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\TableCell');
            $this->cells[] = $item;
            return $this;
        }

    }

    class Text extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->isColored = null;
            $this->tts = null;
            $this->text = $text;
            $this->textAlignment = null;
        }

        function _getCompositeItemType() {
            return 'text';
        }

        function _getCompositeListItemType() {
            return 'text';
        }

        function setText($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->text = $text;
            return $this;
        }

        function setTextAlignment($textAlignment) {
            if (!in_array($textAlignment, array('left','center','right','justify'))) throw new \Exception('value given in $textAlignment not found in enumeration');
            $this->textAlignment = $textAlignment;
            return $this;
        }

        function setColored() {
            $this->colored = true;
            return $this;
        }

        function setTTS($tts) {
            if (!is_bool($tts)) throw new \Exception('$tts must be of type boolean');
            $this->tts = $tts;
            return $this;
        }

    }

    class TextInput extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($name, $value = null) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if ($value != null && !is_string($value)) throw new \Exception('$value must be of type string');
            parent::__construct();
            $this->icon = null;
            $this->captionText = null;
            $this->placeholder = null;
            $this->name = $name;
            $this->value = $value;
        }

        function _getCompositeListItemType() {
            return 'textInput';
        }

        function setInitialValue($value) {
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->value = $value;
            return $this;
        }

        function setIcon($icon) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            $this->icon = $icon;
            return $this;
        }

        function setCaptionText($captionText) {
            if (!is_string($captionText)) throw new \Exception('$captionText must be of type string');
            $this->captionText = $captionText;
            return $this;
        }

        function setPlaceholderText($placeholder) {
            if (!is_string($placeholder)) throw new \Exception('$placeholder must be of type string');
            $this->placeholder = $placeholder;
            return $this;
        }

    }

    class Time extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($mode) {
            if (!in_array($mode, array('Full','Half'))) throw new \Exception('value given in $mode not found in enumeration');
            $this->minuteoffset = null;
            $this->text = null;
            $this->modifiable = null;
            $this->hour = null;
            $this->houroffset = null;
            $this->suffix = null;
            $this->mode = $mode;
            $this->minute = null;
            $this->timemax = null;
            $this->SoyType = null;
            $this->name = null;
            $this->minutestep = null;
            $this->timemin = null;
            $this->hourstep = null;
        }

        function _getCompositeListItemType() {
            return 'timeinput';
        }

        function setName($name) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            $this->name = $name;
            return $this;
        }

        function setText($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->text = $text;
            return $this;
        }

        function setModifiable($modifiable) {
            if (!is_bool($modifiable)) throw new \Exception('$modifiable must be of type boolean');
            $this->modifiable = $modifiable;
            return $this;
        }

    }

    class ToolbarButtonPlaceholder extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct() {
            parent::__construct('toolbarButtonPlaceholder', null);
        }

    }

    class ToolbarIconButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($icon, $linkTarget, $toolTip) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarIconButton', $toolTip);
            $this->icon = $icon;
            $this->linkTarget = $linkTarget;
        }

    }

    class ToolbarMailButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($linkTarget, $toolTip) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarMailButton', $toolTip);
            $this->linkTarget = $linkTarget;
            $this->TYPE = null;
        }

    }

    class ToolbarMapButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($linkTarget, $toolTip) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarMapButton', $toolTip);
            $this->linkTarget = $linkTarget;
            $this->TYPE = null;
        }

    }

    class ToolbarNavButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($poiEntry, $toolTip) {
            if (!($poiEntry instanceof \com\bmw\developer\cloud\c1\data\component\PoiEntry)) throw new \Exception('$poiEntry must be of type \com\bmw\developer\cloud\c1\data\component\PoiEntry');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarNavButton', $toolTip);
            $this->poiEntry = $poiEntry;
            $this->TYPE = null;
        }

    }

    class ToolbarPhoneButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($phoneNumber, $toolTip) {
            if (!is_string($phoneNumber)) throw new \Exception('$phoneNumber must be of type string');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarPhoneButton', $toolTip);
            $this->phoneNumber = $phoneNumber;
            $this->TYPE = null;
        }

    }

    class ToolbarTTSButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($toolTip) {
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarTTSButton', $toolTip);
        }

    }

    class ToolbarTextButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($captionText, $linkTarget, $toolTip) {
            if (!is_string($captionText)) throw new \Exception('$captionText must be of type string');
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarTextButton', $toolTip);
            $this->captionText = $captionText;
            $this->linkTarget = $linkTarget;
        }

    }

    class ToolbarWebButton extends \com\bmw\developer\cloud\c1\data\AbstractToolbarButton {
        function __construct($url, $toolTip) {
            if (!is_string($url)) throw new \Exception('$url must be of type string');
            if (!is_string($toolTip)) throw new \Exception('$toolTip must be of type string');
            parent::__construct('toolbarWebButton', $toolTip);
            $this->url = $url;
            $this->TYPE = null;
        }

    }

    class TwoLink extends \com\bmw\developer\cloud\c1\data\AbstractListItem {
        function __construct($name, $text) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->icon = null;
            $this->text = $text;
            $this->name = $name;
            $this->target = null;
            $this->date = null;
        }

        function _getCompositeListItemType() {
            return 'twoLink';
        }

        function setDate($date) {
            if (!is_string($date)) throw new \Exception('$date must be of type string');
            $this->date = $date;
            return $this;
        }

        function setIcon($icon) {
            if (!($icon instanceof \com\bmw\developer\cloud\c1\data\AbstractIcon)) throw new \Exception('$icon must be of type \com\bmw\developer\cloud\c1\data\AbstractIcon');
            $this->icon = $icon;
            return $this;
        }

        function setTarget($target) {
            if (!($target instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$target must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            $this->target = $target;
            return $this;
        }

        function addReturnParam($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->target->addReturnParam($name, $value);
            return $this;
        }

    }

    class WebLink extends \com\bmw\developer\cloud\c1\data\AbstractLink {
        function __construct($web) {
            if (!is_string($web)) throw new \Exception('$web must be of type string');
            parent::__construct();
            $this->web = $web;
        }

        function _getCompositeListItemType() {
            return 'urlLink';
        }

    }

}

namespace com\bmw\developer\cloud\c1\data\page {
    class BMWPage extends \_ContentAdapter {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            $this->providerlogo = null;
            $this->noHistory = null;
            $this->heading = $heading;
            $this->version = 'c1';
        }

        function setHeading($heading) {
            if (!is_string($heading)) throw new \Exception('$heading must be of type string');
            $this->heading = $heading;
            return $this;
        }

        function setProviderLogo($providerlogo) {
            if (!($providerlogo instanceof \com\bmw\developer\cloud\c1\data\component\Image)) throw new \Exception('$providerlogo must be of type \com\bmw\developer\cloud\c1\data\component\Image');
            $this->providerlogo = $providerlogo;
            return $this;
        }

        function disableHistory() {
            $this->noHistory = true;
            return $this;
        }

    }

    class GeocoderCapablePage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->useGeocoder = false;
        }

        function setGeocoding($useGeocoder) {
            if (!is_bool($useGeocoder)) throw new \Exception('$useGeocoder must be of type boolean');
            $this->useGeocoder = $useGeocoder;
            return $this;
        }

    }

    class PoiDetailPage extends \com\bmw\developer\cloud\c1\data\page\GeocoderCapablePage {
        function __construct($contactInformation, $heading = null) {
            if (!($contactInformation instanceof \com\bmw\developer\cloud\c1\data\component\ContactInformation)) throw new \Exception('$contactInformation must be of type \com\bmw\developer\cloud\c1\data\component\ContactInformation');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->noMap = null;
            $this->text = null;
            $this->contactInformation = $contactInformation;
            $this->zoom = null;
            $this->noNav = null;
            $this->bottomCompositeListItems = array();
            $this->remoteImage = null;
        }

        function setText($text) {
            if (!is_string($text)) throw new \Exception('$text must be of type string');
            $this->text = $text;
            return $this;
        }

        function setImage($remoteImage) {
            if (!($remoteImage instanceof \com\bmw\developer\cloud\c1\data\component\RemoteImage)) throw new \Exception('$remoteImage must be of type \com\bmw\developer\cloud\c1\data\component\RemoteImage');
            $this->remoteImage = $remoteImage;
            return $this;
        }

        function setContactInformation($contactInformation) {
            if (!($contactInformation instanceof \com\bmw\developer\cloud\c1\data\component\ContactInformation)) throw new \Exception('$contactInformation must be of type \com\bmw\developer\cloud\c1\data\component\ContactInformation');
            $this->contactInformation = $contactInformation;
            return $this;
        }

        function setNoMap() {
            $this->noMap = true;
            return $this;
        }

        function setNoNavigation() {
            $this->noNav = true;
            return $this;
        }

        function setZoom($zoom) {
            if (!is_int($zoom)) throw new \Exception('$zoom must be of type integer');
            $this->zoom = $zoom;
            return $this;
        }

        function addBottomItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->bottomCompositeListItems[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

    }

    class AutoNaviPoiDetailPage extends \com\bmw\developer\cloud\c1\data\page\PoiDetailPage {
        function __construct($contactInformation, $heading = null) {
            if (!($contactInformation instanceof \com\bmw\developer\cloud\c1\data\component\ContactInformation)) throw new \Exception('$contactInformation must be of type \com\bmw\developer\cloud\c1\data\component\ContactInformation');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            
            parent::__construct($contactInformation, $heading);
        }

    }
    
    class BaiduMapPoiDetailPage extends \com\bmw\developer\cloud\c1\data\page\PoiDetailPage {
    	function __construct($contactInformation, $heading = null) {
    		if (!($contactInformation instanceof \com\bmw\developer\cloud\c1\data\component\ContactInformation)) throw new \Exception('$contactInformation must be of type \com\bmw\developer\cloud\c1\data\component\ContactInformation');
    		if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
    
    		parent::__construct($contactInformation, $heading);
    	}
    
    }

    class PoiOverviewPage extends \com\bmw\developer\cloud\c1\data\page\GeocoderCapablePage {
        function __construct($linkingScreenId, $heading = null) {
            if (!is_string($linkingScreenId)) throw new \Exception('$linkingScreenId must be of type string');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->topCompositeListItems = array();
            $this->poiEntries = array();
            $this->linkingScreenId = $linkingScreenId;
            $this->bottomCompositeListItems = array();
            $this->useGeocoder = true;
        }

        function addBottomItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->bottomCompositeListItems[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

        function addPoiEntry($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\PoiEntry)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\PoiEntry');
            $this->poiEntries[] = $item;
            return $this;
        }

        function addTopItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->topCompositeListItems[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

    }

    class AutoNaviPoiOverviewPage extends \com\bmw\developer\cloud\c1\data\page\PoiOverviewPage {
        function __construct($linkingScreenId, $heading = null) {
            if (!is_string($linkingScreenId)) throw new \Exception('$linkingScreenId must be of type string');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            
            parent::__construct($linkingScreenId, $heading);
            
        }

    }

    class BaiduMapPoiOverviewPage extends \com\bmw\developer\cloud\c1\data\page\PoiOverviewPage {
        function __construct($linkingScreenId, $heading = null) {
            if (!is_string($linkingScreenId)) throw new \Exception('$linkingScreenId must be of type string');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            
            parent::__construct($linkingScreenId, $heading);
            
            $this->carCoordinate = null;
        }

        function setCarCoordinate($carCoordinate) {
            if (!($carCoordinate instanceof \com\bmw\developer\cloud\c1\data\component\Coordinates)) throw new \Exception('$carCoordinate must be of type \com\bmw\developer\cloud\c1\data\component\Coordinates');
            $this->carCoordinate = $carCoordinate;
            return $this;
        }

    }

    class CompositeItemPage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($item, $heading = null) {
            if (!method_exists($item, '_getCompositeItemType')) throw new \Exception('$item must be a composite item type');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->compositeItem = array('type' => $item->_getCompositeItemType(), 'data' => $item);
        }

    }

    class CompositeListPage extends \com\bmw\developer\cloud\c1\data\page\GeocoderCapablePage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->mapCoordinates = null;
            $this->componentList = array();
        }

        function setMapCoordinates($mapCoordinates) {
            if (!($mapCoordinates instanceof \com\bmw\developer\cloud\c1\data\component\Coordinates)) throw new \Exception('$mapCoordinates must be of type \com\bmw\developer\cloud\c1\data\component\Coordinates');
            $this->mapCoordinates = $mapCoordinates;
            return $this;
        }

        function addItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->componentList[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

    }

    class CompositeListWithToolbarPage extends \com\bmw\developer\cloud\c1\data\page\CompositeListPage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->toolbarItems = array();
            $this->noScrollButtons = false;
        }

        function disableScrollButtons() {
            $this->noScrollButtons = true;
            return $this;
        }

        function addToolbarItem($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\AbstractToolbarButton)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\AbstractToolbarButton');
            $this->toolbarItems[] = $item;
            return $this;
        }

    }

    class EmailFormPage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($linkTarget, $heading, $emailParamName, $label, $submitBtnText, $defaultEmail = null) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if (!is_string($heading)) throw new \Exception('$heading must be of type string');
            if (!is_string($emailParamName)) throw new \Exception('$emailParamName must be of type string');
            if (!is_string($label)) throw new \Exception('$label must be of type string');
            if (!is_string($submitBtnText)) throw new \Exception('$submitBtnText must be of type string');
            if ($defaultEmail != null && !is_string($defaultEmail)) throw new \Exception('$defaultEmail must be of type string');
            parent::__construct($heading);
            $this->form = new \com\bmw\developer\cloud\c1\data\component\Form();
        }

    }

    class ErrorPage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($message = null) {
            if ($message != null && !is_string($message)) throw new \Exception('$message must be of type string');
            parent::__construct('Error');
            $this->message = $message;
            $this->error = 'E2';
        }

        function setServiceCurrentlyUnavailableError() {
            $this->error = 'E0';
            return $this;
        }

        function setErrorOccurredError() {
            $this->error = 'E2';
            return $this;
        }

        function setMissingParameterError() {
            $this->error = 'E10';
            return $this;
        }

        function setInvalidParameterFormatError() {
            $this->error = 'E11';
            return $this;
        }

        function setUnsupportedLanguageError() {
            $this->error = 'E12';
            return $this;
        }

        function setUnsupportedMarketError() {
            $this->error = 'E13';
            return $this;
        }

        function setNoContentAvailableForThisLocationError() {
            $this->error = 'E20';
            return $this;
        }

        function setNoContentAvailableError() {
            $this->error = 'E21';
            return $this;
        }

        function setNoSearchResultsError() {
            $this->error = 'E22';
            return $this;
        }

        function setErrorWhilePreparingContentError() {
            $this->error = 'E23';
            return $this;
        }

    }

    class FormPage extends \com\bmw\developer\cloud\c1\data\page\GeocoderCapablePage {
        function __construct($linkTarget, $heading = null) {
            if (!($linkTarget instanceof \com\bmw\developer\cloud\c1\data\component\LinkTarget)) throw new \Exception('$linkTarget must be of type \com\bmw\developer\cloud\c1\data\component\LinkTarget');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->form = new \com\bmw\developer\cloud\c1\data\component\Form($linkTarget);
        }

        function addItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->form->addItem($item);
            return $this;
        }

    }

    class IparkPage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($heading) {
            if (!is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
        }

    }

    class LinksPage extends \com\bmw\developer\cloud\c1\data\page\GeocoderCapablePage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->links = array();
        }

        function addLink($link) {
            if (!($link instanceof \com\bmw\developer\cloud\c1\data\AbstractLink)) throw new \Exception('$link must be of type \com\bmw\developer\cloud\c1\data\AbstractLink');
            $this->links[] = $link;
            return $this;
        }

    }

    class MultiPanePage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->subpages = array();
        }

        function addSubpage($item) {
            if (!method_exists($item, '_getCompositeItemType')) throw new \Exception('$item must be a composite item type');
            $this->subpages[] = array('type' => $item->_getCompositeItemType(), 'data' => $item);
            return $this;
        }

    }

    class PoiDetailNaviListPage extends \com\bmw\developer\cloud\c1\data\page\CompositeListPage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->displayMap = true;
        }

        function disableDisplayMap() {
            $this->displayMap = false;
            return $this;
        }

    }

    class PoiOverviewParkopediaPage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($linkingScreenId, $heading = null) {
            if (!is_string($linkingScreenId)) throw new \Exception('$linkingScreenId must be of type string');
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->topCompositeListItems = array();
            $this->linkingScreenId = $linkingScreenId;
            $this->bottomCompositeListItems = array();
            $this->poiEntryCaptions = array();
        }

        function addBottomItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->bottomCompositeListItems[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

        function addTopItem($item) {
            if (!method_exists($item, '_getCompositeListItemType')) throw new \Exception('$item must be a composite list item type');
            $this->topCompositeListItems[] = array('type' => $item->_getCompositeListItemType(), 'data' => $item);
            return $this;
        }

        function addPoiEntryCaption($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\PoiEntryCaption)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\PoiEntryCaption');
            $this->poiEntryCaptions[] = $item;
            return $this;
        }

    }

    class TablePage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($heading) {
            if (!is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->table = new \com\bmw\developer\cloud\c1\data\component\Table();
        }

        function addRow($row) {
            if (!($row instanceof \com\bmw\developer\cloud\c1\data\component\TableRow)) throw new \Exception('$row must be of type \com\bmw\developer\cloud\c1\data\component\TableRow');
            $this->table->addRow($row);
            return $this;
        }

    }

    class TextPage extends \com\bmw\developer\cloud\c1\data\page\BMWPage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->title = null;
            $this->paragraphs = array();
        }

        function setTitle($title) {
            if (!is_string($title)) throw new \Exception('$title must be of type string');
            $this->title = $title;
            return $this;
        }

        function addParagraph($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\component\Paragraph)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\component\Paragraph');
            $this->paragraphs[] = $item;
            return $this;
        }

    }

    class TextWithToolbarPage extends \com\bmw\developer\cloud\c1\data\page\TextPage {
        function __construct($heading = null) {
            if ($heading != null && !is_string($heading)) throw new \Exception('$heading must be of type string');
            parent::__construct($heading);
            $this->toolbarItems = array();
            $this->noScrollButtons = null;
        }

        function disableScrollButtons() {
            $this->noScrollButtons = true;
            return $this;
        }

        function addToolbarItem($item) {
            if (!($item instanceof \com\bmw\developer\cloud\c1\data\AbstractToolbarButton)) throw new \Exception('$item must be of type \com\bmw\developer\cloud\c1\data\AbstractToolbarButton');
            $this->toolbarItems[] = $item;
            return $this;
        }

    }

}

namespace com\bmw\developer\cloud\c1\data\manifest {
    class Screen extends \_ContentAdapter {
        function __construct($templateName, $production, $integration = null, $test = null) {
            if (!is_string($templateName)) throw new \Exception('$templateName must be of type string');
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            $this->template = array();
            $this->test = $test;
            $this->integration = $integration;
            $this->configuration = array('_DICTMAINTAINER' => true);
            $this->production = $production;
            $this->template[] = $templateName;
            $this->template[] = 'c1';
        }

    }

    class TableScreen extends \com\bmw\developer\cloud\c1\data\manifest\Screen {
        function __construct($templateName, $production, $integration = null, $test = null) {
            if (!is_string($templateName)) throw new \Exception('$templateName must be of type string');
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct($templateName, $production, $integration, $test);
        }

        function addTableConfiguration($tableConfig) {
            if (!($tableConfig instanceof \com\bmw\developer\cloud\c1\data\TableConfiguration)) throw new \Exception('$tableConfig must be of type \com\bmw\developer\cloud\c1\data\TableConfiguration');
            $this->configuration['tableConfig'] = $tableConfig;
            return $this;
        }

    }

    class AutoNaviPoiDetailPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('autoNaviPoiDetail', $production, $integration, $test);
        }

    }

    class AutoNaviPoiOverviewPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('autoNaviPoiOverview', $production, $integration, $test);
        }

    }

    class BaiduMapPoiDetailPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('baiduMapPoiDetail', $production, $integration, $test);
        }

    }

    class BaiduMapPoiOverviewPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('baiduMapPoiOverview', $production, $integration, $test);
        }

    }

    class CompositeListPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('compositeList', $production, $integration, $test);
        }

    }

    class CompositeListWithToolbarPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('compositeListWithToolbar', $production, $integration, $test);
        }

    }

    class DataSource extends \_ContentAdapter {
        function __construct() {
        }

    }

    class ConstantData extends \com\bmw\developer\cloud\c1\data\manifest\DataSource {
        function __construct($data) {
            if (!($data instanceof \com\bmw\developer\cloud\c1\data\page\BMWPage)) throw new \Exception('$data must be of type \com\bmw\developer\cloud\c1\data\page\BMWPage');
            parent::__construct();
            $this->data = $data;
        }

    }

    class DataFromJSCall extends \com\bmw\developer\cloud\c1\data\manifest\DataSource {
        function __construct($javaScript) {
            if (!is_string($javaScript)) throw new \Exception('$javaScript must be of type string');
            parent::__construct();
            $this->javaScript = $javaScript;
        }

    }

    class EmailFormPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\Screen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('emailForm', $production, $integration, $test);
            $this->TEMPLATE_NAME = null;
        }

    }

    class FormPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('form', $production, $integration, $test);
        }

    }

    class Header extends \_ContentAdapter {
        function __construct($id, $version, $mainPage, $gps, $icon) {
            if (!is_string($id)) throw new \Exception('$id must be of type string');
            if (!is_string($version)) throw new \Exception('$version must be of type string');
            if (!is_string($mainPage)) throw new \Exception('$mainPage must be of type string');
            if (!is_bool($gps)) throw new \Exception('$gps must be of type boolean');
            if (!is_string($icon)) throw new \Exception('$icon must be of type string');
            $this->id = $id;
            $this->icon = $icon;
            $this->markets = null;
            $this->jsClass = null;
            $this->gps = $gps;
            $this->sdkVersion = 'c1';
            $this->mainpage = $mainPage;
            $this->version = $version;
        }

        function setJsClass($jsClass) {
            if (!is_string($jsClass)) throw new \Exception('$jsClass must be of type string');
            $this->jsClass = $jsClass;
            return $this;
        }

    }

    class LinksPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('links', $production, $integration, $test);
        }

    }

    class Manifest extends \_ContentAdapter {
        function __construct($header) {
            if (!($header instanceof \com\bmw\developer\cloud\c1\data\manifest\Header)) throw new \Exception('$header must be of type \com\bmw\developer\cloud\c1\data\manifest\Header');
            $this->screens = array('_DICTMAINTAINER' => true);
            $this->header = $header;
        }

        function addScreen($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!($value instanceof \com\bmw\developer\cloud\c1\data\manifest\Screen)) throw new \Exception('$value must be of type \com\bmw\developer\cloud\c1\data\manifest\Screen');
            $this->screens[$name] = $value;
            return $this;
        }

    }

    class Markets extends \_ContentAdapter {
        function __construct() {
            $this->serialVersionUID = null;
        }

        function addMarket($market, $languages) {
            if (!in_array($market, array('de','ch','us','fr','it','uk','nl','es','at','kw','ae','cn','dk','be','gr','ca','kr','jp','hu','pl','pt','ru','se','tr','tw','ro','br','hk','lu','cz','ie','no','mo','gb'))) throw new \Exception('value given in $market not found in enumeration');
            if (!is_array($languages)) throw new \Exception('$languages must be of type array');
            foreach($languages as $i) {
                if (!in_array($i, array('DA','DE','EN','IT','ES','FR','NL','EL','AR','KO','JA','HU','PL','PT','RU','SV','TR','ZH','RO','CS','NO'))) throw new \Exception('value given in $i not found in enumeration');
            }
            $this->$market = $languages;
            return $this;
        }

    }

    class MultiPanePageScreen extends \com\bmw\developer\cloud\c1\data\manifest\Screen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('multiPanePage', $production, $integration, $test);
        }

        function addTableConfigurations($configs) {
            if (!is_array($configs)) throw new \Exception('$configs must be of type array');
            $this->configuration['tableConfigurations'] = $configs;
            return $this;
        }

    }

    class PoiDetailPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('poiDetail', $production, $integration, $test);
        }

    }

    class PoiOverviewPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('poiOverview', $production, $integration, $test);
        }

    }

    class PoiOverviewParkopediaPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\Screen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('poiOverviewParkopedia', $production, $integration, $test);
        }

    }

    class RemoteData extends \com\bmw\developer\cloud\c1\data\manifest\DataSource {
        function __construct($source) {
            if (!is_string($source)) throw new \Exception('$source must be of type string');
            parent::__construct();
            $this->parameter = array('_DICTMAINTAINER' => true);
            $this->source = $source;
        }

        function addParameter($name, $value) {
            if (!is_string($name)) throw new \Exception('$name must be of type string');
            if (!is_string($value)) throw new \Exception('$value must be of type string');
            $this->parameter[$name] = $value;
            return $this;
        }

    }

    class TablePageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($tableConfig, $production, $integration = null, $test = null) {
            if (!($tableConfig instanceof \com\bmw\developer\cloud\c1\data\TableConfiguration)) throw new \Exception('$tableConfig must be of type \com\bmw\developer\cloud\c1\data\TableConfiguration');
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('table', $production, $integration, $test);
        }

    }

    class TextPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('text', $production, $integration, $test);
        }

        function addTextConfiguration($textConfig) {
            if (!($textConfig instanceof \com\bmw\developer\cloud\c1\data\TextConfig)) throw new \Exception('$textConfig must be of type \com\bmw\developer\cloud\c1\data\TextConfig');
            $this->configuration['textConfig'] = $textConfig;
            return $this;
        }

    }

    class TextWithToolbarPageScreen extends \com\bmw\developer\cloud\c1\data\manifest\TableScreen {
        function __construct($production, $integration = null, $test = null) {
            if (!($production instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$production must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($integration != null && !($integration instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$integration must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            if ($test != null && !($test instanceof \com\bmw\developer\cloud\c1\data\manifest\DataSource)) throw new \Exception('$test must be of type \com\bmw\developer\cloud\c1\data\manifest\DataSource');
            parent::__construct('textWithToolbar', $production, $integration, $test);
        }

    }

}

?>
