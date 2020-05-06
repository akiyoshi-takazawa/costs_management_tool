@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">案件登録(承認者)</h3>
            <div class="container">
                {!! Form::open(['route' => 'proposition.store']) !!}
                    <div class="form-group row">
                        {!! Form::label('name', '案件名', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::text('name', old('name'), ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('input_id', '案件ID', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::text('input_id', old('input_id'), ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('client_name', 'クライアント名', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::text('client_name', old('client_name'), ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('start_date', '案件開始日', ['class' => 'ml-5 mr-3 text-center col-sm-3 col-md-2 form-control']) !!}
                        {{ Form::date('start_date', date('Y-m-d') )}}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('endt_date', '案件終了日', ['class' => 'ml-5 mr-3 text-center col-sm-3 col-md-2 form-control']) !!}
                        {{ Form::date('end_date', date('Y-m-d') )}}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('authorizer_user_id', '承認者', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::select('authorizer_user_id', $data['auth_name_loop'], [], ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        <div class="ml-5 text-center col-sm-7 col-md-7"></div>
                        {!! Form::submit('新規登録', ['class' => 'ml-4 btn btn-primary']) !!}
                    </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection