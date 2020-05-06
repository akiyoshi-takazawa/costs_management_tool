<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rules\CostRule;
use Validator;

use Illuminate\Support\Facades\DB;

use DateTime;


class CostsController extends Controller
{
    public function index($inputweek = null)
    {
        $data = [];
        // 報告週データの取得
        $weeks = \App\Week::all();
        
        $week_data = [];
        
        $inputweek = $inputweek ?? 0 ;  
        
        $week_data[0] =
          '報告週を選択してください';
         
        
        foreach($weeks as $week){
            $week_data[$week->id] = 
            
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
        }
        
        // コスト選択
        $costs_loop =[
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
            
        // 登録されている案件データの取得(案件名、案件ID)
        $propositions = \App\Proposition::select( 'id', 'name', 'input_id')->get();
        
        //デフォルト工数データの取得 //メモ：ユーザー判別して、ボタンおしたら、切り替え
        $default_costs = \App\DefaultCost::all();
        
        $data = [
            'default_costs' => $default_costs,
            'propositions' => $propositions ,
            'week_data' => $week_data,
            'costs_loop' => $costs_loop,
            'inputweek' => $inputweek,
            ];
        
        return view('costs.index', compact('data'));
    }
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cost' => ['required', new CostRule],
            'proposition_id' => 'required',
            'week_id' => ['required','integer','min:1'],
        ]);
            
        if ($validator->fails()) {
            return redirect('costs')->withErrors($validator);
        }
        
        // データの配列を整える
        $input = $request->all();
        foreach($input['proposition_id'] as $i => $v) {
            if ($input['cost'][$i] != null && $input['cost'][$i] > 0) {
                $cost_data[] = [
                    'user_id' => \Auth::user()->id,
                    'proposition_id' => $input['proposition_id'][$i],
                    'cost' => $input['cost'][$i],
                    'week_id' =>  (int)$input['week_id'],
                    'status' => $input['status'],
                    'comment' => $input['comment'],
                    'submit_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // var_dump($cost_data);
        
        // return;
        
        // デフォルト工数登録
        DB::table('costs')->insert($cost_data);
         
         return back(); 
         
    }
    
    public function report_index()
    {
        //承認者の場合
        if(\Auth::user()->user_type = 2){
            
            $data = [];
            
            
            // 1.工数データの取得
            $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status');
            
            // 2.1に案件データを追加
            $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.input_id','propositions.name', 'propositions.authorizer_user_id');
            
            // 3. 2に報告週データを追加
            $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
            
            // 取得
            $cost = $cost_sc->get();
            
            $weeks = \App\Week::all();
            
            $inputweek = 0;
            
            $week_data[0] =
            '全て';
            
            foreach($weeks as $week){
            $week_data[$week->id] = 
            
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            }
            
            $data = [
                'cost' => $cost,
                'week_data' => $week_data,
                'inputweek' => $inputweek,
                ];
            
            //  var_dump($data['week_data']);
            //  return;
            
            
            return view(('report.auth_index') , compact('data'));
            
        }else{
            return redirect('/');
        }
    }
    
    public function search(Request $request)
    {
        
        $data = [];
            
        // 1.工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status');
            
        // 2.1に案件データを追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.input_id','propositions.name', 'propositions.authorizer_user_id');
            
        // 3. 2に報告週データを追加
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
            
        $weeks = \App\Week::all();
        $inputweek = 0;
        $week_data[0] ='全て';
            
        foreach($weeks as $week){
        $week_data[$week->id] = 
        $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
        }
            
        // 検索ワード
        $search_name = $request->name;
        // 検索報告週
        $search_week = $request->week;
        
        // 検索ワード 検索場合
        if($request->name != null ){
            
            $cost_sc->where('name', 'LIKE', "%$search_name%");
                
        }
            
        //報告週 検索
        if($request->week != null && $request->week > 0){
            
            $cost_sc->where('week_id' , $search_week);
            
        }
            
        $inputweek = $search_week;
        
        $cost = $cost_sc->get();
        
        $data = [
                'cost' => $cost,
                'week_data' => $week_data,
                'inputweek' => $inputweek,
                ];
       
       //承認者の場合
        if(\Auth::user()->user_type == 2){         
            return view(('report.auth_index') , compact('data'));
        }else{
            return view(('report.index') , compact('data'));
        } 
        
        
    }
    // 集計データ一覧(承認者) $idは案件id
    public function report_show($id)
    {

        $data = [];
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost')
                    ->where('costs.proposition_id', $id);
                    
        // その案件を登録しているユーザー情報追加
        $cost_sc->join('users', 'users.id', '=', 'costs.user_id')->addSelect('users.name');
        
        // 報告週データを追加
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date')
                ->where('weeks.year', date("Y"))->where('weeks.month', date("n"));
        // 取得
        $costs = $cost_sc->get();
        
        // weeksテーブルから稼働日を取得
        $weeks = \App\Week::where('year', date("Y"))
                            ->where('month', date("n"))->get();
        
        // 稼働日
        $interval = [];
        $total_day = [];
        $total_sumday = 0;
        
        // weeksを週ごとの配列にする
        foreach($weeks as $week){
            $startday[$week->week] = new DateTime(date($week->start_date));
            $endday[$week->week] = new DateTime(date($week->end_date));
            
            $interval[$week->week] = $endday[$week->week]->diff($startday[$week->week]);
            
            $total_day[$week->week] = $interval[$week->week]->format('%a') + 1;
            
            
            $total_sumday += $total_day[$week->week];
        }
        
        
        // テーブル表示用
        $user_data = [];
        $cost_data = [];
        $total_cost = 0;
        $user_total_cost = [];
        $user_cost = [];
        $week_total_cost = [];
        $user_sum = [];
        
        foreach($costs as $cost){
            $user_data[$cost->user_id] = $cost;
            $cost_data[$cost->week][$cost->user_id] = $cost;
            
            // ユーザーごとの合計
            if (!isset($user_total_cost[$cost->user_id])) {
                $user_total_cost[$cost->user_id] = 0;
            }
            
            $user_total_cost[$cost->user_id] += $cost->cost;
            
            // 週ごとの合計
            if (!isset($week_total_cost[$cost->week])) {
                $week_total_cost[$cost->week] = 0;
            }
            // 週ごとの人月を出し、フォーマットを変更
            $week_total_cost[$cost->week] += $cost->cost / 100;
            $week_total_cost[$cost->week] = number_format($week_total_cost[$cost->week], 2, '.', '');
            
            // 1ユーザーごとの月合計
            $user_cost[$cost->week][$cost->user_id] = $cost->cost * $total_day[$cost->week] / $total_sumday;
            
            if (!isset($user_sum[$cost->user_id])) {
                $user_sum[$cost->user_id] = 0;
            }
            
            $user_sum[$cost->user_id] += $user_cost[$cost->week][$cost->user_id];
            
            //月全体の合計工数(人月)の計算
            $total_cost += $week_total_cost[$cost->week] * $total_day[$cost->week] / $total_sumday ;
            $total_cost = number_format($total_cost, 2, '.', '');
            
        }
        
        // var_dump($user_cost);
        // return;
        

        //案件データ
        $proposition = \App\Proposition::find($id);
        //今のyearデータ
        $year = date("Y");
        //今のmonthデータ
        $month = date("n");
        
        // 送るデータ
        $data = [
                'cost_data' => $cost_data,
                'user_data' => $user_data,
                'proposition' => $proposition,
                'year' => $year,
                'month' => $month,
                'week_total_cost' => $week_total_cost,
                'user_sum' => $user_sum,
                'total_cost' => $total_cost
                ];
            
        return view(('report.auth_show') , compact('data')); 
            
    }
    
    public function report_search(Request $request)
    {
        $data = [];
        
        // 検索ワード
        $year = $request->year;
        // 検索報告週
        $month = $request->month;
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost')
                    ->where('costs.proposition_id', $request->id);
        // その案件を登録しているユーザー情報追加
        $cost_sc->join('users', 'users.id', '=', 'costs.user_id')->addSelect('users.name');
        // 報告週データを追加
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date')
                ->where('weeks.year', $year)->where('weeks.month', $month);
        // 取得
        $costs = $cost_sc->get();
        
        //案件データ
        $proposition = \App\Proposition::find($request->id);
        
        // weeksテーブルから稼働日を取得
        $weeks = \App\Week::where('year', date("Y"))
                            ->where('month', date("n"))->get();
        
        // ここからユーザー別、週別、月合計の計算含む配列
        // 稼働日
        $interval = [];
        $total_day = [];
        $total_sumday = 0;
        
        
        
        // weeksを週ごとの配列にする
        foreach($weeks as $week){
            $startday[$week->week] = new DateTime(date($week->start_date));
            $endday[$week->week] = new DateTime(date($week->end_date));
            
            $interval[$week->week] = $endday[$week->week]->diff($startday[$week->week]);
            
            $total_day[$week->week] = $interval[$week->week]->format('%a') + 1;
            
            
            $total_sumday += $total_day[$week->week];
        }
        
        
        // テーブル表示用
        $user_data = [];
        $cost_data = [];
        $total_cost = 0;
        $user_total_cost = [];
        $user_cost = [];
        $week_total_cost = [];
        $user_sum = [];
        
        foreach($costs as $cost){
            $user_data[$cost->user_id] = $cost;
            $cost_data[$cost->week][$cost->user_id] = $cost;
            
            // ユーザーごとの合計
            if (!isset($user_total_cost[$cost->user_id])) {
                $user_total_cost[$cost->user_id] = 0;
            }
            
            $user_total_cost[$cost->user_id] += $cost->cost;
            
            // 週ごとの合計
            if (!isset($week_total_cost[$cost->week])) {
                $week_total_cost[$cost->week] = 0;
            }
            // 週ごとの人月を出し、フォーマットを変更
            $week_total_cost[$cost->week] += $cost->cost / 100;
            $week_total_cost[$cost->week] = number_format($week_total_cost[$cost->week], 2, '.', '');
            
            // 1ユーザーごとの月合計
            $user_cost[$cost->week][$cost->user_id] = $cost->cost * $total_day[$cost->week] / $total_sumday ;
            
            if (!isset($user_sum[$cost->user_id])) {
                $user_sum[$cost->user_id] = 0;
            }
            
            $user_sum[$cost->user_id] += $user_cost[$cost->week][$cost->user_id];
            
            //月全体の合計工数(人月)の計算
            $total_cost += $week_total_cost[$cost->week] * $total_day[$cost->week] / $total_sumday ;
            $total_cost = number_format($total_cost, 2, '.', '');
            
        }
        
        $data = [
                'cost_data' => $cost_data,
                'user_data' => $user_data,
                'proposition' => $proposition,
                'year' => $year,
                'month' => $month,
                'week_total_cost' => $week_total_cost,
                'user_sum' => $user_sum,
                'total_cost' => $total_cost
                ];
                
        return view(('report.auth_show') , compact('data'));
        
    } 
    
    public function report_cost(Request $request)
    {
        // var_dump($cost_sc);
        // return;     
        $data = [];
        $id = $request->id;
        $week_id = $request->week_id;
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment')
                    ->where('costs.proposition_id', $id)->where('costs.week_id', $week_id);
        // その案件を登録しているユーザー情報追加
        $cost_sc->join('users', 'users.id', '=', 'costs.user_id')->addSelect('users.name');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // 取得
        $costs = $cost_sc->get();
        
        
        
        // 案件データ
        $proposition = \App\Proposition::find($request->id);   
        
        // 報告週検索データの抽出
        $weeks = \App\Week::all();
        $week_data = [];
            
        foreach($weeks as $week){
            $week_data[$week->id] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            }
        
        // var_dump($week_value);
        // return;
        
        $user_data = [];
        
        foreach($costs as $cost){
            $user_data[$cost->user_id] = $cost;
        }
        
        
        $data = [
                'user_data' => $user_data,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'week_id' => $week_id,
                ];
                
        return view(('report.auth_cost') , compact('data'));
        
    } 
    public function cost_search(Request $request)
    {
        
        $data = [];
        $id = $request->id;
        $week_id = $request->week_id;
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment')
                    ->where('costs.proposition_id', $id)->where('costs.week_id', $week_id);
        // その案件を登録しているユーザー情報追加
        $cost_sc->join('users', 'users.id', '=', 'costs.user_id')->addSelect('users.name');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // 取得
        $costs = $cost_sc->get();
    
        // 案件データ
        $proposition = \App\Proposition::find($request->id);   
        
        // 報告週検索データの抽出
        $weeks = \App\Week::all();
        
        $week_data = [];
            
        foreach($weeks as $data){
            $week_data[$data->id] = 
            $data->year . '年'. $data->month . '月' . $data->week . 'W'. ' '. date("n/d", strtotime($data->start_date)) .'~'. date("n/d", strtotime($data->end_date));
            }
        
        // var_dump($week_value);
        // return;
        
        $user_data = [];
        
        foreach($costs as $cost){
            $user_data[$cost->user_id] = $cost;
        }
        
        $data = [
                'user_data' => $user_data,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'week_id' => $week_id,
                ];
        
                
        return view(('report.auth_cost') , compact('data'));
    } 
    //承認者＆従業員 承認 or 否認、提出ボタンの処理
    public function cost_update(Request $request)
    {   
        //バリデーション
        if(\Auth::user()->user_type == 1){
            $validator = Validator::make($request->all(), [
            'id' => 'required',
            'cost' => 'required', //new CostRule]複数バリデーション,
            'comment' => ['required','max:200'],
        ]);
        
            if ($validator->fails()) {
            return back()->withErrors($validator);
        }   
            
        }
        
        // var_dump($request->all());
        // return;
        
        
        $id = $request->id;
        $status = $request->status;
        $approval = '承認';
        
        $cost = \App\Cost::find($id);
        
        // var_dump($cost);
        // return;
        
        $cost->comment = $request->comment;
        $cost->auth_comment = $request->auth_comment;
        $cost->status = $status;
        
        // 承認ステータス処理 1=提出済 2=承認 3=否認
        if(\Auth::user()->user_type == 2){
        if($status == $approval){
            $cost->status = 2 ;
        }else{
            $cost->status = 3 ;
        }
        }
        
        $cost->save();
    
        return back();
    } 
    // 報告データ一覧(従業員)
    public function employee_index()
    {
        //従業員の場合
        if(\Auth::user()->user_type == 1){
        
        $user_id = \Auth::user()->id;   
            
        $data = [];
        
        $start = 0;
        $end = 0;
            
        // 1.工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status')
                        ->where('costs.user_id', $user_id);
        // 2.1に案件データを追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.input_id','propositions.name', 'propositions.authorizer_user_id');
        // 3. 2に報告週データを追加
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date')->whereNotBetween('id', [$start, $end]);
        // 取得
        $cost = $cost_sc->get();
        
        $weeks = \App\Week::all();
            
        $inputweek = 0;
            
        $week_data[0] ='全て';
            
        foreach($weeks as $week){
        $week_data[$week->id] = 
            
        $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
        }
            
        $data = [
            'cost' => $cost,
            'week_data' => $week_data,
            'inputweek' => $inputweek,
            ];
            
        return view(('report.index') , compact('data'));
            
        }else{
            return redirect('/');
        }
        
        
    }
    // 工数詳細確認(従業員)
    public function employee_cost(Request $request)
    {
        
        $data = [];
        $id = $request->id;
        $week_id = $request->week_id;
        
        $user_id = \Auth::user()->id;
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment', 'costs.auth_comment')
                    ->where('costs.week_id', $week_id)->where('costs.user_id', $user_id);
        // その案件を登録している案件情報追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.name', 'propositions.input_id');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // 取得
        $costs = $cost_sc->get();
        
        // 案件データ
        $proposition = \App\Proposition::find($request->id);   
        
        // 報告週検索データの抽出
        $weeks = \App\Week::all();
        $week_data = [];
            
        foreach($weeks as $week){
            $week_data[$week->id] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            }
        
        $user_data = [];
        
        foreach($costs as $cost){
            $user_data[$cost->proposition_id] = $cost;
        }
        
        $default_costs_loop =[
            '' => '再提出時に選択',
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
                'user_data' => $user_data,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'week_id' => $week_id,
                'default_costs_loop' => $default_costs_loop,
                ];
                
        return view(('report.cost') , compact('data'));
        
    }
    // 工数詳細画面　検索(従業員)
    public function employee_cost_search(Request $request)
    {
        $data = [];
        $week_id = $request->week_id;
        
        $user_id = \Auth::user()->id;
        
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment', 'costs.auth_comment')
                    ->where('costs.week_id', $week_id)->where('costs.user_id', $user_id);
        // その案件を登録している案件情報追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.name', 'propositions.input_id');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // 取得
        $costs = $cost_sc->get();
        
        // 案件データ
        $proposition = \App\Proposition::find($request->id);
    
        
        // 報告週検索データの抽出
        $weeks = \App\Week::all();
        $week_data = [];
            
        foreach($weeks as $week){
            $week_data[$week->id] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            }
        
        $user_data = [];
        
        foreach($costs as $cost){
            $user_data[$cost->proposition_id] = $cost;
        }
        
        $default_costs_loop =[
            '' => '再提出時に選択',
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
                'user_data' => $user_data,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'week_id' => $week_id,
                'default_costs_loop' => $default_costs_loop,
                ];
                
        return view(('report.cost') , compact('data'));
        
    }
    
    // 案件工数確認ページ
    public function proposition_cost(Request $request)
    {   
        $data = [];
        $proposition_id = $request->id;
        
        $start_select = 0;
        $end_select = 0;
        
        // メモ：データをすべて表示ではなく、時間あれば、最初は表示なしにする
        
        $user_id = \Auth::user()->id;
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment', 'costs.auth_comment')
                    ->where('costs.proposition_id', $proposition_id)->where('costs.user_id', $user_id);
        // その案件を登録している案件情報追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.name', 'propositions.input_id');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // 取得
        $costs = $cost_sc->oldest('start_date')->get();
        
        // 案件データ
        $proposition = \App\Proposition::find($request->id);  
        
        // 期間選択データ
        $weeks = \App\Week::oldest('start_date')->get();
        
        //  var_dump($weeks);
        //  return;
        
        $week_data = [];
        $week_data[0] =
          '報告週を選択してください';
            
        foreach($weeks as $week){
            $week_data[$week->start_date] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            }
        
        // 指定した週の工数表示検索
        
        // 送られてきたデータ
        // start(開始週)で指定されたweek_id->そのstart_dateを取得
        // end(終了週)で指定されたweek_id->そのend_dateを取得
        
        // week_idからcostデータを取得＆ユーザー絞り込みを行う
        // week_idからweeksデータを取得
        // ->whereBetweenでstart endでの週を検索
        // ->oldest()で古い週から表示させる
    
        

        $data = [
                'costs' => $costs,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'start_select' => $start_select,
                'end_select' => $end_select,
                ];
                
        return view(('proposition.cost') , compact('data'));
        
    }
    // 案件工数確認ページ検査
    public function proposition_cost_search(Request $request)
    {   
        $data = [];
        $proposition_id = $request->id;
        $user_id = \Auth::user()->id;
        
        $start_select = $request->start;
        $end_select = $request->end;
        
        // 指定した週の工数表示検索
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment', 'costs.auth_comment')
                    ->where('costs.proposition_id', $proposition_id)->where('costs.user_id', $user_id);
        // その案件を登録している案件情報追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.name', 'propositions.input_id');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // start_dateの中から指定した範囲のデータを取得
        $costs = $cost_sc->whereBetween('weeks.start_date', [$start_select, $end_select])->oldest('start_date')->get();
        
        // 案件データ
        $proposition = \App\Proposition::find($request->id);  
        
        // 期間選択データ
        $weeks = \App\Week::oldest('start_date')->get();
        
        //  var_dump($costs);
        //  return;
        
        $week_data = [];
        $week_data[0] =
          '報告週を選択してください';
            
        foreach($weeks as $week){
            $week_data[$week->start_date] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            }
    
        $data = [
                'costs' => $costs,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'start_select' => $start_select,
                'end_select' => $end_select,
                ];
                
        return view(('proposition.cost') , compact('data'));
        
    }
}
