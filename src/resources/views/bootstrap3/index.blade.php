@extends(config('enews.layout'))

@section(config('enews.layout_section'))

    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading"
                 style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center;">
                <h3 class="panel-title">Enewsletter Manager</h3>

                <div>
                    <a href="{{ route('jzpeepz.enews.create') }}" class="btn btn-success btn-xs">Create Enews</a>
                </div>
            </div>
            @if ($enews->isEmpty())
            <div class="panel-body">
                <div>No enewsletters exist.</div>
            </div>
            @endif

            @if (!$enews->isEmpty())
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Updated</th>
                            <th>Sent</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enews as $item)
                            <tr>
                                <td>{{ $item->subject }}</td>
                                <td>{{ $item->updated_at->format('n/y/Y g:i a') }}</td>
                                <td>{{ !empty($item->sent_at) ? $item->sent_at->format('n/y/Y g:i a') : '' }}</td>
                                <td>
                                    <a href="{{ route('jzpeepz.enews.edit', $item->id) }}">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

@endsection