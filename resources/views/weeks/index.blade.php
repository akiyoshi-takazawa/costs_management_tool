@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">報告週登録(承認者)</h3>
            <div class="container">
            <div class="form-group year_month">
                <span>
                登録を行う年月を選択：
                </span>
                <span class="year">
                {{Form::selectRange('year', 2020, 2025, $data['year'] )}}年
                </span>
                <span class="month">
                {{Form::selectRange('month', 1, 12, $data['month'] )}}月
                </span>
            </div>
            
                <table class="table table-bordered">
                    <tr>
                        <th class="text-center">報告週</th>
                        <th class="text-center">開始日</th>
                        <th class="text-center">終了日</th>
                        <th class="text-center">登録</th>
                    </tr>
                            
                    @for($i = 1; $i < 7; $i++)
                    @if(@isset($data['week_data'][$i]))
                    {!! Form::open(['route' => 'weeks.store']) !!}
                    {{ Form::hidden('year', $data['year']) }}
                    {{ Form::hidden('month', $data['month']) }}
                    {{ Form::hidden('id', $data['week_data'][$i]->id)}}
                    <tr>
                        <td class="text-center">{{ $i }} {{ Form::hidden('week', $i)}}</td>
                        <td class="text-center"><span class="border border-primary">登録日: {{ date('m月d日', strtotime($data['week_data'][$i]->start_date)) }}</span><div>{{ Form::date('start_date', date('Y-m-d') )}}</div></td>
                        <td class="text-center"><span class="border border-primary">登録日: {{ date('m月d日', strtotime($data['week_data'][$i]->end_date)) }}</span><div>{{ Form::date('end_date', date('Y-m-d') )}}</div></td>
                        <td class="text-center">{!! Form::submit('登録', ['class' => 'btn btn-primary btn-block ']) !!}</td>
                    </tr>
                    {!! Form::close() !!}
                            
                    @elseif(@is_null($data['week_data'][$i]))
                    {!! Form::open(['route' => 'weeks.store']) !!}
                    {{ Form::hidden('year', $data['year']) }}
                    {{ Form::hidden('month', $data['month']) }}
                    <tr>
                        <td class="text-center">{{ $i }}  {{ Form::hidden('week', $i)}}</td>
                        <td class="text-center">{{ Form::date('start_date', date('Y-m-d') )}}</td>
                        <td class="text-center">{{ Form::date('end_date', date('Y-m-d') )}}</td>
                        <td class="text-center">{!! Form::submit('登録', ['class' => 'btn btn-primary btn-block ']) !!}</td>
                    </tr>
                    {!! Form::close() !!}
                    @endif
                    @endfor
                            
                </table>
            </div>
        </div>
    </div>
@endsection