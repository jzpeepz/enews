@extends(config('enews.layout'))

@section(config('enews.layout_section'))

    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Enewsletter Manager</h3>
            </div>
            <div class="panel-body">
                <form action="{{ $formOptions['action'] }}"
                      method="{{ in_array($formOptions['method'], ['get', 'post']) ? $formOptions['method'] : 'post' }}"
                      style="margin-bottom: 0;">

                    {{ csrf_field() }}

                    @if (!in_array($formOptions['method'], ['get', 'post']))
                        <input type="hidden" name="_method" value="{{ $formOptions['method'] }}">
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Template</label>
                                <select class="form-control" disabled>
                                    @foreach (config('enews.enewsletters') as $enewsConfig)
                                    <option value="{{ $enewsConfig['key'] }}" {{ $email->template == $enewsConfig['key'] ? 'selected' : '' }}>{{ $enewsConfig['label'] }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="template" value="{{ $email->template }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Day/Zone</label>
                                <select name="day" class="form-control">
                                    @foreach (config('enews.days') as $dayIndex => $dayLabel)
                                    <option value="{{ $dayIndex }}">{{ $dayLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Will Be Sent On</label>
                                <input type="text"
                                       name="send_at"
                                       class="form-control"
                                       value="{{ $email->send_at }}">
                                <p class="help-block">This determines the date displayed in the email.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="">Subject</label>
                        <input type="text"
                               name="subject"
                               class="form-control"
                               value="{{ $email->subject }}"
                               maxlength="255"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="">Preview Text</label>
                        <input type="text"
                               name="preview_text"
                               class="form-control"
                               value="{{ $email->preview_text }}"
                               maxlength="255">
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <h2>Available Articles</h2>
            
                            <div class="articles available-articles well">
                                @foreach ($articles as $article)
                                    <div id="article-available-{{ $article->id }}"
                                        class="article btn btn-default btn-block"
                                        data-article-id="{{ $article->id }}">
                                        <strong>{{ $article->published_on('n/j') }}</strong> {{ $article->title }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <h2>Selected Articles</h2>
            
                            <div class="articles selected-articles well">
                                @if (!empty($email->articles))
                                    @foreach ($email->articles as $id)
                                        <?php $a = Article::find(array('id' => $id)); ?>
                                        <div id="article-selected-{{ $id }}>" class="article btn btn-default btn-block" data-article-id="{{ $id }}">
                                            <strong>{{ $a->published_on('n/j') }}</strong> {{ $a->title }}
                                            <input type="hidden" name="articles[]" value="{{ $id }}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Preview Email</button>

                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){

    // regular articles
    $(".selected-articles").sortable();

    $(".selected-articles .article").each(function(){
        var $article = $(this);
        var id = $article.attr('data-article-id');
        $('#article-available-'+id).hide();
    });

    $('.available-articles').delegate('.article', 'click', function(){
        var $article = $(this).clone();
        $article.attr('id', 'article-selected-' + $article.attr('data-article-id'));
        $article.append('<input type="hidden" name="articles[]" value="' + $article.attr('data-article-id') + '">');

        $('.selected-articles').append($article);
        $(this).hide();

        $(".selected-articles").sortable();
    });

    $('.selected-articles').delegate('.article', 'click', function(){
        var $article = $(this);
        $('#article-available-' + $article.attr('data-article-id')).show();
        $article.remove();
    });

});
</script>

<style>
.summary-field textarea.form-control { height: 170px; }

.articles {
    height: 400px;
    overflow-y: scroll;
}

.article {
    padding-left: 12px !important;
    padding-right: 12px !important;
    white-space: normal !important;
    text-align: left !important;
}
</style>        
@endsection