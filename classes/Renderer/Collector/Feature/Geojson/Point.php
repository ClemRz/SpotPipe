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

namespace Renderer\Collector\Feature\Geojson;

use GeoJson\Geometry;
use Renderer\Collector\Collector;

class Point implements Collector
{

    public function collect($features)
    {
        $collection = array();
        $featureReflector = new \ReflectionClass('\GeoJson\Feature\Feature');
        foreach ($features as $point) {
            $point[0] = new Geometry\Point($point[0]);
            $feature = $featureReflector->newInstanceArgs($point);
            array_push($collection, $feature);
        }
        return $collection;
    }
}