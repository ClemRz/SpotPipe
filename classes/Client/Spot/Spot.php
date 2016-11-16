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

namespace Client\Spot;

class Spot
{
    const BASE_URL = 'https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed';
    const LATEST_ENDPOINT = 'latest.json';
    const MESSAGE_ENDPOINT = 'message.json';
    const PASSWORD_PARAMETER = 'feedPassword';
    const START_PARAMETER = 'start';

    public static $MESSAGE_TYPES = array(
        'OK',
        'TRACK',
        'EXTREME-TRACK',
        'UNLIMITED-TRACK',
        'NEWMOVEMENT',
        'HELP',
        'HELP-CANCEL',
        'CUSTOM',
        'POI',
        'STOP'
    );
}