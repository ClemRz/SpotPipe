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

namespace Renderer\Format;


use Renderer\Renderer;
use Renderer\Collector\CollectorFactory;

class Kml implements Renderer
{
    private $_featureType;

    public function render(array $features)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
        $parNode = $dom->appendChild($node);
        $document = $dom->createElement('Document');
        $docNode = $parNode->appendChild($document);

        $collector = CollectorFactory::getCollector('Kml', $this->_featureType);
        $collector->setDom($dom)
            ->setDocumentNode($docNode)
            ->applyStyleNode()
            ->collect($features);

        return $dom->saveXML();
    }

    public function setFeatureType($featureType)
    {
        $this->_featureType = $featureType;
    }

    public function getHeader()
    {
        return 'Content-type: application/vnd.google-earth.kml+xml; filename="bla.kml"';
    }

    public function getFileExtension()
    {
        return 'kml';
    }
}