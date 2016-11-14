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

namespace Spot\FeatureFetcher;

class LinestringFeatureFetcher implements FeatureFetcher
{

    public function fetchFeature(array $messages)
    {
        $points = array();
        foreach ($messages as $index => $message) {
            $point = SpotFeatureHelper::getPoint($message);
            array_push($points, $point);
        }
        return $points;
    }

}