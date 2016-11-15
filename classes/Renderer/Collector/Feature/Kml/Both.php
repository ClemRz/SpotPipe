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

class Both implements Kml
{
    private $_dom;
    private $_documentNode;

    public function collect($features)
    {
        $linestring = $this->getLineString($features);
        $pointCollector = new Point();
        $pointCollector->setDom($this->_dom);
        $pointCollector->setDocumentNode($this->_documentNode);
        $linestringCollector = new LineString();
        $linestringCollector->setDom($this->_dom);
        $linestringCollector->setDocumentNode($this->_documentNode);
        $linestringCollector->collect($linestring);
        $pointCollector->collect($features);
    }

    public function setDom($dom)
    {
        $this->_dom = $dom;
    }

    public function setDocumentNode($docNode)
    {
        $this->_documentNode = $docNode;
    }

    private function getLineString($features) {
        return array_map(function ($feature) {
            return $feature[0];
        }, $features);
    }
}