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

namespace Cache;

class CacheManager
{
    private $_cachePath;

    public function setCacheName($client, $featureType, $id)
    {
        if (!empty($id)) {
            $this->_cachePath = "classes/Cache/storage/{$client}_{$id}";
        }
    }

    public function getFeatures($adapter)
    {
        $lastCachedFeature = $this->readLastFeature();
        if ($lastCachedFeature !== null && $adapter->isUpToDate($lastCachedFeature)) {
            $features = $this->readAllFeatures();
            return $features;
        } else {
            $features = $adapter->getFeatures();
            if (!empty($this->_cachePath)) {
                $this->writeFeatures($features);
            }
            return $features;
        }
    }

    private function writeFeatures(array $features)
    {
        $file = $this->openFileForOverwrite();
        foreach ($features as $feature) {
            fwrite($file, json_encode($feature) . "\n");
        }
    }

    private function readLastFeature()
    {
        if ($this->cacheExists()) {
            $file = escapeshellarg($this->_cachePath);
            $line = `tail -n 1 $file`;
            return json_decode($line, true);
        }
        return null;
    }

    private function readAllFeatures()
    {
        if ($this->cacheExists()) {
            $features = array();
            $file = $this->openFileForRead();
            while (!feof($file)) {
                $feature = json_decode(fgets($file), true);
                if (!empty($feature)) {
                    array_push($features, $feature);
                }
            }
            return $features;
        }
        return null;
    }

    private function cacheExists()
    {
        return file_exists($this->_cachePath);
    }

    private function openFileForOverwrite()
    {
        $file = fopen($this->_cachePath, "w") or die("Could not write to cache.");
        return $file;
    }

    private function openFileForRead()
    {
        $file = fopen($this->_cachePath, "r") or die("Could not read cache.");
        return $file;
    }
}