<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropositionsController extends Controller
{
    public function index()
    {   
        $data = [];
        
        $user_id = \Auth::user()->id;
        
        //従業員の場合
        if(\Auth::user()->user_type == 1){
            
            // $user_cost = \Auth::user()->costs();
            
            // 1.工数データの取得
            $cost_sc = \App\Cost::select('costs.id', 'costs.week_id', 'costs.proposition_id', 'costs.user_id', 'costs.cost', 'costs.submit_at', 'costs.status')
                        ->where('costs.user_id', $user_id);
            // 2.1に案件データを追加
            $cost_sc->join('propositions', 'propositions.id', '=', 'costs.proposition_id')
            ->addSelect('propositions.input_id', 'propositions.name', 'propositions.client_name', 'propositions.authorizer_user_id');
            
            $cost_sc->join('users', 'users.id', '=', 'propositions.authorizer_user_id')
            ->addSelect('users.name as authorizer_user_name');
            
            $propositions = $cost_sc->get();
            
            // var_dump($propositions);
            // return;
            
            $proposition_data = [];
            
            foreach($propositions as $proposition){
                $proposition_data[$proposition->proposition_id] = $proposition;
            }
            
            $data = [
            'proposition_data' => $proposition_data,
            
            ];
            
            // var_dump($data);
            // return;
            
            return view('proposition.employee_index', compact('data'));
           
           
        }//承認者の場合
        else{
            $propositions = \App\Proposition::where('authorizer_user_id', $user_id)->get();
           
            $data = [
            'propositions' => $propositions
            ];
            
            return view('proposition.index', compact('data'));
        }
        
    }
    
    public function create()
    { 
        $data = [];
        $users = \App\User::where('user_type', '2')->get();
        
        $proposition = new \App\Proposition;
        
        $auth_name_loop = [];
        
        $auth_name_loop = [
            '' => '選択してください' ];
            
                        
        foreach($users as $user) {
          $auth_name_loop[$user->id] = $user->name;
        }
        
        $data = [
            'proposition' => $proposition,
            'auth_name_loop' => $auth_name_loop,
        ];
        
        return view('proposition.create', compact('data'));
    }
    
    public function store(Request $request)
    { 
        $this->validate($request, [
            'name' => 'required',
            'input_id' => 'required',
            'client_name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'authorizer_user_id' => 'required'
            ]);
            
            
            
         \App\Proposition::create ([
            'name' => $request->name,
            'input_id' => $request->input_id,
            'client_name' => $request->client_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'authorizer_user_id' => $request->authorizer_user_id,
            ]);
        
        $data = [];
        
        $user_id = \Auth::user()->id;
        
        $propositions = \App\Proposition::where('authorizer_user_id', $user_id)->get();
        
        $data = [
            'propositions' => $propositions];
            
            return view('proposition.index', compact('data'));
    }
    
    //編集画面へ移動
    public function edit(Request $request)
    { 
        $data = [];
        
        $users = \App\User::where('user_type', '2')->get();
        
        $auth_name_loop = [];
        $auth_name_loop = [
            '' => '選択してください' ];
            
                        
        foreach($users as $user) {
          $auth_name_loop[$user->id] = $user->name;
        }
        
        $id = $request->id;
        
        $proposition = \App\Proposition::find($id);
        
        $data = [
            'proposition' => $proposition,
            'auth_name_loop' => $auth_name_loop,
            ];
            
            return view('proposition.edit', compact('data'));
    }
    //編集画面で、案件を更新
    public function update(Request $request)
    { 
        $this->validate($request, [
            'name' => 'required',
            'input_id' => 'required',
            'client_name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'authorizer_user_id' => 'required'
            ]);
        
        $id = $request->id;
        
        $proposition = \App\Proposition::find($id);
        
        $proposition->name = $request->name;
        $proposition->input_id = $request->input_id;
        $proposition->client_name = $request->client_name;
        $proposition->start_date = $request->start_date;
        $proposition->end_date = $request->end_date;
        $proposition->authorizer_user_id = $request->authorizer_user_id;
        
        $proposition->save();
        
        return redirect('proposition');
    }
    
}
