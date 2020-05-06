@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.employee_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">工数詳細確認(従業員)</h3>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="search">
                            {!! Form::open(['route' => ['report.employee_cost_search', 'id' => $data['proposition']->id] , 'method' => 'GET']) !!}
                            {!! Form::select('week_id', $data['week_data'], $data['week_id'] ) !!}
                            {!! Form::hidden('id',  $data['proposition']->id) !!}
                            {!! Form::submit('検索', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="cost">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">案件名</th>
                                    <th class="text-center">案件ID</th>
                                    <th class="text-center">登録した工数比率</th>
                                    <th class="text-center">承認ステータス</th>
                                    <th class="text-center">コメント入力欄</th>
                                    <th class="text-center">承認者コメント</th>
                                    <th class="text-center">提出</th>
                                </tr>
                                @foreach($data['user_data'] as $cost)
                                {!! Form::open(['route' => ['report.cost_update', 'id' => $cost->id] , 'method' => 'put']) !!}
                                {!! Form::hidden('id', $cost->id) !!}
                                <tr>
                                    <td class="text-center">
                                        {{ $cost->name }}
                                    </td>
                                    <td class="text-center">
                                        {{ $cost->input_id }}
                                    </td>
                                    <td class="text-center">
                                        {!! Form::select('cost[]', $data['default_costs_loop'], $cost->cost) !!}%
                                    </td>
                                    <td class="text-center">
                                        @if($cost->status == 1)
                                        提出済
                                        @elseif($cost->status == 2)
                                        承認
                                        @else($cost->status == 3)
                                        否認
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {!! Form::textarea('comment', $cost->comment, ['class' => 'field', 'size' => '25x3']) !!}
         
                                    </td>
                                    <td class="text-center">
                                        {{ $cost->auth_comment }}
                                    </td>
                                    <td class="text-center">
                                        {!! Form::hidden('status', '1') !!}
                                        {!! Form::submit('再提出', ['class' => 'btn btn-primary']) !!}
                                    </td>
                                </tr>
                                {!! Form::close() !!}
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection