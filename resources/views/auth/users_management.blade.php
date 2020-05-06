@extends('layouts.app')
@section('content')
@include('layouts.header')
    <div class="row">
        @include('layouts.auth_sidebar')
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center mt-3 mb-5">ユーザー管理(承認者)</h3>
            <div class="container">
                <table class="table table-bordered">
                    <tr>
                        <th class="text-center">表示名</th>
                        <th class="text-center">ID</th>
                        <th class="text-center">パスワード<br>リセット</th>
                    </tr>
                    @foreach($data['users'] as $user)
                    <tr>
                        <td class="text-center">{{ $user->name }}</td>
                        <td class="text-center">{{ $user->email }}</td>
                        <td class="text-center"></td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection