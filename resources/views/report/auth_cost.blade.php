@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">工数詳細確認(承認者)</h3>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="container mb-3">
                        <div class="row">
                        <div class="search col-5">
                            {!! Form::open(['route' => ['report.cost_search', 'id' => $data['proposition']->id] , 'method' => 'GET']) !!}
                            {!! Form::select('week_id', $data['week_data'], $data['week_id'] ) !!}
                            {!! Form::hidden('id',  $data['proposition']->id) !!}
                            {!! Form::submit('検索', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="proposition col-3 border border-primary text-center">
                            <h4>案件名: {!! $data['proposition']->name !!}</h4>
                        </div>
                        </div>
                        </div>
                        <div class="cost">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">表示名</th>
                                    <th class="text-center">登録された<br>工数比率</th>
                                    <th class="text-center">提出日時</th>
                                    <th class="text-center">承認<br>ステータス</th>
                                    <th class="text-center">従業員<br>コメント</th>
                                    <th class="text-center">コメント</th>
                                    <th class="text-center">承認</th>
                                    <th class="text-center">否認</th>
                                </tr>
                                @foreach($data['user_data'] as $cost)
                                {!! Form::open(['route' => ['report.cost_update', 'id' => $cost->id] , 'method' => 'put']) !!}
                                {!! Form::hidden('id', $cost->id) !!}
                                <tr>
                                    <td class="text-center">
                                        {{ $cost->name }}
                                    </td>
                                    <td class="text-center">
                                        {{ $cost->cost . '%' }}
                                    </td>
                                    <td class="text-center">
                                        {{ date("Y/m/d H:i", strtotime($cost->submit_at)) }}
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
                                        {{ $cost->comment }}
                                    </td>
                                    <td class="text-center">
                                        {!! Form::textarea('auth_comment', $cost->auth_comment, ['class' => 'field', 'size' => '22x3']) !!}
                                    </td>
                                    <td class="text-center">
                                        {!! Form::submit('承認', ['class' => 'btn btn-primary', 'name' => 'status']) !!}
                                    </td>
                                    <td class="text-center">
                                        {!! Form::submit('否認', ['class' => 'btn btn-danger', 'name' => 'status']) !!}
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