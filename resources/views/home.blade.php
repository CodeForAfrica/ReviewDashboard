@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-offset-2">
            <div class="page-header">
                <h1>
                    Forms
                    <a href="form/create" class="btn btn-primary btn-wide pull-right" style="margin-top:17px;">
                        <i class="fa fa-btn fa-plus"></i> New Form
                    </a>
                </h1>
            </div>
            @if(count($forms) == 0)
                <p class="lead text-center"><em>No forms here yet. <a href="{{ url('form/create') }}"><u>Add</u></a> one now.</em></p>
            @endif
            <div class="list-group">
                @foreach($forms as $key => $form)
                    <a href="/form/{{ $form->id }}" class="list-group-item">
                        <h4 class="list-group-item-heading">{{ $form->title }}</h4>
                        <p class="text-muted">
                            <small>Updated: {{ \Carbon\Carbon::parse($form->updated_at)->toRfc850String() }}</small>
                        </p>
                        <p class="list-group-item-text">{!! str_limit(nl2br($linkify->process($form->description)), 200) !!}</p>
                    </a>
                @endforeach
            </div>
        </div> <!-- /.col-md-8 -->
    </div>
</div>
@endsection
