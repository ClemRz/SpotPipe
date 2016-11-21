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

use Adapter\AdapterFactory;
use Cache\CacheManager;
use Exception\ExceptionFactory;
use Renderer\RendererFactory;
use Validator\Map;
use Validator\ParametersValidator;

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

    $validator = new ParametersValidator($client, $sourceFormat, $targetFormat, $featureType);
    $validator->validate();

    $adapter = AdapterFactory::getAdapter($client, $sourceFormat);
    $adapter->setFeatureType($featureType);
    $cacheId = $adapter->getCacheId();

    $cacheManager = new CacheManager();
    $cacheManager->setCacheName($client, $featureType, $cacheId);
    $features = $cacheManager->getFeatures($adapter);

    $fileName = $adapter->getFileName();

    $renderer = RendererFactory::getRenderer($targetFormat);
    $renderer->setFeatureType($featureType);
    $string = $renderer->render($features);
    $header = $renderer->getHeader();
    $fileExtension = $renderer->getFileExtension();

    header($header);
    header("Content-Disposition: filename=\"{$fileName}.{$fileExtension}\"");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo($string);

} catch (Exception $e) {

    $targetFormat = ucfirst($_GET['to']);
    $targetFormats = array();
    array_walk_recursive(Map::$TARGET_FORMATS, function ($a) use (&$targetFormats) {
        $targetFormats[] = $a;
    });
    if (empty($targetFormat) || !in_array($targetFormat, $targetFormats)) {
        $targetFormat = 'Text';
    }
    $renderer = ExceptionFactory::getRenderer($targetFormat);
    $string = $renderer->render($e);
    $header = $renderer->getHeader();

    header($header);
    echo($string);

}

