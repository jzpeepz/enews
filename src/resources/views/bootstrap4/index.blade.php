@extends(config('enews.layout'))

@section(config('enews.layout_section'))

    <div class="container">

        <div class="card mt-4">
            <div class="card-header"
                 style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center;">
                <h3 class="panel-title">Enewsletter Manager</h3>

                <div>
                    @if (count(config('enews.enewsletters')) > 1)
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Create Enews <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach (config('enews.enewsletters') as $enewsConfig)
                                <li><a href="{{ route('jzpeepz.enews.create', ['template' => $enewsConfig['key']]) }}">{{ $enewsConfig['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        @foreach (config('enews.enewsletters') as $enewsConfig)
                        <a href="{{ route('jzpeepz.enews.create', ['template' => $enewsConfig['key']]) }}" class="btn btn-success btn-xs">Create Enews</a>
                        @endforeach
                    @endif
                </div>
            </div>
            @if ($enews->isEmpty())
            <div class="card-body">
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