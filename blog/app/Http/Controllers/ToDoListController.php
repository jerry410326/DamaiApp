<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ToDoListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        #print_r("123".$request->input('page'));
        $page = strcmp($request->input('page'),"") != 0 ? intval($request->input('page')) : 1;
        $rows = strcmp($request->input('rows'),"") != 0 ? intval($request->input('rows')) : 20;
        $sort = strcmp($request->input('sort'),"") != 0 ? strval($request->input('sort')) : 'Deadline';
        $order = strcmp($request->input('order'),"") != 0 ? strval($request->input('order')) : 'desc';
        $final = strcmp($request->input('history'),"") != 0 ? strval($request->input('history')) : 'no';
        $offset = ($page-1)*$rows;
        
        $total = DB::table('deadlinelist')->count();
        
        if(strcmp($final, 'no') == 0) {
            $list =  DB::table('deadlinelist')->where('Checked', 0)->orderBy($sort, $order)->skip($offset)->take($rows)->get();
        }else {
            $list =  DB::table('deadlinelist')->orderBy($sort, $order)->skip($offset)->take($rows)->get();
        }
        
        /*$list = array();
        $list[] = [
            "ID"=>"2",
            "CreateDate"=>"2021-01-27",
            "DealDate"=>"2021-01-27",
            "Deadline"=>"2021-02-10",
            "Content"=>$final,
            "CompleteDate"=>"2000-01-01",
            "Checked"=>"0",
            "Del"=>"0"
        ];*/
        
        return response()->json(['rows' => $list, 'total' => $total]);
    }

    public function add_and_edit(Request $request)
    {
        $today = date("Y-m-d");
        
        if (strcmp($request->input('ID'),"-1") == 0) {
            DB::table('deadlinelist')->insert(
                [
                    'CreateDate' => $today,
                    'DealDate' => $today,
                    'Deadline' => $request->input('Deadline'),
                    'Content' => $request->input('Content'),
                    'CompleteDate' => "2000-01-01",
                    'Checked' => 0,
                    'Del' => 0
                ]
            );
        }else {
            $input_id = intval($request->input('ID'));
            DB::table('deadlinelist')->where('ID', $input_id)->update(array('DealDate' => $today, 'Content' => $request->input('Content')));
        }

        return "OK";
    }
    
    public function update(Request $request)
    {
        $today = date("Y-m-d");
        $input_id = intval($request->input('id'));
        DB::table('deadlinelist')->where('ID', $input_id)->update(array('Checked' => 1, 'CompleteDate' => $today));
        echo $input_id;
    }
}
