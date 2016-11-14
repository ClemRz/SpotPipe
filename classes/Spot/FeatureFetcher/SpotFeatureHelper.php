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

namespace Spot\FeatureFetcher;

class SpotFeatureHelper
{
    public static function getProperties($message, $index)
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

    public static function getPoint($message)
    {
        if ($message->latitude === null || $message->longitude === null) {
            throw new \Exception('Invalid coordinates.');
        }
        return array($message->longitude, $message->latitude);
    }

}