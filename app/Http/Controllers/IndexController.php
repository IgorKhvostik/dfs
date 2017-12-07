<?php

namespace App\Http\Controllers;

use App\Query;
use Illuminate\Http\Request;
use RestClient;
use RestClientException;

class IndexController extends Controller
{
    public function index()
    {

        require('RestClient.php');
        try {
            //Instead of 'login' and 'password' use your credentials from https://my.dataforseo.com/login
            $client = new RestClient('https://api.dataforseo.com/', null, 'challenger17@rankactive.info', 'LEVgtxFTGmlmSLT9');

            $se_get_result = $client->get('v2/cmn_se/GB');
            foreach ($se_get_result['results'] as $k => $smallArr) {
                foreach ($smallArr as $key => $item) {
                    if ($key == 'se_id' || $key == 'se_name') {
                        $arr[$k][$key] = $item;
                    };
                }
            }

            $searchEngines = array();
            foreach ($arr as $item) {
                if (!in_array($item['se_name'], $searchEngines)) {
                    $searchEngines[$item['se_id']] = $item['se_name'];
                }
            }


            $client = new RestClient('https://api.dataforseo.com/', null, 'challenger17@rankactive.info', 'LEVgtxFTGmlmSLT9');

            $se_get_result = $client->get('v2/cmn_locations/GB');
            $se_get_result = array_slice($se_get_result['results'], 0, 20);
            $locations = array();
            foreach ($se_get_result as $k => $val)
                foreach ($val as $key => $item) {
                    if ($key == 'loc_id' || $key == 'loc_name') {
                        $locations[$k][$key] = $item;
                    };
                }

        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
            exit();
        }
        $client = null;

        return view('index')->with([
            'locations' => $locations,
            'searchEngines' => $searchEngines
        ]);

    }

    public function save(Request $request)
    {
        $request->validate([
            'website' => 'url',
            'keywords' => 'string'
        ]);
        $query = new Query($request->all());
        $query->save();
    }
}
