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

namespace Client\Spot\Format;

use Adapter\Adapter;
use Client\Spot\Fetcher\FetcherFactory;
use Client\Spot\Indexer\IndexerFactory;
use Client\Spot\Spot;

class Json implements Adapter
{
    private $_lastMessagesCount;
    private $_feed;
    private $_featureType = 'Point';
    private $_password = '';
    private $_all = false;
    private $_name;

    function __construct()
    {
        $this->_feed = $_GET['feed'];
        if (empty($this->_feed)) {
            throw new \Exception('Please specify feed parameter.');
        }
        $this->_password = $_GET['password'];
        $this->_all = !empty($_GET['all']);
    }

    public function setFeatureType($featureType)
    {
        $this->_featureType = $featureType;
        return $this;
    }

    //TODO clement do some caching before fetching the entire feed
    public function getFeatures()
    {
        $fetcher = FetcherFactory::getFetcher($this->_featureType);
        $allFeatures = array();
        $start = 0;
        do {
            $jsonObject = $this->getJsonObject($start);
            $feedMessageResponse = $jsonObject->response->feedMessageResponse;
            $this->_lastMessagesCount = $feedMessageResponse->count;
            $this->_name = $feedMessageResponse->feed->name;
            $messages = $this->getMessages($jsonObject);
            $features = $fetcher->fetchFeature($messages);
            $allFeatures = array_merge($allFeatures, $features);
            $start += 50;
        } while ($this->_all && $this->hasMoreMessages());
        $allFeatures = array_reverse($allFeatures);
        $indexer = IndexerFactory::getIndexer($this->_featureType);
        $indexer->index($allFeatures);
        return $allFeatures;
    }

    public function getFileName()
    {
        return "{$this->_name} - {$this->_featureType}";
    }

    private function getUrl($start, $latest = false)
    {
        $endpoint = $latest ? Spot::LATEST_ENDPOINT : Spot::MESSAGE_ENDPOINT;
        $url = Spot::BASE_URL . "/{$this->_feed}/{$endpoint}?";
        $parameters = array();
        if (!empty($this->_password)) {
            array_push($parameters, Spot::PASSWORD_PARAMETER . "={$this->_password}");
        }
        array_push($parameters, Spot::START_PARAMETER . "={$start}");
        $url .= join('&', $parameters);
        return $url;
    }

    private function hasMoreMessages()
    {
        return $this->_lastMessagesCount === 50;
    }

    private function getMessages($jsonObject)
    {
        $response = $jsonObject->response;
        if ($response === null) {
            throw new \Exception('Response is null.');
        }
        $feedMessage = $response->feedMessageResponse;
        if ($feedMessage === null) {
            throw new \Exception('Feed message is null.');
        }
        $messages = $feedMessage->messages;
        if ($messages === null) {
            throw new \Exception('Messages is null.');
        }
        $message = $messages->message;
        if ($message === null) {
            throw new \Exception('Message is null.');
        }
        return $message;
    }

    private function getJsonObject($start = 0, $latest = false)
    {
        $myCurl = new \MyCurl($this->getUrl($start, $latest));
        $myCurl->createCurl();
        $jsonObject = json_decode($myCurl);
        return $jsonObject;
    }
}