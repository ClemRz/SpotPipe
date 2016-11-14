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

namespace Spot;

use Spot\FeatureFetcher\FeatureFetcherFactory;
use Spot\FeatureFetcher\FeatureFetcher;

class SpotJsonAdapter implements \Adapter\JsonAdapter
{
    private $_features = array();
    private $_lastMessagesCount;
    private $_feed;
    private $_type = 'Point';
    private $_password = '';
    private $_all = false;

    public function setFeed($feed)
    {
        $this->_feed = $feed;
        return $this;
    }

    public function setPassword($password)
    {

        $this->_password = $password;
        return $this;
    }

    public function setAll($all)
    {
        $this->_all = $all;
        return $this;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getFeatures()
    {
        return $this->_features;
    }

    //TODO clement do some caching before fetching the entire feed
    public function fetchFeatures()
    {
        if (empty($this->_feed)) {
            throw new \Exception('No feed provided.');
        }
        $fetcher = FeatureFetcherFactory::getFetcher($this->_type);
        $start = 0;
        do {
            $jsonObject = $this->getJsonObject($start);
            $messages = $this->getMessages($jsonObject);
            $this->_lastMessagesCount = $jsonObject->response->feedMessageResponse->count;
            $features = $this->fetchFeature($fetcher, $messages);
            $this->_features = array_merge($this->_features, $features);
            $start += 50;
        } while ($this->_all && $this->hasMoreMessages());
    }

    private function fetchFeature(FeatureFetcher $fetcher, array $message) {
        return $fetcher->fetchFeature($message);
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