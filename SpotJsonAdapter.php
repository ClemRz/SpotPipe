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
    private $_jsonString;
    private $_jsonObject;

    public function __construct($jsonString) {
        $this->_jsonString = $jsonString;
        $this->_jsonObject = json_decode($this->_jsonString);
    }

    public function getGeoJsonFeatures() {
        $messages = $this->getMessages();
        $features = array();
        foreach ($messages as $message) {
            if ($message->latitude === null || $message->longitude === null) {
                throw new Exception('Invalid coordinates.');
            }
            $point = new \GeoJson\Geometry\Point([$message->longitude, $message->latitude]);
            $properties = array(
                "clientUnixTime" => $message->clientUnixTime,
                "id" => $message->id,
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
            $id = $message->id;
            $feature = new \GeoJson\Feature\Feature($point, $properties, $id);
            array_push($features, $feature);
        }
        return new GeoJson\Feature\FeatureCollection($features);
    }

    private function getMessages() {
        $response = $this->_jsonObject->response;
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
}