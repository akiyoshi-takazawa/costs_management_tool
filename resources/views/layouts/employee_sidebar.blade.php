<div class="clearfix"></div>
<div class="col-sm-3 col-md-2 sidebar">
    <ul class="list-group">
      <li class="list-group-item list-group-item-primary">案件・工数管理</li>
      {!! link_to_route('information.get', '案件情報', [], ['class' => 'list-group-item']) !!}
      {!! link_to_route('costs.index', '工数入力',  [], ['class' => 'list-group-item']) !!}
      {!! link_to_route('report.employee_index', '報告データ一覧', [], ['class' => 'list-group-item']) !!}
      <li class="list-group-item list-group-item-primary">ユーザ管理</li>
      <li class="list-group-item">プロフィール編集</li>
    </ul>
</div>