@extends(config('enews.layout'))

@section(config('enews.layout_section'))

<div class="container">

        @include('enews::bootstrap3.alerts')

        <div class="panel panel-default">
            <div class="panel-heading"
                    style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center;">
                <h3 class="panel-title">Email Preview</h3>

                <!-- <div>
                    <a href="{{ route('jzpeepz.enews.create') }}" class="btn btn-success btn-xs">Create Enews</a>
                </div> -->
            </div>
                
            <div class="panel-body">
                
    
                <form action="{{ route('jzpeepz.enews.send', $email->id) }}" method="post" role="form">

                    {{ csrf_field() }}

                    <div class="row">
                        <div class="form-group col-lg-3">
                            <label for="list_id">List</label>
                            <select name="list_id" id="list_id" class="form-control" onchange="$(this).val() == '{{ array_keys($lists)[0] }}' ? $('#testEmails').show() : $('#testEmails').val('').hide();">
                                @foreach ($lists as $listId => $listLabel)
                                <option value="{{ $listId }}">{{ $listLabel }}</option>
                                @endforeach
                            </select>
                            <div style="margin-top: 10px;">
                                <input type="text" id="testEmails" name="testEmails" class="form-control" placeholder="Custom test emails (comma separated)">
                            </div>
                        </div>
            
                        <div class="form-group col-lg-3">
                            <label for="">When <sup>**</sup></label>
                            <input type="text" id="scheduled_for" name="scheduled_for" class="form-control enews-datetimepicker" value="" placeholder="Leave this blank to send immediately" tabindex="3">
                        </div>
                    </div>
            
                    <!-- <div class="form-group">
                        <label for="email">Or</label>
                        <input type="text" id="email" name="email" class="form-control" placeholder="comma separated emails" style="width: 200px;">
                    </div> -->
            
                    <input type="hidden" name="id" value="{{ $email->id }}">
            
                    <button class="btn btn-primary" onclick="return confirm('Are you sure you are ready to send?');">Send</button>
                    <a href="{{ route('jzpeepz.enews.edit', $email->id) }}" class="btn btn-default">Edit</a>
                    @if (! empty($email->campaign_id))
                    <a href="https://app.adestra.com/Abpg/campaign/{{ $email->campaign_id }}/view" class="btn btn-default" target="_blank">View in Adestra</a>
                    @endif
                </form>
            
                <!-- <div class="alert alert-info" style="margin-top: 15px;">
                    <p><sup>**</sup> <strong>This feature is BETA. Please verify that scheduled emails are sent at the appropriate time until further notice.</strong> Future dated emails are handed off to Streamsend to send at a later date and time.</p>
                    <p>If you need to cancel to scheduled email, contact Jonathan (jonathan@flex360.com) or Robert (robert@flex360.com). Scheduled emails can also be cancelled by accessing the Streamsend account if you have such privileges.</p>
                    <p><strong>Editing an email after it is scheduled WILL NOT change the content of the email that is sent.</strong></p>
                </div> -->
            
                <div style="margin-top: 20px; font-size: 18px;">
                    <strong>Subject:</strong> <?=$email->subject?>
                </div>
            
                <div style="margin-top: 20px; font-size: 18px;">
                    <strong>Preview Text:</strong> <?=$email->preview_text?>
                </div>

            </div>
        
        </div>
    
        <iframe src="{{ route('jzpeepz.enews.html', $email->id) }}" frameborder="1" style="width: 100%; min-width: 320px; height: 2000px; margin-top: 20px;"></iframe>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
    $( ".enews-datetimepicker" ).datetimepicker({
        dateFormat: 'mm/dd/yy',
        timeFormat: 'hh:mm tt',
        ampm: true
    });

    $('#scheduled_for').blur();
});
</script>
@endsection