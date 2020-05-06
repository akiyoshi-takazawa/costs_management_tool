@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">集計データ一覧(承認者)</h3>
            <div class="container">
                <div class="row">
                    <div class="col-11">
                        <div class="container mb-3">
                        <div class="row">
                        <div class="search col-5">
                            {!! Form::open(['route' => ['report.show_search', $data['proposition']->id] , 'method' => 'GET']) !!}
                            {!! Form::selectRange('year', 2020 , 2025, $data['year']) . '年' !!}
                            {!! Form::selectRange('month', 1 , 12, $data['month'])  . '月' !!}
                            {!! Form::submit('検索', ['class' => 'btn btn-primary']) !!}
                            {{ Form::hidden('id',  $data['proposition']->id) }} 
                            {!! Form::close() !!}
                        </div>
                        <div class="proposition col-3 border border-primary text-center">
                            <h4>案件名: {!! $data['proposition']->name !!}</h4>
                        </div>
                        </div>
                        </div>
                        <div class="report">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">表示名</th>
                                    <th class="text-center">1W</th>
                                    <th class="text-center">2W</th>
                                    <th class="text-center">3W</th>
                                    <th class="text-center">4W</th>
                                    <th class="text-center">5W</th>
                                    <th class="text-center">6W</th>
                                    <th class="text-center">月合計</th>
                                </tr>
                                @foreach($data['user_data'] as $user)
                                <tr>
                                    <td class="text-center">
                                        {{ $user->name }}
                                    </td>
                                    @for($num = 1; $num < 7; $num++)
                                    <td class="text-center week">
                                        {{ isset($data['cost_data'][$num][$user->user_id]) ? $data['cost_data'][$num][$user->user_id]->cost . '%'  : 0 . '%'  }}
                                    </td>
                                    @endfor
                                    <td class="text-center average">
                                        {{ isset($data['user_sum'][$user->user_id]) ? round(($data['user_sum'][$user->user_id]), 1) . '%'  : 0 . '%'  }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="user_sum">
                                    <td class="text-center">
                                        ユーザ合計
                                    </td>
                                    @for($num = 1; $num < 7; $num++)
                                    <td class="text-center">
                                        {{ isset($data['week_total_cost'][$num]) ? $data['week_total_cost'][$num] . '人月': 0 . '人月' }}
                                    </td>
                                    @endfor
                                    <td class="text-center">
                                        {{ $data['total_cost'] . '人月'}}
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