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
    private $_pointCollector;
    private $_linestringCollector;

    public function __construct()
    {
        $this->_pointCollector = new Point();
        $this->_linestringCollector = new Linestring();
    }

    public function collect($features)
    {
        $linestring = $this->getLineString($features);
        $this->_linestringCollector->collect($linestring);
        $this->_pointCollector->collect($features);
        return $this;
    }

    public function applyStyleNode()
    {
        $this->_linestringCollector->applyStyleNode();
        $this->_pointCollector->applyStyleNode();
        return $this;
    }

    public function setDom($dom)
    {
        $this->_pointCollector->setDom($dom);
        $this->_linestringCollector->setDom($dom);
        return $this;
    }

    public function setDocumentNode($docNode)
    {
        $this->_pointCollector->setDocumentNode($docNode);
        $this->_linestringCollector->setDocumentNode($docNode);
        return $this;
    }

    private function getLineString($features) {
        return array_map(function ($feature) {
            return $feature[0];
        }, $features);
    }
}