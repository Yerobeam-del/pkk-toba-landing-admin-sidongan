{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="table-container u-p-0">
    @include('admin.partials.table', [
        'data' => $data,
        'columns' => $columns,
        'emptyMessage' => $emptyMessage,
        'editRoute' => $editRoute,
        'deleteRoute' => $deleteRoute,
        'actions' => $actions
    ])
</div>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
