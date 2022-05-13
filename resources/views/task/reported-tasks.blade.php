@extends('voyager::master')


@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <h1>Незавершенные задания</h1>

                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Задание ID</th>
                            <th>Названия задании</th>
                            <th>Создатель</th>
                            <th>Исполнитель</th>
                            <th>Опции</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td><a  target="_blank" href="{{ route('searchTask.task', $task->id) }}">{{ $task->id }}</a></td>
                            <td><a target="_blank" href="{{ route('searchTask.task', $task->id) }}">{{ $task->name }}</a></td>
                            <td><a  target="_blank" href="{{ route('performers.performer', $task->user_id) }}">{{ $task->user->name }}</a></td>
                            <td><a  target="_blank" href="{{ route('performers.performer', $task->performer_id) }}">{{ $task->performer->name }}</a></td>
                            <td>
                                <a href="{{ route('admin.tasks.complete', $task->id) }}" class="btn btn-primary">Завершить заданию</a>
                                <form action="{{ route('admin.tasks.reported.delete', $task->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-danger">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div>

@stop
