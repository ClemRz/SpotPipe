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

class SpotJsonAdapter
{
    private $_forLineString;
    private $_features = array();
    private $_lastMessagesCount;
    private $_url;
    private $_all;

    public function __construct($feed, $password = '', $forLineString = false, $all = false)
    {
        if (empty($feed)) {
            throw new Exception('No feed provided.');
        }
        $this->_url = "https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/{$feed}/message.json";
        if (!empty($password)) {
            $this->_url .= "?feedPassword={$password}";
        }
        $this->_forLineString = $forLineString;
        $this->_all = $all;
    }

    public function getGeoJsonFeatures()
    {
        $this->fetchGeoJsonFeatures();
        return new GeoJson\Feature\FeatureCollection($this->_features);
    }

    private function hasMoreMessages()
    {
        return $this->_lastMessagesCount === 50;
    }

    private function fetchGeoJsonFeatures()
    {
        $start = 0;
        do {
            $jsonObject = $this->getJsonObject($start);
            $messages = $this->getMessages($jsonObject);
            $this->_lastMessagesCount = $jsonObject->response->feedMessageResponse->count;
            $this->_features = array_merge($this->_features, $this->getFeatures($messages));
            $start += 50;
        } while ($this->_all && $this->hasMoreMessages());
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

    private function getFeatures(array $messages)
    {
        if ($this->_forLineString) {
            return $this->getLineStringFeatures($messages);
        } else {
            return $this->getPointFeatures($messages);
        }
    }

    private function getPointFeatures(array $messages)
    {
        $features = array();
        foreach ($messages as $index => $message) {
            $point = $this->getPoint($message);
            $properties = $this->getProperties($message, $index);
            $id = $message->id;
            $feature = new \GeoJson\Feature\Feature($point, $properties, $id);
            array_push($features, $feature);
        }
        return $features;
    }

    private function getLineStringFeatures(array $messages)
    {
        $features = array();
        $points = array();
        foreach ($messages as $message) {
            $point = $this->getPoint($message);
            array_push($points, $point);
        }
        $lineString = new \GeoJson\Geometry\LineString($points);
        $feature = new \GeoJson\Feature\Feature($lineString);
        array_push($features, $feature);
        return $features;
    }

    private function getJsonObject($start = 0)
    {
        $myCurl = new MyCurl($this->_url . '&start=' . $start);
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
        $point = new \GeoJson\Geometry\Point([$message->longitude, $message->latitude]);
        return $point;
    }
}