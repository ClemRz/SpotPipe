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

namespace Renderer;

use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\Geometry\LineString;
use GeoJson\Geometry\Point;

class GeoJsonRenderer implements Renderer
{
    private $_type = 'Point';

    public function render(array $features)
    {
        if ($this->_type === 'Linestring') {
            $lineString = new LineString($features);
            $feature = new Feature($lineString);
            $collection = array($feature);
        } else {
            $collection = array();
            $featureReflector = new \ReflectionClass('\GeoJson\Feature\Feature');
            foreach ($features as $point) {
                $point[0] = new Point($point[0]);
                $feature = $featureReflector->newInstanceArgs($point);
                array_push($collection, $feature);
            }
        }
        $jsonObject = new FeatureCollection($collection);
        return $jsonObject;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }
}