@extends('layouts.app')

@section('content')
<div class="container jumbotron">
    <div class="text-center mb-5">
        <h1>新規登録</h1>
    </div>
    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            {!! Form::open(['route' => 'signup.post']) !!}
                <!-- ID入力欄 -->
                <div class="form-group">
                    {!! Form::label('email', 'ID入力(メールアドレス)') !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control']) !!}
                </div>
                <!-- 表示名 -->
                <div class="form-group">
                    {!! Form::label('name', '表示名入力') !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
                </div>
                <!-- パスワード --> 
                <div class="form-group">
                        {!! Form::label('password', 'パスワード入力') !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                </div>
                <!-- 再確認パスワード -->  
                <div class="form-group">
                        {!! Form::label('password_confirmation', '再度パスワード入力') !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                </div>
                <!-- 従業員と承認者選択 -->  
                <div class="form-group">
                @php
                    $job_name_loop = [
                        ''      => '選択してください' ,
                        '1' =>'1.従業員' ,
                        '2' =>'2.承認者' ,
                    ];
                @endphp
                        {!! Form::label('user_type', '従業員/承認者 選択') !!}
                        {!! Form::select('user_type', $job_name_loop, old('job_name'), ['class' => 'form-control']) !!}
                </div>
                
                {!! Form::submit('新規登録', ['class' => 'btn btn-primary btn-block mt-5']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection