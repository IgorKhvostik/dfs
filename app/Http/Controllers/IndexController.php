<?php

namespace App\Http\Controllers;

use App\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use RestClient;
use RestClientException;

class IndexController extends Controller
{
    public function index()
    {
        require('RestClient.php');
        try {
            //Instead of 'login' and 'password' use your credentials from https://my.dataforseo.com/login
            $client = new RestClient('https://api.dataforseo.com/', null, env('API_LOGIN'), env('API_PASSWORD'));

            $se_get_result = $client->get('v2/cmn_se/NL');

            //delete unnecessary elements from sub-arrays
            foreach ($se_get_result['results'] as $k => $smallArr) {
                foreach ($smallArr as $key => $item) {
                    if ($key == 'se_id' || $key == 'se_name') {
                        $arr[$k][$key] = $item;
                    };
                }
            }

            //Making search engines look like: 'id'=>'name'
            $searchEngines = array();
            foreach ($arr as $item) {
                if (!in_array($item['se_name'], $searchEngines)) {
                    $searchEngines[$item['se_id']] = $item['se_name'];
                }
            }

            $client = new RestClient('https://api.dataforseo.com/', null, env('API_LOGIN'), env('API_PASSWORD'));
            $se_get_result = $client->get('v2/cmn_locations/NL');

            //Alternate variant to sort the list of locations
            /*for ($i = 0; $i < count($se_get_result['results']); $i++) {
                if ($se_get_result['results'][$i]['loc_type'] == 'Municipality') {
                    $municipalities[$se_get_result['results'][$i]['loc_id']] = $se_get_result['results'][$i]['loc_name'];
                }
            }*/

            $se_get_result = array_slice($se_get_result['results'], 0, 50);
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
            'locations' => $se_get_result,
            'searchEngines' => $searchEngines
        ]);

    }

    public function save(Request $request)
    {
        $request->validate([
            'website' => 'string',
            'keywords' => 'string'
        ]);

        require('RestClient.php');

        try {
            //Instead of 'login' and 'password' use your credentials from https://my.dataforseo.com/login
            $client = new RestClient('https://api.dataforseo.com/', null, env('API_LOGIN'), env('API_PASSWORD'));
        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
            exit();
        }


        $post_array = array();
        $my_unq_id = mt_rand(0, 30000000); //your unique ID. will be returned with all results
        $post_array[$my_unq_id] = array(
            "priority" => 2,
            "site" => $request->website,
            "se_name" => $request->searchEng,
            "se_language" => "English",
            "loc_id" => $request->location,
            "key" => mb_convert_encoding($request->keywords, "UTF-8")
        );

        try {
            // POST /v2/rnk_tasks_post/$data
            // $tasks_data must be array with key 'data'
            $task_post_result = $client->post('v2/rnk_tasks_post', array('data' => $post_array));
            $query = new Query($request->all());
            $query->taskId = $task_post_result['results'][$my_unq_id]['task_id'];
            $query->save();

        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
        }

        return redirect()->route('result');
    }

    public function result()
    {
        $data = Query::paginate(10);
        return view('result')->with([
            'data' => $data
        ]);
    }

    public function check(Request $request)
    {
        require('RestClient.php');
        try {
            $client = new RestClient('https://api.dataforseo.com', null, env('API_LOGIN'), env('API_PASSWORD'));
        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
        }
        try {
            $task_get_result = $client->get('v2/rnk_tasks_get/' . $request->taskId);
            if ($task_get_result['results_count'] == 0) {
                return "Task is still in progress, be patient :)";
            } elseif ($task_get_result['results']['organic'][0]['result_position'] == null) {
                return "Service error. Please, try again or write to \"Support\"";
            } else {
                $row = Query::where('taskId', $request->taskId)->first();
                $row->position = $task_get_result['results']['organic'][0]['result_position'];
                $row->save();
            }

            return $row->position;

        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
        }

        $client = null;
    }
    public function checkAll()
    {
        require('RestClient.php');
        try {
            $client = new RestClient('https://api.dataforseo.com', null, env('API_LOGIN'), env('API_PASSWORD'));
        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
        }
        try {
            $task_get_result = $client->get('v2/rnk_tasks_get');
            //dd($task_get_result);
            if ($task_get_result['results_count'] !== 0){
                foreach ($task_get_result['results']['organic'] as $item){
                    $row = Query::where('taskId', $item['task_id'])->first();
                    if($row){
                        $row->position = $item['result_position'];
                        $row->save();
                    }
                }
            }

        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
        }

        $client = null;
        return redirect()->route('result');
    }

}
