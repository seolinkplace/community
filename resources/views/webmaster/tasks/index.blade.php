@extends('webmaster.layouts.app')
@section('title', __('client.tasks_available'))
@section('content')
<div class="max-w-3xl mx-auto py-6 px-4">
    @include('shared.tasks._task_list', [
        'tasks'         => $tasks,
        'taskTypes'     => $taskTypes,
        'completeRoute' => 'webmaster.tasks.complete',
        'myRoute'       => 'webmaster.tasks.my',
        'createRoute'   => 'webmaster.tasks.create',
        'indexRoute'    => 'webmaster.tasks.index',
    ])
</div>
@endsection

@push('scripts')
@include('shared.tasks._task_list_js')
@endpush
