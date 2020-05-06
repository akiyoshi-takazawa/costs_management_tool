<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User; //追加

class UsersController extends Controller
{
    // ユーザー管理画面表示処理」
    public function index()
    {   
        $data = [];
        
        if(\Auth::user()->user_type = 2){
            
            $users = \App\User::all();
            
            $data = [
            'users' => $users
            ];
            
            return view('auth.users_management', compact('data'));
            
        }else{
            return back('/');
        }
        
    }

    // getでmatters/createにアクセスされた場合の「案件作成画面処理」
    public function create()
    {
        //
    }

    // postでmatters/にアクセスされた場合の「案件新規登録処理」
    public function store(Request $request)
    {
        //
    }

    // getでmatters/idにアクセスされた場合の「案件取得」
    public function show($id)
    {
        //
    }

    // getでmatters/id/editにアクセスされた場合の「案件更新画面表示処理」
    public function edit($id)
    {
        //
    }

    // putまたはpatchでmatters/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        //
    }

    // deleteでmatters/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        //
    }
}
