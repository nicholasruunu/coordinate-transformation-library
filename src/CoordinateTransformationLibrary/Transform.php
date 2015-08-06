<?php

namespace Drola\CoordinateTransformationLibrary;

use Drola\CoordinateTransformationLibrary\Position\RT90Position;
use Drola\CoordinateTransformationLibrary\Position\SWEREF99Position;

class Transform
{
    public static function RT90ToWGS84($latitude, $longitude)
    {
        $position = new RT90Position($latitude, $longitude);
        $wgsPos = $position->toWGS84();
        return array($wgsPos->getLatitude(), $wgsPos->getLongitude());
    }

    public static function SWEREF99ToWGS84($latitude, $longitude)
    {
        $position = new SWEREF99Position($latitude, $longitude);
        $wgsPos = $position->toWGS84();
        return array($wgsPos->getLatitude(), $wgsPos->getLongitude());
    }
}
