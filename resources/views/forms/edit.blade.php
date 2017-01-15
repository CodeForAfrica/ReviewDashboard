@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h3 class="page-header">{{ $page['title'] }}</h3>
                <form action="/form" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control input-hg" name="title" placeholder="Form Title" value="{{ $form->title or '' }}">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="description" placeholder="Form Description" rows="3">{{ $form->description or '' }}</textarea>
                    </div>
                    <div class="form-group">
                        {{-- TODO: Use Google Picker API (https://goo.gl/Z9rzcv)--}}
                        <label for="url">Responses URL (Google Sheets):
                            <span class="refresh-form" data-toggle="tooltip" data-placement="top" title="Will NOT refresh responses data">
                                <i class="fa fa-dot-circle-o"></i>
                            </span>
                        </label>
                        <input type="text" class="form-control" name="responses_url" placeholder="https://docs.google.com/spreadsheets/d/13Wxta0N3WrKMUp2XeVj0L6l_V6beGo4OQ8PSM_SHCU4/edit#gid=0" value="{{ $form->responses_url or '' }}">
                    </div>
                    <hr/>
                    <div class="text-left">
                        <a onclick="{{ isset($form) ? 'javascript:updateForm('.$form->id.');' : 'javascript:$(\'form\').submit();' }}" class="btn btn-primary btn-wide mrm">Save</a>
                        <a href="{{ $page['cancel_link'] }}" class="btn btn-default btn-wide">Cancel</a>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div> <!-- /.col-md-8 -->
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">

        // On click, it will queue for download
        var refresh_form = 0;
        var refresh_form_force = 0;
        $('[name="responses_url"]').focus(function () { refreshForm(); });

        var responses_url_old = '{{ $form->responses_url or '' }}';

        function updateForm(id) {

            // TODO: Check if already running (in case of "Save" clicked twice)
            // TODO: Disable save button

            $.ajax({
                type: "PATCH",
                data: {
                    'title': $("[name='title']").val(),
                    'description': $("[name='description']").val(),
                    'responses_url': $("[name='responses_url']").val(),
                    'refresh_form': refresh_form,
                    '_token': '{{ csrf_token() }}'
                },
                url: '/form/' + id,
                success: function() {
                    window.location = '/form/' + id;
                }
            });
        }

        function refreshForm(){
            if (responses_url_old !== $("[name='responses_url']").val()){
                refresh_form_force = 1;
            } else {
                refresh_form_force = 0;
            }
            if(refresh_form == 0 || refresh_form_force == 1){
                refresh_form = 1;
                $('.refresh-form').html('<i class="fa fa-download"></i>');
                $('.refresh-form').attr('title', 'Will refresh responses data').tooltip('fixTitle');
            } else {
                refresh_form = 0;
                $('.refresh-form').html('<i class="fa fa-dot-circle-o"></i>');
                $('.refresh-form').attr('title', 'Will NOT refresh responses data').tooltip('fixTitle');
            };
        };
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
