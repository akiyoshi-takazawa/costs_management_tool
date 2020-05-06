@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        
        @include('layouts.auth_sidebar')

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <div class="container">
            <h3 class="text-center mt-3 mb-5">デフォルト工数登録</h3>
                <table class="table table-bordered">
                    <tr>
                        <th class="text-center">案件名</th>
                        <th class="text-center">案件ID</th>
                        <th class="text-center">工数比率</th>
                    </tr>
                        {!! Form::open(['route' => 'default_costs.store']) !!}
                        @foreach($data['propositions'] as $proposition)
                    <tr class="cost">
                        <td class="text-center">{{ $proposition->name }} 
                        {!! Form::hidden('proposition_id[]', $proposition->id) !!}
                        </td>
                        <td class="text-center">{{ $proposition->input_id }}</td>
                        <td class="text-center">
                        @foreach($data['default_costs'] as $default_costs)
                        @if(Auth::check() && $proposition->id == $default_costs->proposition_id)
                    <div>{!! $default_costs->cost !!}</div>
                        @endif 
                        @endforeach
                        {!! Form::select('cost[]', $data['default_costs_loop'], '選択してください') !!}%
                        </td>
                    </tr>
                        @endforeach
                </table>
                <div class="text-right">
                    {!! Form::submit('デフォルト工数登録', ['class' => 'btn btn-primary']) !!}
                </div>
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
                
@endsection