@extends('layouts.app')

@section('content')
    @if (Auth::check())
        @if(Auth::user()->user_type == 2)
            @include ('users.2_top')
        @else
            @include ('users.1_top')
        @endif
    @else
    <div class="container">
    <div class="center jumbotron">
        <div class="text-center">
            <h1>社内工数管理システム(KOSU)ログイン</h1>
        </div>
        <div class="container">
        <div class="row">
        <!-- ログインフォーム -->
            <div class="col-sm-6 offset-sm-3">
                {!! Form::open(['route' => 'login.post']) !!}
                    <!-- ID入力 -->
                    <div class="form-group">
                        {!! Form::label('email', 'ID入力') !!}
                        {!! Form::email('email', old('email'), ['class' => 'form-control']) !!}
                    </div>
                    <!-- パスワード入力 -->
                    <div class="form-group">
                        {!! Form::label('password', 'パスワード入力') !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                    </div>
                    <div class="flex-column float-right">
                    <div class="mb-2">
                     <!--　登録済みのログインボタン　-->
                    {!! Form::submit('ログイン', ['class' => 'btn btn-outline-primary']) !!}
                    </div>
                    <div class="mt-3">
                    {!! link_to_route('signup.get', '新規登録', [], ['class' =>  'btn btn-outline-primary']) !!}
                    </div> 
                    </div>
                {!! Form::close() !!}
            </div>
        <!-- 新規登録ボタン-->
        </div>
        </div>
    </div>
    </div>
    @endif
@endsection