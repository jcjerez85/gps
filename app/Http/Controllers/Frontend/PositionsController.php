<?php namespace App\Http\Controllers\Frontend;

use App\Console\PositionsStack;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class PositionsController extends Controller {
    public function insert() {
        $input = Request::all();

        $error = null;
        $required = ['uniqueId' => '', 'fixTime' => '', 'latitude' => '', 'longitude' => '', 'speed' => '', 'altitude' => '', 'course' => '', 'protocol' => ''];

        foreach ($required as $field => $value) {
            if (!isset($input[$field]))
                $error .= $field.', ';
        }

        if (!is_null($error))
            return Response::make(json_encode(['status' => 0, 'message' => 'Missing params: '.substr($error, 0, -2)]), 400);

        $data = [
            'fixTime'    => strtotime($input['date']) * 1000,
            'valid'      => $input['valid'],
            'imei'       => $input['uniqueId'],
            'latitude'   => $input['latitude'],
            'longitude'  => $input['longitude'],
            'attributes' => empty($input['attributes']) ? [] : $input['attributes'],
            'speed'      => $input['speed'],
            'altitude'   => $input['altitude'],
            'course'     => $input['course'],
            'protocol'   => $input['protocol'],
        ];

        (new PositionsStack())->add($data);
    }
}
