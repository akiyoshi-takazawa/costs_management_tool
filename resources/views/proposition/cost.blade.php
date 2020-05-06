@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @if (Auth::check())
        @if(Auth::user()->user_type == 2)
            @include('layouts.auth_sidebar')
        @else
            @include('layouts.employee_sidebar')
        @endif
        @endif
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">案件工数確認</h3>
            <div class="container">
                <div class="row">
                    <div class="col-11">
                        <div class="week_search">
                            {!! Form::open(['route' => ['proposition.cost_search', $data['proposition']->id] , 'method' => 'GET']) !!}
                            {!! Form::label('start', '開始') !!}
                            {!! Form::select('start', $data['week_data'], $data['start_select']) !!}
                            {!! Form::label('end', '終了') !!}
                            {!! Form::select('end', $data['week_enddata'], $data['end_select']) !!}
                            {!! Form::submit('表示', ['class' => 'btn btn-primary']) !!}
                            {{ Form::hidden('id',  $data['proposition']->id) }} 
                            {!! Form::close() !!}
                        </div>
                        <h4 class="proposition border border-primary text-center mt-3 mb-3">
                        案件名: {!! $data['proposition']->name !!} 
                        </h4>
                        <div class="report">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">日付</th>
                                    <th class="text-center">工数</th>
                                </tr>
                                @foreach($data['costs'] as $cost)
                                <tr>
                                    <td class="text-center week">
                                        {{ $data['week_data'][$cost->start_date]}}
                                    </td>
                                    <td class="text-center cost">
                                        {{ $cost->cost .'%' }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="user_sum">
                                    <td class="text-center">
                                        期間合計
                                    </td>
                                    <td class="text-center">
                                        @if($data['result'][$data['user_id']] !== null )
                                        {{ floor($data['result'][$data['user_id']]).'%' }}
                                        @else
                                         0
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection