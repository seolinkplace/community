@extends('performer.layouts.app')
@section('title', __('client.tasks_available'))
@section('content')
<div class="max-w-3xl mx-auto py-6 px-4">
    @include('shared.tasks._task_list', [
        'tasks'         => $tasks,
        'taskTypes'     => $taskTypes,
        'completeRoute' => 'performer.tasks.complete',
        'myRoute'       => 'performer.tasks.my',
        'createRoute'   => 'client.tasks.create',
        'indexRoute'    => 'performer.tasks.index',
    ])
</div>
@endsection

@push('scripts')
@include('shared.tasks._task_list_js')
@endpush
