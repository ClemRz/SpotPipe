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
try {
    require __DIR__ . '/vendor/autoload.php';
    /*spl_autoload_register(function ($class) {
        include "classes/{$class}.php";
    });*/
    require __DIR__ . '/MyCurl.php';
    require __DIR__ . '/JsonAdapter.php';
    require __DIR__ . '/JsonAdapterFactory.php';
    require __DIR__ . '/SpotJsonAdapter.php';
    $feed = $_GET['feed'];
    $password = $_GET['password'];
    $forLineString = !empty($_GET['linestring']);
    $all = !empty($_GET['all']);
    $jsonAdapter = JsonAdapterFactory::getJsonAdapter('Spot');
    $jsonAdapter->setFeed($feed)
        ->setPassword($password)
        ->setAsLineString($forLineString)
        ->setAll($all)
        ->fetchFeatures();
    $features = $jsonAdapter->getFeatures();
    if ($jsonAdapter->asLineString()) {
        $lineString = new \GeoJson\Geometry\LineString($features);
        $feature = new \GeoJson\Feature\Feature($lineString);
        $collection = array($feature);
    } else {
        $collection = array();
        $featureReflector = new ReflectionClass('\GeoJson\Feature\Feature');
        foreach ($features as $point) {
            $point[0] = new \GeoJson\Geometry\Point($point[0]);
            $feature = $featureReflector->newInstanceArgs($point);
            array_push($collection, $feature);
        }
    }
    $jsonObject = new GeoJson\Feature\FeatureCollection($collection);
} catch (Exception $e) {
    $jsonObject = new stdClass();
    $jsonObject->error = $e->getMessage();
} finally {
    $json = json_encode($jsonObject);
    header('Content-Type: application/json; charset=utf-8');
    echo($json);
}

