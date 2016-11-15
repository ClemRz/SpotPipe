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

namespace Validator;

class ParametersValidator
{
    private $_client;
    private $_sourceFormat;
    private $_targetFormat;
    private $_featureType;

    public function __construct($client, $sourceFormat, $targetFormat, $featureType)
    {
        $this->_client = $client;
        $this->_sourceFormat = $sourceFormat;
        $this->_targetFormat = $targetFormat;
        $this->_featureType = $featureType;
    }

    public function validate()
    {
        $clientMap = Map::$CLIENT_SOURCE_FORMAT;
        if (array_key_exists($this->_client, $clientMap)) {
            if (!in_array($this->_sourceFormat, $clientMap[$this->_client])) {
                throw new \Exception("Wrong value for from parameter: {$this->_sourceFormat}.");
            }
        } else {
            throw new \Exception("Wrong value for client parameter: {$this->_client}.");
        }
        $targetMap = Map::$TARGET_FORMATS;
        if (array_key_exists($this->_targetFormat, $targetMap)) {
            if (!in_array($this->_featureType, $targetMap[$this->_targetFormat])) {
                throw new \Exception("Wrong value for feature parameter: {$this->_featureType}.");
            }
        } else {
            throw new \Exception("Wrong value for to parameter: {$this->_targetFormat}.");
        }
    }
}