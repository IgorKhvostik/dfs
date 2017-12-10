@extends('layouts.layout')
@section('content')

    <header id="header">
        <div class="header-wrap">
            <h1 class="header-text">Search results</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="row">
                <div class="main-container">
                    <table class="table table-bordered main-table">
                        <thead>
                        <tr>
                            <th>Search engine</th>
                            <th>Keyword</th>
                            <th>Website</th>
                            <th>Position</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td>{{$item->searchEng}}</td>
                                <td>{{$item->keywords}}</td>
                                <td>{{$item->website}}</td>
                                <td>
                                    @if(is_null($item->position))
                                        <div id="{{$item->taskId}}" class="hid">
                                            <button class=" btn btn-default  btn-sm result" href="">Get position
                                            </button>
                                        </div>
                                    @else{{$item->position}}
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($item->position))
                                        Processing
                                    @else Tracked
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="button-box">
                        <a class=" btn btn-default" href="{{route('index')}}">Get back</a>
                        <a class=" btn btn-default" href="{{route('checkAll')}}">Get all completed tasks</a>
                    </div>
                    <div class="pagination-box">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>

        $(".result").click(function () {
            var taskId = $(this).parent(".hid").attr('id');
            $.post({
                url: "{{route('check')}}",
                data: {taskId: taskId},
                dataType: 'html',
                success: function (result) {
                    $('#' + taskId + '.hid').empty().html('<b>' + result + '</b>');
                },
                error: function () {
                    $('#' + taskId + '.hid').empty().html('<b>Error!</b>');
                }
            })
        });

    </script>

@endsection