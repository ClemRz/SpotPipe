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

class SpotJsonAdapter implements JsonAdapter
{
    private $_asLineString = false;
    private $_features = array();
    private $_lastMessagesCount;
    private $_feed;
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

    public function setAsLineString($asLineString)
    {
        $this->_asLineString = $asLineString;
        return $this;
    }

    public function setAll($all)
    {
        $this->_all = $all;
        return $this;
    }

    public function getFeatures()
    {
        return $this->_features;
    }

    public function asLineString()
    {
        return $this->_asLineString;
    }

    //TODO clement do some caching before fetching the entire feed
    public function fetchFeatures()
    {
        if (empty($this->_feed)) {
            throw new Exception('No feed provided.');
        }
        $start = 0;
        do {
            $jsonObject = $this->getJsonObject($start);
            $messages = $this->getMessages($jsonObject);
            $this->_lastMessagesCount = $jsonObject->response->feedMessageResponse->count;

            if ($this->_asLineString) {
                $features = $this->getLineString($messages);
            } else {
                $features = $this->getPoints($messages);
            }
            $this->_features = array_merge($this->_features, $features);
            $start += 50;
        } while ($this->_all && $this->hasMoreMessages());
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
            throw new Exception('Response is null.');
        }
        $feedMessage = $response->feedMessageResponse;
        if ($feedMessage === null) {
            throw new Exception('Feed message is null.');
        }
        $messages = $feedMessage->messages;
        if ($messages === null) {
            throw new Exception('Messages is null.');
        }
        $message = $messages->message;
        if ($message === null) {
            throw new Exception('Message is null.');
        }
        return $message;
    }

    private function getLineString(array $messages)
    {
        $points = $this->getPoints($messages);
        array_walk($points, function (&$point) {
            $point = $point[0];
        });
        return $points;
    }

    private function getPoints(array $messages)
    {
        $points = array();
        foreach ($messages as $index => $message) {
            $point = $this->getPoint($message);
            $properties = $this->getProperties($message, $index);
            $id = $message->id;
            array_push($points, array($point, $properties, $id));
        }
        return $points;
    }

    private function getJsonObject($start = 0, $latest = false)
    {
        $myCurl = new MyCurl($this->getUrl($start, $latest));
        $myCurl->createCurl();
        $jsonObject = json_decode($myCurl);
        return $jsonObject;
    }

    private function getProperties($message, $index)
    {
        return array(
            "clientUnixTime" => $message->clientUnixTime,
            "id" => $message->id,
            "index" => $index,
            "messengerId" => $message->messengerId,
            "messengerName" => $message->messengerName,
            "unixTime" => $message->unixTime,
            "messageType" => $message->messageType,
            "latitude" => $message->latitude,
            "longitude" => $message->longitude,
            "modelId" => $message->modelId,
            "showCustomMsg" => $message->showCustomMsg,
            "dateTime" => $message->dateTime,
            "batteryState" => $message->batteryState,
            "hidden" => $message->hidden,
            "messageContent" => $message->messageContent
        );
    }

    private function getPoint($message)
    {
        if ($message->latitude === null || $message->longitude === null) {
            throw new Exception('Invalid coordinates.');
        }
        //$point = new \GeoJson\Geometry\Point([$message->longitude, $message->latitude]);
        return array($message->longitude, $message->latitude);
    }
}

class Spot
{
    const BASE_URL = 'https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed';
    const LATEST_ENDPOINT = 'latest.json';
    const MESSAGE_ENDPOINT = 'message.json';
    const PASSWORD_PARAMETER = 'feedPassword';
    const START_PARAMETER = 'start';
}