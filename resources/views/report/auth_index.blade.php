@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">報告データ一覧(承認者)</h3>
            <div class="container">
                <div class="row">
                    <div class="col-11">
                        <div class="search">
                            {!! Form::open(['route' => 'report.search', 'method' => 'GET']) !!}
                            {!! Form::label('name', '案件名') !!}
                            {!! Form::text('name', old('name')) !!}
                            {!! Form::label('week', '報告週') !!}
                            {!! Form::select('week', $data['week_data'], $data['inputweek'] ) !!}
                            {!! Form::submit('検索', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="report mt-3">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="text-center">案件名</th>
                                    <th class="text-center">案件ID</th>
                                    <th class="text-center">報告週</th>
                                    <th class="text-center">登録工数</th>
                                </tr>
                                @foreach($data['proposition_data'] as $cost)
                                <tr>
                                    <td class="text-center">
                                        {!! link_to_route('report.show', $cost->name, ['id' => $cost->proposition_id] ) !!}
                                    </td>
                                    <td class="text-center">
                                        {{ $cost->input_id }}
                                    </td>
                                    <td class="text-center">
                                        {!! link_to_route('report.cost', $cost->year . '年' . $cost->month . '月' . $cost->week . 'W' . date("n/d", strtotime($cost->start_date)) .'~'. date("n/d", strtotime($cost->end_date)), ['id' => $cost->proposition_id, 'week_id' => $cost->week_id] ) !!}
                                    </td>
                                    <td class="text-center">
                                        {{ $data['week_total_cost'][$cost->proposition_id][$cost->week_id] .'人月'}}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection