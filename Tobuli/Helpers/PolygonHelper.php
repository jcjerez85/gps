<?php namespace Tobuli\Helpers;
/*
Description: The point-in-polygon algorithm allows you to check if a point is
inside a polygon or outside of it.
Author: Michaël Niessen (2009)
Website: http://AssemblySys.com
 
If you find this script useful, you can show your
appreciation by getting Michaël a cup of coffee ;)
PayPal: michael.niessen@assemblysys.com
 
As long as this notice (including author name and details) is included and
UNALTERED, this code is licensed under the GNU General Public License version 3:
http://www.gnu.org/licenses/gpl.html
*/

class PolygonHelper {
    var $pointOnVertex = true; // Check if the point sits exactly on one of the vertices?

    var $vertices = [];

    var $vertices_count = 0;

    var $bound = [];

    private $center;

    public function __construct($polygon = [])
    {
        foreach ($polygon as $vertex) {
            $point = $this->pointStringToCoordinates($vertex);

            if (empty($this->bound)) {
                $this->bound['top'] = $point;
                $this->bound['bottom'] = $point;
            } else {
                $this->bound['top']['x'] = min($this->bound['top']['x'], $point['x']);
                $this->bound['top']['y'] = max($this->bound['top']['y'], $point['y']);

                $this->bound['bottom']['x'] = max($this->bound['bottom']['x'], $point['x']);
                $this->bound['bottom']['y'] = min($this->bound['bottom']['y'], $point['y']);
            }

            $this->vertices[] = $point;
        }

        $this->vertices_count = count($this->vertices);
    }

    function pointInPolygon($point, $pointOnVertex = false) {
        $this->pointOnVertex = $pointOnVertex;

        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);

        if ( ! $this->boundsCheck($point))
            return false;

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point) == true) {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;

        for ($i=1; $i < $this->vertices_count; $i++)
        {
            $vertex1 = $this->vertices[$i-1];
            $vertex2 = $this->vertices[$i];

            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon. 
        if ($intersections % 2 != 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function boundsCheck($point)
    {
        if (empty($this->bound))
            return false;

        if ($this->bound['top']['x'] > $point['x'])
            return false;

        if ($this->bound['top']['y'] < $point['y'])
            return false;

        if ($this->bound['bottom']['x'] < $point['x'])
            return false;

        if ($this->bound['bottom']['y'] > $point['y'])
            return false;

        return true;
    }

    function pointOnVertex($point) {
        foreach($this->vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }

        return false;
    }

    function pointStringToCoordinates($vertex) {
        return [
            "x" => $vertex['lat'],
            "y" => $vertex['lng']
        ];
    }

    function getCenter()
    {
        if ( ! is_null($this->center))
            return $this->center;

        $cx = 0;
        $cy = 0;
        $area = 0;
        
        for ($vi = 0, $vl = sizeof($this->vertices); $vi < $vl; $vi++) {
            $thisx = $this->vertices[$vi]['x'];
            $thisy = $this->vertices[$vi]['y'];
            $nextx = $this->vertices[($vi + 1) % $vl]['x'];
            $nexty = $this->vertices[($vi + 1) % $vl]['y'];

            $p = ($thisx * $nexty) - ($thisy * $nextx);
            $cx += ($thisx + $nextx) * $p;
            $cy += ($thisy + $nexty) * $p;
            $area += ($thisx * $nexty) - ($thisy * $nextx);
        }

        if (empty($area))
            return null;

        $area = abs(($area / 2));
        $cx = -$cx / (6 * $area);
        $cy = -$cy / (6 * $area);

        $this->center = ['lat' => $cx, 'lng' => $cy];

        return $this->center;
    }

}