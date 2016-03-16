@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Forms</div>

                <div class="panel-body">
                    <a href="form/create" class="btn btn-primary btn-wide"><i class="fa fa-btn fa-plus"></i> New Form</a>
                </div>
                <div class="list-group">
                    @foreach($forms as $form)
                        <a href="/form/{{ $form->id }}" class="list-group-item">{{ $form->title }}</a>
                    @endforeach
                </div>
            </div> <!-- /.panel -->
        </div> <!-- /.col-md-4 -->
        <div class="col-md-8">

        </div>
    </div>
</div>
@endsection
