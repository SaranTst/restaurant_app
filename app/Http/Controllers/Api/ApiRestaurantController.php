<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Http\Request;
use \Cache;

class ApiRestaurantController extends Controller
{
    //
    public function getCsrf() {

        $dataJson = [
            'csrf_token' => csrf_token()
        ];

        return response()->json($dataJson);
    }

    public function getCache() {

        $dataJson = [ // set message json
            'error' => 1,
            'msg' => "Don't Have Data In File Cache.",
            'results' => []
        ];

        $getDataCache = Cache::get('restaurant');
        if($getDataCache) {

            $dataJson['error'] = 0;
            $dataJson['msg'] = "Success.";
            $dataJson['results'] = $getDataCache;
        }

        return response()->json($dataJson);
    }

    public function searchRestaurants(Request $request) {

        $dataJson = [ // set message json
            'error' => 1,
            'msg' => 'ไม่พบร้านอาหาร.',
            'results' => []
        ];

        $search = $request->input('search') ? str_replace(' ', '', trim($request->input('search'))) : 'Bangsue';

        // get data cache
        $dataCache = Cache::get('restaurant');
        if (!empty($dataCache[$search])) {

            $dataJson['error'] = 0;
            $dataJson['msg'] = "Success.";
            $dataJson['results'] = $dataCache[$search];
            goto error;

        } else {

            // curl get data
            $resCurlGoogleApi = json_decode($this->curlGoogleApi($search), true);

            // check status curl
            if ($resCurlGoogleApi['status'] !== 'OK') {
                $dataJson['results'] = $resCurlGoogleApi['results'];
                goto error;
            }

            $dataCache[$search] = $resCurlGoogleApi['results']; // push new data
            Cache::put('restaurant', $dataCache); // save data *no send time cache will be stored indefinitely

            $dataJson['error'] = 0;
            $dataJson['msg'] = "Success.";
            $dataJson['results'] = $resCurlGoogleApi['results'];
            goto error;
        }

        error:
        return response()->json($dataJson);
    }

    public function curlGoogleApi($search) {

        // set param + filter
        $searchTxt = "restaurants+in+" . $search;
        $apiKey = "AIzaSyB38R7yhcuBx3atqooETmk81J4JvQvlql8";
        $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".$searchTxt."&key=".$apiKey;


        // curl get data
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
