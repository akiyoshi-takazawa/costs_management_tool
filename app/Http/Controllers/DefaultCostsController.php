<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rules\CostRule;
use Validator;

use Illuminate\Support\Facades\DB;


class DefaultCostsController extends Controller
{
    public function index()
    {
        $data = [];
        if (\Auth::check()){
            
            $id =  \Auth::id();
            
            //デフォルト工数の登録データの取得
            $default_costs = \App\DefaultCost::where('user_id', $id)->get();
            
            // 登録されている案件データの取得
            $propositions = \App\Proposition::where('end_date', '>', date('Y/m/d'))
            ->select('id', 'name', 'input_id')
            ->get();
            
            $default_costs_loop =[
            '' => '選択してください',
            '10' => '10',
            '20' => '20',
            '30' => '30',
            '40' => '40',
            '50' => '50',
            '60' => '60',
            '70' => '70',
            '80' => '80',
            '90' => '90',
            '100' => '100',
            ];
        
            $data = [
                'default_costs' => $default_costs,
                'propositions' => $propositions ,
                'default_costs_loop' => $default_costs_loop,
                ];
        }
        
        // var_dump($data);
        // return;
        
        return view('costs.default_index', compact('data'));
    }
    
    public function store(Request $request)
    {
        //　DBに保存せず送っているデータを確認
        // var_dump($request->all());
        // return;
        
        $validator = Validator::make($request->all(), [
            'cost' => ['required', new CostRule],
            'proposition_id' => 'required',
        ]);
            
        if ($validator->fails()) {
            return redirect('default_costs')->withErrors($validator);
        }
        
        
        // メモ：有田さんコード
        //$input = $request->all();
        // foreach($input['proposition_id'] as $i => $v) {
        //     if ($input['cost'][$i] != null && $input['cost'][$i] > 0) {
        //         $cost_data[] = [
        //             'user_id' => 1,
        //             'proposition_id' => $input['proposition_id'][$i],
        //             'cost' => $input['cost'][$i],
        //         ];
        //     }
        // }
        
        // var_dump($cost_data);
        
        // return;
        
        
        //配列数をカウント
        $array_count = count($request->proposition_id);
            
        // proposition_idの配列を処理
        $proposition_id_data = [];
        
        $proposition_id_data = $request->proposition_id;
        
        //costの配列を処理
        $cost_data = [];
        $cost_data = $request->cost;
        
        // var_dump($cost_data);
        
        // return;
        

        //ユーザーデータ
        $user_id = \Auth::user()->id;
        
        //すべてのデータをまとめる
        $data= [];
        
        for($i = 0; $i < $array_count; $i++){
            if($cost_data[$i]  != null && $cost_data[$i] > 0){
             
            $data[] = array(
            'user_id' => $user_id,   
            'proposition_id' => $proposition_id_data[$i],
            'cost' => $cost_data[$i],
            'created_at' => now(),
            'updated_at' => now()
            );   
            };
        };
        
        // var_dump($data);
        
        // return;
        
        // デフォルト工数登録
        DB::table('default_costs')->insert($data);
         
         return back(); 
         
    }
    
}
