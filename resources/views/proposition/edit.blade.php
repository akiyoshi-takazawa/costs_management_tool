@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">案件情報編集(承認者)</h3>
            <div class="container">
                {!! Form::open(['route' => 'proposition.update', 'method' => 'PUT']) !!}
                    <div class="form-group row">
                        {!! Form::label('name', '案件名', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::text('name', $data['proposition']->name, ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('input_id', '案件ID', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::text('input_id', $data['proposition']->input_id, ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('client_name', 'クライアント名', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::text('client_name', $data['proposition']->client_name, ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('start_date', '案件開始日', ['class' => 'ml-5 mr-3 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::input('date', 'start_date', date('Y-m-d', strtotime($data['proposition']->start_date))) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('endt_date', '案件終了日', ['class' => 'ml-5 mr-3 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::input('date', 'end_date', date('Y-m-d', strtotime($data['proposition']->end_date))) !!}
                    </div>
                    <div class="form-group row">
                        {!! Form::label('authorizer_user_id', '承認者', ['class' => 'ml-5 text-center col-sm-3 col-md-2 form-control']) !!}
                        {!! Form::select('authorizer_user_id', $data['auth_name_loop'], [], ['class' => 'ml-3 col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 form-control']) !!}
                    </div>
                    <div class="text-right">
                    {!! Form::submit('編集完了', ['class' => 'btn btn-primary']) !!}
                    </div>
            {!! Form::hidden('id', $data['proposition']->id) !!}
            {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection