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
        
        $user_id = \Auth::user()->id;
        
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
        
        
        if($inputweek !== null){
            $week_sc =\App\Week::select('id', 'year', 'month', 'week', 'start_date', 'end_date')
                            ->where('id', $inputweek)->get(); 
            
            $week_start_data = [];
            $week_end_data = [];
            
            foreach($week_sc as $_week){
            $week_start_data[$_week->id] = $_week->start_date;
            $week_end_data[$_week->id] = $_week->end_date;
            }
            
            // 報告週の間に案件データのstart_dateがある案件の取得(案件名、案件ID)
            // $proposition_sc = \App\Proposition::select('propositions.id', 'propositions.name', 'propositions.input_id', 'propositions.start_date', 'propositions.end_date')
            //             ->where(['start_date', '<=', $week_start_data[$inputweek]],['end_date', '>=', $week_end_data[$inputweek]]);
        
            // $propositions = $proposition_sc->get();
        
        }else{
            
        }
        
        $propositions = \App\Proposition::all();

        // すべての工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status')
                    ->where('costs.user_id', $user_id)
                    ->where('costs.week_id', $inputweek);
        
        $costs = $cost_sc->get();
        
        $cost_data = [];
        
        foreach($costs as $cost){
            $cost_data[$cost->proposition_id] = $cost->cost;
        }
        
        
        //デフォルト工数データの取得 //メモ：ユーザー判別して、ボタンおしたら、切り替え
        // $default_costs = \App\DefaultCost::all();
        
        $data = [
            // 'default_costs' => $default_costs,
            'propositions' => $propositions ,
            'week_data' => $week_data,
            'costs_loop' => $costs_loop,
            'inputweek' => $inputweek,
            'cost_data' => $cost_data,
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
    // 報告データ一覧(承認者)
    public function report_index()
    {
        //承認者の場合
        if(\Auth::user()->user_type = 2){
            
            $data = [];
            
            $user_id = \Auth::user()->id;
            
            // 1.すべての工数データの取得
            $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status');
            
            // 2、ログインしている承認者に紐づく案件データを追加
            $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')
                    ->addSelect('propositions.input_id','propositions.name', 'propositions.authorizer_user_id')
                    ->where('authorizer_user_id', $user_id);
            
            // 3. 2に報告週データを追加
            $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')
                    ->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
            
            // 取得
            $costs = $cost_sc->get();
            
            $weeks = \App\Week::all();
            $inputweek = 0;
            $week_data[0] =
            '全て';
            // 稼働日
            $interval = [];
            $total_day = [];
            
            foreach($weeks as $week){
            //検索用の報告週
            $week_data[$week->id] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            
            //稼働日計算
            $startday[$week->week] = new DateTime(date($week->start_date));
            $endday[$week->week] = new DateTime(date($week->end_date));
            $interval[$week->week] = $endday[$week->week]->diff($startday[$week->week]);
            
            //稼働日
            $total_day[$week->week] = $interval[$week->week]->format('%a') + 1;    
            }
            
            // 案件とweekごとのコストを計算
            // テーブル表示用
            $week_total_cost = [];
            $proposition_data = [];
            
            foreach($costs as $cost){
                
                $proposition_data[$cost->proposition_id."_".$cost->week_id] = $cost;
                
                // week_idごとのcost合計
                if (!isset($week_total_cost[$cost->proposition_id][$cost->week_id])) {
                    $week_total_cost[$cost->proposition_id][$cost->week_id] = 0;
                }
                
                // week_idごとの人月を出し、フォーマットを変更
                $week_total_cost[$cost->proposition_id][$cost->week_id] += $cost->cost / 100;
                $week_total_cost[$cost->proposition_id][$cost->week_id] = number_format($week_total_cost[$cost->proposition_id][$cost->week_id], 2, '.', '');
                
            }
            
            // var_dump($week_total_cost);
            // return;
            
            $data = [
                'costs' => $costs,
                'week_total_cost' => $week_total_cost,
                'proposition_data' => $proposition_data,
                'week_data' => $week_data,
                'inputweek' => $inputweek,
                ];
            
            return view(('report.auth_index') , compact('data'));
            
        }else{
            return redirect('/');
        }
    }
    
    public function search(Request $request)
    {
        
        $data = [];
        
        $user_id = \Auth::user()->id;
            
        // 1.工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status');
            
        // 2.1に案件データを追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')
                ->addSelect('propositions.input_id','propositions.name', 'propositions.authorizer_user_id')
                ->where('authorizer_user_id', $user_id);
            
        // 3. 2に報告週データを追加
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
            
        $weeks = \App\Week::all();
            $inputweek = 0;
            $week_data[0] =
            '全て';
            // 稼働日
            $interval = [];
            $total_day = [];
            $total_sumday = 0;
            
            foreach($weeks as $week){
            //検索用の報告週
            $week_data[$week->id] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            
            //稼働日計算
            $startday[$week->week] = new DateTime(date($week->start_date));
            $endday[$week->week] = new DateTime(date($week->end_date));
            $interval[$week->week] = $endday[$week->week]->diff($startday[$week->week]);
            
            //稼働日
            $total_day[$week->week] = $interval[$week->week]->format('%a') + 1;    
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
        
        $costs = $cost_sc->get();
        
        
        // 案件とweekごとのコストを計算
            // テーブル表示用
            $week_total_cost = [];
            $proposition_data = [];
            
            foreach($costs as $cost){
                
                $proposition_data[$cost->proposition_id."_".$cost->week_id] =$cost;
                
                // week_idごとのcost合計
                if (!isset($week_total_cost[$cost->proposition_id][$cost->week_id])) {
                    $week_total_cost[$cost->proposition_id][$cost->week_id] = 0;
                }
                
                // week_idごとの人月を出し、フォーマットを変更
                $week_total_cost[$cost->proposition_id][$cost->week_id] += $cost->cost / 100;
                $week_total_cost[$cost->proposition_id][$cost->week_id] = number_format($week_total_cost[$cost->proposition_id][$cost->week], 2, '.', '');
                
                
            }
        
        $data = [
                'costs' => $costs,
                'week_total_cost' => $week_total_cost,
                'proposition_data' => $proposition_data,
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
        
            
        // 1.工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status')
                        ->where('costs.user_id', $user_id);
        // 2.1に案件データを追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.input_id','propositions.name', 'propositions.authorizer_user_id');
        // 3. 2に報告週データを追加
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')
        ->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
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
        $user_id = \Auth::user()->id;
        
        
        // 期間選択データ
        $weeks = \App\Week::oldest('start_date')->get();
        
        // 週選択時の表示処理
        $week_data = [];
        
        $week_data[0] =
          '選択してください';
          // 週別の工数合計
          
        $week_enddata = [];
        $week_enddata[0] =
          '選択してください';
         
          
        $interval = [];
        $total_day = [];
        $total_sumday = 0;
            
        foreach($weeks as $week){
            $week_data[$week->start_date] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            
            $week_enddata[$week->end_date] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            
            
            $startday[$week->id] = new DateTime(date($week->start_date));
            $endday[$week->id] = new DateTime(date($week->end_date));
            
            $interval[$week->id] = $endday[$week->id]->diff($startday[$week->id]);
            
            $total_day[$week->id] = $interval[$week->id]->format('%a') + 1;
            
        }
        // //もし報告週が登録されていない場合、リダイレクトさせる
        // if($week_data == NULL){
            
        // }
        
        //デフォルトで当月を選択処理
        $start_select = date("Y-m-01 00:00:00");
        $end_select = date("Y-m-t 00:00:00");
        
        // 当月(月初）を登録していない場合
        if($week_data[$start_select] == null ){
            $start_select = 0;
            $end_select = 0;
        }
        
        
        
        // 取得した案件idから特定の案件工数データの取得
        $cost_sc = \App\Cost::select('costs.id', 'costs.user_id','costs.week_id', 'costs.proposition_id', 'costs.cost', 'costs.submit_at', 'costs.status', 'costs.comment', 'costs.auth_comment')
                    ->where('costs.proposition_id', $proposition_id)->where('costs.user_id', $user_id);
        // その案件を登録している案件情報追加
        $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')->addSelect('propositions.name', 'propositions.input_id');
        // 取得した報告週からデータを追加・絞り込み
        $cost_sc->join('weeks', 'weeks.id', '=', 'costs.week_id')->addSelect('weeks.year', 'weeks.month', 'weeks.week', 'weeks.start_date', 'weeks.end_date');
        // 取得
        $costs = $cost_sc->whereBetween('weeks.start_date', [$start_select, $end_select])->oldest('start_date')->get();
        
        // 案件データ
        $proposition = \App\Proposition::find($request->id);  
        
        
        // テーブル表示用
        $cost_data = [];
        $total_cost = 0;
        $user_cost = [];
        $week_total_cost = [];
        $user_sum = [];
        $totalday = 0;
        $result = [];
        
        foreach($costs as $cost){
            $cost_data[$cost->week][$cost->user_id] = $cost;
            
            // // 週ごとの合計
            // if (!isset($week_total_cost[$cost->week_id])) {
            //     $week_total_cost[$cost->week_id] = 0;
            // }
            // // 週ごとの人月を出し、フォーマットを変更
            // $week_total_cost[$cost->week_id] += $cost->cost / 100;
            // $week_total_cost[$cost->week_id] = number_format($week_total_cost[$cost->week], 2, '.', '');
            
            
            $total_sumday += $total_day[$cost->week_id];
            
            // 選択期間のユーザーのコスト合計
            $user_cost[$cost->week_id] = $cost->cost * $total_day[$cost->week_id];
            
            if (!isset($user_sum[$cost->user_id])) {
                $user_sum[$cost->user_id] = 0;
            }
            
            $user_sum[$cost->user_id] += $user_cost[$cost->week_id];
            
            $result[$cost->user_id] = $user_sum[$cost->user_id] / $total_sumday;
        }
        //  var_dump($result);
        //  return;
        
        
        //（（週単位の工数×その週の稼働日数）を期間分合計）÷期間全体の稼働日数
        //（月単位の工数×その月の稼働日数）を期間分合計）÷期間全体の稼働日数
        //（期間内の週単位の工数×その週の稼働日数）を期間分合計）÷期間全体の稼働日数
        //月単位の工数の出し方
        //（週単位の工数×その週の稼働日数）をその月の分合計）÷月全体の稼働日数
        
        // 1weekごとの稼働日数を計算する
        // 選択された期間内での稼働日数を計算
        // ログインしているユーザーの工数情報から、選択されているコストを抽出
        // (コストデータ×その週の稼働日数)/全体日数 + 残りのデータ
    
        $data = [
                'costs' => $costs,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'week_enddata' => $week_enddata,
                'start_select' => $start_select,
                'end_select' => $end_select,
                'result' => $result,
                'user_id' => $user_id,
                ];
                
        return view(('proposition.cost') , compact('data'));
        
    }
    // 案件工数確認ページ検索
    public function proposition_cost_search(Request $request)
    {   
        
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required|after:start' ,
            ]);
        
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
        
        $week_data = [];
        $week_data[0] =
          '報告週を選択してください';
          
         $week_enddata = [];
        $week_enddata[0] =
         '選択してください';
        
        // 週別の工数合計
        $interval = [];
        $total_day = [];
        $total_sumday = 0;
                            
        // weeksより報告週の週ごとの日数を計算
        foreach($weeks as $week){
            $week_data[$week->start_date] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            
            $week_enddata[$week->end_date] = 
            $week->year . '年'. $week->month . '月' . $week->week . 'W'. ' '. date("n/d", strtotime($week->start_date)) .'~'. date("n/d", strtotime($week->end_date));
            
            $startday[$week->id] = new DateTime(date($week->start_date));
            $endday[$week->id] = new DateTime(date($week->end_date));
            
            $interval[$week->id] = $endday[$week->id]->diff($startday[$week->id]);
            
            $total_day[$week->id] = $interval[$week->id]->format('%a') + 1;
            

        }
        
        // テーブル表示用
        $cost_data = [];
        $total_cost = 0;
        $user_cost = [];
        $week_total_cost = [];
        $user_sum = [];
        $totalday = 0;
        $result = [];
        
        foreach($costs as $cost){
            $cost_data[$cost->week][$cost->user_id] = $cost;
            
            // // 週ごとの合計
            // if (!isset($week_total_cost[$cost->week_id])) {
            //     $week_total_cost[$cost->week_id] = 0;
            // }
            // // 週ごとの人月を出し、フォーマットを変更
            // $week_total_cost[$cost->week_id] += $cost->cost / 100;
            // $week_total_cost[$cost->week_id] = number_format($week_total_cost[$cost->week], 2, '.', '');
            
            
            $total_sumday += $total_day[$cost->week_id];
            
            // 選択期間のユーザーのコスト合計
            $user_cost[$cost->week_id] = $cost->cost * $total_day[$cost->week_id];
            
            if (!isset($user_sum[$cost->user_id])) {
                $user_sum[$cost->user_id] = 0;
            }
            
            $user_sum[$cost->user_id] += $user_cost[$cost->week_id];
            
            $result[$cost->user_id] = $user_sum[$cost->user_id] / $total_sumday;
        }
        //  var_dump($result);
        //  return;
        
        
        //（（週単位の工数×その週の稼働日数）を期間分合計）÷期間全体の稼働日数
        //（月単位の工数×その月の稼働日数）を期間分合計）÷期間全体の稼働日数
        //（期間内の週単位の工数×その週の稼働日数）を期間分合計）÷期間全体の稼働日数
        //月単位の工数の出し方
        //（週単位の工数×その週の稼働日数）をその月の分合計）÷月全体の稼働日数
        
        // 1weekごとの稼働日数を計算する
        // 選択された期間内での稼働日数を計算
        // ログインしているユーザーの工数情報から、選択されているコストを抽出
        // (コストデータ×その週の稼働日数)/全体日数 + 残りのデータ
        
    
        $data = [
                'costs' => $costs,
                'proposition' => $proposition,
                'week_data' => $week_data,
                'week_enddata' => $week_enddata,
                'start_select' => $start_select,
                'end_select' => $end_select,
                'result' => $result,
                'user_id' => $user_id,
                ];
                
        return view(('proposition.cost') , compact('data'));
        
    }
}
