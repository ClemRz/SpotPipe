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
    require __DIR__ . '/MyCurl.php';
    require __DIR__ . '/SpotJsonAdapter.php';
    $feed = $_GET['feed'];
    $password = $_GET['password'];
    if (empty($feed)) {
        throw new Exception('No feed provided.');
    }
    $url = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/{$feed}/message.json";
    if (!empty($password)) {
        $url .= "?feedPassword={$password}";
    }
    $myCurl = new MyCurl($url);
    $myCurl->createCurl();
    $adapter = new SpotJsonAdapter($myCurl);
    $jsonObject = $adapter->getGeoJsonFeatures();
} catch (Exception $e) {
    $jsonObject = new stdClass();
    $jsonObject->error = $e->getMessage();
} finally {
    $json = json_encode($jsonObject);
    header('Content-Type: application/json; charset=utf-8');
    echo($json);
}

