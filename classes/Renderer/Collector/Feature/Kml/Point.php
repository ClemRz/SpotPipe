<?php
/*
    Copyright (C) 2016 Clément Ronzon

    This file is part of SpotPipe.

    SpotPipe is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    SpotPipe is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SpotPipe.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Renderer\Collector\Feature\Kml;

use Client\Spot\Spot;

class Point implements Kml
{
    private $_dom;
    private $_documentNode;

    public function collect($features)
    {
        foreach ($features as $point) {
            $node = $this->_dom->createElement('Placemark');
            $placeNode = $this->_documentNode->appendChild($node);
            $placeNode->setAttribute('id', 'placemark' . $point[2]);
            $nameNode = $this->_dom->createElement('name', $point[1]['index']);
            $placeNode->appendChild($nameNode);
            $descNode = $this->_dom->createElement('description', $this->getDescription($point));
            $placeNode->appendChild($descNode);
            $styleUrl = $this->_dom->createElement('styleUrl', "#{$point[1]['messageType']}MarkerStyle");
            $placeNode->appendChild($styleUrl);
            $tsNode = $this->_dom->createElement('TimeStamp');
            $whenNode = $this->_dom->createElement('when', $this->getXmlFormattedDateString($point));
            $tsNode->appendChild($whenNode);
            $placeNode->appendChild($tsNode);
            $pointNode = $this->_dom->createElement('Point');
            $coordinatesNode = $this->_dom->createElement('coordinates', join(",", $point[0]));
            $pointNode->appendChild($coordinatesNode);
            $placeNode->appendChild($pointNode);
        }
        return $this;
    }

    public function applyStyleNode()
    {
        foreach (Spot::$MESSAGE_TYPES as $messageType) {
            $styleNode = $this->_dom->createElement('Style');
            $styleNode->setAttribute('id', "{$messageType}MarkerStyle");
            $markerIconStyleNode = $this->_dom->createElement('IconStyle');
            $markerIconNode = $this->_dom->createElement('Icon');
            $markerHref = $this->_dom->createElement('href', $this->getIconHref($messageType));
            $markerIconNode->appendChild($markerHref);
            $markerIconStyleNode->appendChild($markerIconNode);
            $styleNode->appendChild($markerIconStyleNode);
            $this->_documentNode->appendChild($styleNode);
        }
        return $this;
    }

    public function setDom($dom)
    {
        $this->_dom = $dom;
        return $this;
    }

    public function setDocumentNode($docNode)
    {
        $this->_documentNode = $docNode;
        return $this;
    }

    private function getXmlFormattedDateString($point)
    {
        return preg_replace('/\+([\d]{2})([\d]{2})/', '+$1:$2', $point[1]['dateTime']);
    }

    private function getIconHref($messageType)
    {
        switch ($messageType) {
            case 'OK':
                return 'http://maps.google.com/mapfiles/kml/shapes/homegardenbusiness.png';
            case 'TRACK':
                return 'http://maps.google.com/mapfiles/kml/shapes/hiker.png';
            case 'EXTREME-TRACK':
                return 'http://maps.google.com/mapfiles/kml/shapes/hiker.png';
            case 'UNLIMITED-TRACK':
                return 'http://maps.google.com/mapfiles/kml/shapes/hiker.png';
            case 'NEWMOVEMENT':
                return 'http://maps.google.com/mapfiles/kml/shapes/arrow-reverse.png';
            case 'HELP':
                return 'http://maps.google.com/mapfiles/kml/shapes/caution.png';
            case 'HELP-CANCEL':
                return 'http://maps.google.com/mapfiles/kml/shapes/info.png';
            case 'CUSTOM':
                return 'http://maps.google.com/mapfiles/kml/shapes/post_office.png';
            case 'POI':
                return 'http://maps.google.com/mapfiles/kml/shapes/arrow.png';
            case 'STOP':
                return 'http://maps.google.com/mapfiles/kml/shapes/polygon.png';
            default:
                return 'http://maps.google.com/mapfiles/kml/paddle/red-circle.png';
        }
    }

    private function getDescription($point)
    {
        $properties = $point[1];
        $string = urldecode(http_build_query($properties, '', '<br>'));
        if (array_key_exists('messageContent', $properties)) {
            $messageContent = urldecode($properties['messageContent']);
            $string = "<h3>{$messageContent}</h3><p>{$string}</p>";
        }
        return $string;
    }
}