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

use \Adapter\AdapterFactory;
use \Renderer\RendererFactory;


try {
    require __DIR__ . '/vendor/autoload.php';
    spl_autoload_register(function ($class) {
        $class = str_replace('\\', '/', $class);
        include "./classes/{$class}.php";
    });

    $client = ucfirst($_GET['client']);
    if (empty($client)) {
        throw new Exception('Please specify client parameter. Available: Spot.');
    }
    $sourceFormat = ucfirst($_GET['from']);
    if (empty($sourceFormat)) {
        throw new Exception('Please specify from parameter. Available: Json.');
    }
    $targetFormat = ucfirst($_GET['to']);
    if (empty($targetFormat)) {
        throw new Exception('Please specify to parameter. Available: Geojson.');
    }
    $featureType = ucfirst($_GET['feature']);
    if (empty($featureType)) {
        throw new Exception('Please specify feature parameter. Available: Point, Linestring.');
    }

    $adapter = AdapterFactory::getAdapter($client, $sourceFormat);
    $adapter->setFeatureType($featureType);
    $features = $adapter->getFeatures();

    $renderer = RendererFactory::getRenderer($targetFormat);
    $renderer->setFeatureType($featureType);
    $jsonObject = $renderer->render($features);

} catch (Exception $e) {
    $jsonObject = new stdClass();
    $jsonObject->error = $e->getMessage();
} finally {
    $json = json_encode($jsonObject);
    header('Content-Type: application/json; charset=utf-8');
    echo($json);
}

