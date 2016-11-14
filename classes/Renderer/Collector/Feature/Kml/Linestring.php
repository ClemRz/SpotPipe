<?php
/*
    Copyright (C) 2016 Clément Ronzon

    This file is part of SpotPipe.

    SpotPipe is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Foobar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Renderer\Collector\Feature\Kml;

class Linestring implements Kml
{
    private $_dom;
    private $_documentNode;

    public function collect($features)
    {
        $node = $this->_dom->createElement('Placemark');
        $placeNode = $this->_documentNode->appendChild($node);
        $styleUrl = $this->_dom->createElement('styleUrl', '#lineStyle');
        $placeNode->appendChild($styleUrl);
        $linestringNode = $this->_dom->createElement('LineString');
        $tessellateNode = $this->_dom->createElement('tessellate', '1');
        $linestringNode->appendChild($tessellateNode);
        $tessellateNode = $this->_dom->createElement('tessellate', '1');
        $linestringNode->appendChild($tessellateNode);
        $coordinatesNode = $this->_dom->createElement('coordinates', join(' ', array_map(function ($point) {
            return join(",", $point) . ',0';
        }, $features)));
        $linestringNode->appendChild($coordinatesNode);
        $placeNode->appendChild($linestringNode);
    }

    public function setDom($dom)
    {
        $this->_dom = $dom;
    }

    public function setDocumentNode($docNode)
    {
        $this->_documentNode = $docNode;
    }
}