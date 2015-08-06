<?php

namespace Drola\CoordinateTransformationLibrary;

use Drola\CoordinateTransformationLibrary\Position\RT90Position;

class Transform
{
    public static function RT90ToWGS84($latitude, $longitude)
    {
        $position = new RT90Position($latitude, $longitude);
        $wgsPos = $position->toWGS84();
        return array($wgsPos->getLatitude(), $wgsPos->getLongitude());
    }
}
