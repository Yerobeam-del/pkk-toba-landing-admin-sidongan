<div class="table-container" style="padding:0">
    @include('admin.partials.table', [
        'data' => $data,
        'columns' => $columns,
        'emptyMessage' => $emptyMessage,
        'editRoute' => $editRoute,
        'deleteRoute' => $deleteRoute,
        'actions' => $actions
    ])
</div>