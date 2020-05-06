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
            <h3 class="text-center mt-3 mb-5">工数入力</h3>
                <div class="container">
                    <div class="row">
                        <div class="col-11">
                            <div class="report-week">
                                提出週を選択：{{ Form::select('week_data', $data['week_data'], $data['inputweek'] ) }}
                            </div>
                            {!! Form::open(['route' => 'costs.store']) !!}
                            {{ Form::hidden('week_id', $data['inputweek']) }}
                            {{ Form::hidden('status', '1') }}
                            {{ Form::hidden('comment', '') }}
                            <div class="cost-input">
                                <div class = "default_costs_button text-right">
                                {!! Form::submit('提出ボタン', ['class' => 'btn btn-primary mb-2']) !!}
                                </div>
                                <div class="cost">
                                    <!--<div class = "default_costs_button text-right">-->
                                    <!--    <button>デフォルト工数反映</button>-->
                                    <!--</div>-->
                                    <table class="table table-bordered">
                                       <tr>
                                        <th class="text-center">案件名</th>
                                        <th class="text-center">案件ID</th>
                                        <th class="text-center">工数入力</th>
                                    </tr>
                                    @foreach($data['propositions'] as $proposition)
                                    <tr class="cost">
                                        <td class="text-center">
                                            {{ $proposition->name }}
                                            {!! Form::hidden('proposition_id[]', $proposition->id) !!}
                                        </td>
                                        <td class="text-center">
                                            {{ $proposition->input_id }}
                                        </td>
                                        <td class="text-center">
                                        <div class="border border-primary ml-5 mr-5">登録済: {{ isset($data['cost_data'][$proposition->id]) ?  $data['cost_data'][$proposition->id] : '-'  }} %</div>
                                        {!! Form::select('cost[]', $data['costs_loop'], '選択してください') !!}%
                                        </td>
                                    </tr>
                                    @endforeach
                                    </table>
                                    <div class="text-right">
                                    {!! Form::submit('提出ボタン', ['class' => 'btn btn-primary']) !!}
                                    </div>
                                </div>
                            </div>
                            {{-- {!! Form::close() !!} --}}
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection