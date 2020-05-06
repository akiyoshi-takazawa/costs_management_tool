@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">案件情報(承認者)</h3>
            <div class="container">
                <table class="table table-bordered">
                            <tr>
                                <th class="text-center">案件名</th>
                                <th class="text-center">案件ID</th>
                                <th class="text-center">クライアント名</th>
                                <th class="text-center">承認者</th>
                                <th class="text-center">案件工数確認</th>
                                <th class="text-center">案件編集</th>
                            </tr>
                    @foreach($data['propositions'] as $proposition)
                            <tr>
                                <td class="text-center">{{ $proposition->name }}</td>
                                <td class="text-center">{{ $proposition->input_id }}</td>
                                <td class="text-center">{{ $proposition->client_name }}</td>
                                <td class="text-center">{{ $proposition->user->name }}</td>
                                <td class="text-center">
                                {!! link_to_route('proposition.cost', '確認', ['id' => $proposition->id], ['class' => 'btn btn-outline-primary']) !!}
                                </td>
                                
                                {!! Form::open(['route' => 'proposition.edit', 'method' => 'GET']) !!}
                                {{ Form::hidden('id', $proposition->id) }}
                                <td class="text-center">
                                {!! Form::submit('編集', ['class' => 'btn btn-outline-primary']) !!}
                                </td>
                                {!! Form::close() !!}
                            </tr>
                    @endforeach
                </table>
                <div class="text-right">
                {!! link_to_route('proposition.create', '案件登録', [], ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        </div>
    </div>
@endsection