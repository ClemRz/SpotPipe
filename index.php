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

    $feed = $_GET['feed'];
    $password = $_GET['password'];
    $forLineString = !empty($_GET['linestring']);
    $all = !empty($_GET['all']);

    $adapter = AdapterFactory::getAdapter('Spot', 'Json');
    $adapter->setFeed($feed)
        ->setPassword($password)
        ->setAll($all)
        ->setType($forLineString ? 'Linestring' : 'Point')
        ->fetchFeatures();
    $features = $adapter->getFeatures();

    $renderer = RendererFactory::getRenderer('GeoJson');
    $renderer->setType($forLineString ? 'Linestring' : 'Point');
    $jsonObject = $renderer->render($features);

} catch (Exception $e) {
    $jsonObject = new stdClass();
    $jsonObject->error = $e->getMessage();
} finally {
    $json = json_encode($jsonObject);
    header('Content-Type: application/json; charset=utf-8');
    echo($json);
}

