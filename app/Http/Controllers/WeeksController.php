<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeeksController extends Controller
{
    public function index($year = null, $month = null)
    {
        $data = [];
        
        // 現在の年を取得
        $year = $year ?? (int)date("Y");
        // 現在の月を取得
        $month = $month ?? (int)date("n");
        
        //登録してあるデータ(報告週・開始日・終了日)を取得
        $weeks = \App\Week::select('id', 'week', 'start_date', 'end_date')
                    ->where('year', $year)
                    ->where('month', $month)
                    ->get();
        
        $week_data = [];
        
        foreach ($weeks as $week) {
            $week_data[$week->week] = $week ;
        }
    
        
        
        $data = [
            
            'year' => $year ,
            'month' => $month ,
            'week_data' => $week_data ,
            ];
        
        return view('weeks.index', compact('data'));
    }
    
    public function store(Request $request)
    {
        
        // var_dump($request->all());
        // return;
        
        $this->validate($request, [
            'year' => 'required',
            'month' => 'required',
            'week' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            ]);
        
        // idを持っていない新規登録
        if($request->id == null){
            
            \App\Week::create ([
            'year' => $request->year,
            'month' => $request->month,
            'week' => $request->week,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            ]);
            
        return back();
            
        }// idをすでに持っている場合
        else{
            
            $week = \App\Week::find($request->id);
            
            $week->year = $request->year;
            $week->month = $request->month;
            $week->week = $request->week;
            $week->start_date = $request->start_date;
            $week->end_date = $request->end_date;
            
            $week->save();
            
            return back();
            
        }  
    }
    
    
}
