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
                        <label for="url">Responses URL (Google Sheets):</label>
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
        function updateForm(id) {

            // TODO: Check if already running (in case of "Save" clicked twice)
            // TODO: Disable save button

            $.ajax({
                type: "PATCH",
                data: {
                    'title': $("[name='title").val(),
                    'description': $("[name='description").val(),
                    'responses_url': $("[name='responses_url").val(),
                    '_token': '{{ csrf_token() }}'
                },
                url: '/form/' + id, //resource
                success: function(affectedRows) {
                    if (affectedRows > 0) window.location = '/form/'+id;
                }
            });
        }
    </script>
@endsection
