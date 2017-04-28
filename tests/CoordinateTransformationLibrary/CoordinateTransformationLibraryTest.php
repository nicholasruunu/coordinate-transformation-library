<?php

/**
 *  CoordinateTransformationLibrary - David Gustafsson 2012.
 *
 *  RT90, SWEREF99 and WGS84 coordinate transformation library
 *
 * This library is a PHP port of the .NET library by Björn Sållarp.
 *  calculations are based entirely on the excellent
 *  javscript library by Arnold Andreassons.
 *
 * Source: http://www.lantmateriet.se/geodesi/
 * Source: Arnold Andreasson, 2007. http://mellifica.se/konsult
 * Source: Björn Sållarp. 2009. http://blog.sallarp.com
 * Source: Mathias Åhsberg, 2009. http://github.com/goober/
 * Author: David Gustafsson, 2012. http://github.com/david-xelera/
 *
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

namespace Drola\Tests\CoordinateTransformationLibrary;

use Drola\CoordinateTransformationLibrary\Position\RT90Position;
use Drola\CoordinateTransformationLibrary\Projection\RT90Projection;
use Drola\CoordinateTransformationLibrary\Position\WGS84Position;
use Drola\CoordinateTransformationLibrary\Position\WGS84Format;
use Drola\CoordinateTransformationLibrary\Position\SWEREF99Position;
use Drola\CoordinateTransformationLibrary\Projection\SWEREFProjection;
use Drola\CoordinateTransformationLibrary\ParseException;

class CoordinateTransformationLibraryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider coordinatesRT90ToWGS84
     */
    public function testRT90ToWGS84($rt90Lat, $rt90Lng, $wgs84Lat, $wgs84Lng)
    {
        $position = new RT90Position($rt90Lat, $rt90Lng);
        $wgsPos = $position->toWGS84();

//        // Values from Hitta.se for the conversion
//        $latFromHitta = 59.3489;
//        $lonFromHitta = 18.0473;
//
//        $lat = ((double) round($wgsPos->getLatitude() * 10000)) / 10000;
//        $lon = ((double) round($wgsPos->getLongitude() * 10000)) / 10000;
//
//        $this->assertEquals($latFromHitta, $lat); //TODO: fix rounding according to extra parameter: 0.00001d
//        $this->assertEquals($lonFromHitta, $lon);
//
//        // String values from Lantmateriet.se, they convert DMS only.
//        // Reference: http://www.lantmateriet.se/templates/LMV_Enkelkoordinattransformation.aspx?id=11500
//        $latDmsStringFromLM = "N 59º 20' 56,09287\"";
//        $lonDmsStringFromLM = "E 18º 2' 50,34806\"";

        $this->assertEquals($wgs84Lat, $wgsPos->latitudeToString(WGS84Format::DEGREES_MINUTES_SECONDS));
        $this->assertEquals($wgs84Lng, $wgsPos->longitudeToString(WGS84Format::DEGREES_MINUTES_SECONDS));

    }

    //@Test
    public function testWGS84ToRT90()
    {
        $wgsPos = null;
        $rtPos = null;
        try {
            $wgsPos = new WGS84Position("N 59º 58' 55.23\" E 017º 50' 06.12\"", WGS84Format::DEGREES_MINUTES_SECONDS);
            $rtPos = new RT90Position($wgsPos, RT90Projection::RT90_2_5_GON_V);
        } catch (ParseException $e) {
            $this->fail($e->getMessage());
        }
        // Conversion values from Lantmateriet.se, they convert from DMS only.
        // Reference: http://www.lantmateriet.se/templates/LMV_Enkelkoordinattransformation.aspx?id=11500
        $xPosFromLM = 6653174.343;
        $yPosFromLM = 1613318.742;

        $lat = ((double) round($rtPos->getLatitude() * 1000) / 1000);
        $lon = ((double) round($rtPos->getLongitude() * 1000) / 1000);

        $this->assertEquals($lat, $xPosFromLM); //fix accuracy: 0.0001d
        $this->assertEquals($lon, $yPosFromLM);
    }

    public function testWGS84ToSweref()
    {
        $wgsPos = new WGS84Position();

        $wgsPos->setLatitudeFromString("N 59º 58' 55.23\"", WGS84Format::DEGREES_MINUTES_SECONDS);
        $wgsPos->setLongitudeFromString("E 017º 50' 06.12\"", WGS84Format::DEGREES_MINUTES_SECONDS);

        $rtPos = new SWEREF99Position($wgsPos, SWEREFProjection::SWEREF_99_TM);

        // Conversion values from Lantmateriet.se, they convert from DMS only.
        // Reference: http://www.lantmateriet.se/templates/LMV_Enkelkoordinattransformation.aspx?id=11500
        $xPosFromLM = 6652797.165;
        $yPosFromLM = 658185.201;

        $lat = ((double)round($rtPos->getLatitude() * 1000) / 1000);
        $lon = ((double) round($rtPos->getLongitude() * 1000) / 1000);
        $this->assertEquals($lat, $xPosFromLM);
        $this->assertEquals($lon, $yPosFromLM);

    }

    public function testSwerefToWGS84()
    {
        $swePos = new SWEREF99Position(6652797.165, 658185.201);
        $wgsPos = $swePos->toWGS84();

        // String values from Lantmateriet.se, they convert DMS only.
        // Reference: http://www.lantmateriet.se/templates/LMV_Enkelkoordinattransformation.aspx?id=11500
        $latDmsStringFromLM = "N 59º 58' 55,23001\"";
        $lonDmsStringFromLM = "E 17º 50' 6,11997\"";

        $this->assertEquals($latDmsStringFromLM, $wgsPos->latitudeToString(WGS84Format::DEGREES_MINUTES_SECONDS));
        $this->assertEquals($lonDmsStringFromLM, $wgsPos->longitudeToString(WGS84Format::DEGREES_MINUTES_SECONDS));

    }

    public function testWGS84Parse()
    {
        // Values from Eniro.se
        $wgsPosDM = null;
        $wgsPosDMs = null;
        try {
            $wgsPosDM = new WGS84Position(
                "N 62º 10.560' E 015º 54.180'",
                WGS84Format::DEGREES_MINUTES
            );
            $wgsPosDMs = new WGS84Position(
                "N 62º 10' 33.60\" E 015º 54' 10.80\"",
                WGS84Format::DEGREES_MINUTES_SECONDS
            );
        } catch (ParseException $e) {
            $this->fail($e->getMessage());
        }
        $lat = ((double) round($wgsPosDM->getLatitude() * 1000) / 1000);
        $lon = ((double) round($wgsPosDM->getLongitude() * 1000) / 1000);

        $this->assertEquals(62.176, $lat);
        $this->assertEquals(15.903, $lon);

        $lat_s = ((double) round($wgsPosDMs->getLatitude() * 1000) / 1000);
        $lon_s = ((double) round($wgsPosDMs->getLongitude() * 1000) / 1000);

        $this->assertEquals(62.176, $lat_s);
        $this->assertEquals(15.903, $lon_s);
    }

    public function coordinatesRT90ToWGS84()
    {
        return [
            [6580801.472, 1628658.401, 'N 59º 19\' 42,21012"', 'E 18º 3\' 55,72708"'],
            [6168499, 1321755, 'N 55º 36\' 47,50186"', 'E 12º 58\' 34,42478"'],
            [6581293, 1633716, 'N 59º 19\' 52,43053"', 'E 18º 9\' 16,39123"'],
            [7538550, 1684280, 'N 67º 52\' 52,57728"', 'E 20º 11\' 30,08068"'],
            [6393271.563, 1648713.771, 'N 57º 38\' 23,59005"', 'E 18º 17\' 43,76377"'],
            [6296267.802, 1588822.449, 'N 56º 47\' 5,58435"', 'E 17º 15\' 30,51645"'],
            [6404198, 1272073, 'N 57º 42\' 16,88417"', 'E 11º 58\' 51,38945"'],
        ];
    }
}
