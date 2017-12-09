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
                                        <button class=" btn btn-primary result" href="#">{{$item->taskId}}</button>
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

                <a class=" btn btn-default" href="{{route('index')}}">Get back</a>
            </div>
        </div>
    </main>
    <script>
        $(".result").click(function () {
            var taskId = $(this).parent(".hid").attr('id');
            $.post({
                url: "{{route('checkResult')}}",
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