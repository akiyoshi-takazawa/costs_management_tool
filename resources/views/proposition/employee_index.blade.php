@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.employee_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">案件情報(従業員)</h3>
            <div class="container">
                <table class="table table-bordered">
                            <tr>
                                <th class="text-center">案件名</th>
                                <th class="text-center">案件ID</th>
                                <th class="text-center">クライアント名</th>
                                <th class="text-center">承認者</th>
                                <th class="text-center">工数確認</th>
                            </tr>
                    @foreach($data['proposition_data'] as $proposition)
                            <tr>
                                <td class="text-center">{{ $proposition->name }}</td>
                                <td class="text-center">{{ $proposition->input_id }}</td>
                                <td class="text-center">{{ $proposition->client_name }}</td>
                                <td class="text-center">{{ $proposition->authorizer_user_name }}</td>
                                <td class="text-center">
                                {!! link_to_route('proposition.cost', '案件工数確認', ['id' => $proposition->proposition_id], ['class' => 'btn btn-danger']) !!}
                                </td>
                            </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection