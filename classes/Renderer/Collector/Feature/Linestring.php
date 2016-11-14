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

namespace Renderer\Collector\Feature;

use GeoJson\Feature\Feature;
use GeoJson\Geometry;
use Renderer\Collector\Collector;

class Linestring implements Collector
{

    public function collect($features)
    {
        $lineString = new Geometry\LineString($features);
        $feature = new Feature($lineString);
        return array($feature);
    }
}