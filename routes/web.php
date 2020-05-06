<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');

});

// サインアップ
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

// ログイン認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');


// ユーザー機能
Route::group(['middleware' => ['auth']], function () {
    //下記でプロフィール編集を行う
    Route::get('users_management', 'UsersController@index')->name('users.management');
    
    //案件情報へ移動
    Route::get('proposition', 'PropositionsController@index')->name('information.get');
    Route::get('proposition/create', 'PropositionsController@create')->name('proposition.create');
    Route::post('proposition/create', 'PropositionsController@store')->name('proposition.store');
    //案件情報編集
    Route::get('proposition/edit', 'PropositionsController@edit')->name('proposition.edit');
    // 案件を更新
    Route::put('proposition/edit/update', 'PropositionsController@update')->name('proposition.update');
    //案件工数確認ページへ移動
    Route::get('proposition/{id?}', 'CostsController@proposition_cost')->name('proposition.cost');
    //案件工数確認ページで検索
    Route::get('proposition/{id?}/search', 'CostsController@proposition_cost_search')->name('proposition.cost_search');
    
    //報告週ページへ移動
    Route::get('weeks', 'WeeksController@index')->name('weeks.index');
    Route::get('weeks/{year?}/{month?}', 'WeeksController@index');
    Route::post('weeks/{year?}/{month?}', 'WeeksController@store')->name('weeks.store');

    // デフォルト工数
    // トップページからページ移動
    Route::get('default_costs', 'DefaultCostsController@index')->name('default_costs.index');
    // 工数登録
    Route::post('default_costs', 'DefaultCostsController@store')->name('default_costs.store');
    
    // 工数処理
    // トップページからページ移動
    Route::get('costs', 'CostsController@index')->name('costs.index');
    //報告週を選択
    Route::get('costs/{inputweek?}', 'CostsController@index');
    // 工数登録
    Route::post('costs{inputweek?}', 'CostsController@store')->name('costs.store');
   
    // 報告データ一覧(従業員)
    Route::get('employee', 'CostsController@employee_index')->name('report.employee_index');
    // 工数詳細確認ページへ遷移
    Route::get('employee/week/{id?}', 'CostsController@employee_cost')->name('employee_cost');
    // 工数詳細確認ページでの検索
    Route::get('employee/week/{id?}/search', 'CostsController@employee_cost_search')->name('report.employee_cost_search');
    
    // 報告データ一覧(承認者)
    Route::get('report', 'CostsController@report_index')->name('report.report_index');
    // 検索/報告データ一覧
    Route::get('report/search', 'CostsController@search')->name('report.search');
    // 集計データ一覧
    Route::get('report/data/{id?}', 'CostsController@report_show')->name('report.show');
    // 集計データ一覧検索
    Route::get('report/data/{id?}/search', 'CostsController@report_search')->name('report.show_search');
    // 工数詳細確認ページへ遷移
    Route::get('report/week/{id?}', 'CostsController@report_cost')->name('report.cost');
    // 工数詳細確認ページでの検索
    Route::get('report/week/{id?}/search', 'CostsController@cost_search')->name('report.cost_search');
    // 工数詳細確認ページ　データアップデート
    Route::put('report/week/{id?}/update', 'CostsController@cost_update')->name('report.cost_update');
    
    
});



