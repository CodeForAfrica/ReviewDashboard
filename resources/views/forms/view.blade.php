@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h3 class="page-header">
                    {{ $form->title }}
                </h3>
                <div class="row">
                    @if( $role == 'Administrator' )
                        <div class="col-sm-6 col-md-8">
                    @else
                        <div class="col-sm-12">
                    @endif
                        <p class="text-muted"><small>
                        @if( $role == 'Administrator' )
                            <strong><em>Source:</em></strong> <a href="{{ $form->responses_url }}" target="_blank">
                                        Google Sheets <i class="fa fa-external-link"></i></a><br/>
                        @endif
                            <strong><em>Updated:</em></strong> {{ \Carbon\Carbon::parse($form->updated_at)->toRfc850String() }}
                        </small></p>
                        <p>{!! nl2br($linkify->process($form->description)) !!}</p>
                    </div>
                    @if( $role == 'Administrator' )
                        <div class="col-sm-6 col-md-4">
                            <div class="row">
                                <div class="col-sm-6 text-left">
                                    <p>
                                        <a href="/form/{{ $form->id }}/share" class="btn btn-block btn-sm btn-info">
                                            <i class="fa fa-btn fa-users"></i> Share
                                        </a>
                                    </p>
                                    @if( $form->import_status == 2 )
                                        <p>
                                            <a href="/form/{{ $form->id }}/ratings/config" class="btn btn-block btn-sm btn-primary">
                                                <i class="fa fa-btn fa-cogs"></i> Review Config
                                            </a>
                                        </p>
                                    @endif
                                </div> <!-- /.col-sm-2 -->
                                <div class="col-sm-6 text-left">
                                    <p>
                                        <a href="/form/{{ $form->id }}/edit" class="btn btn-block btn-sm btn-warning">
                                            <i class="fa fa-btn fa-pencil"></i> Edit
                                        </a>
                                    </p>
                                    <p>
                                        <a href="javascript:deleteForm('{{ $form->id }}');" class="btn btn-block btn-sm btn-danger">
                                            <i class="fa fa-btn fa-trash"></i> Delete
                                        </a>
                                    </p>
                                </div> <!-- /.col-sm-2 -->
                            </div> <!-- /.row -->
                        </div> <!-- /.col-sm-4 -->


                    @endif
                </div> <!-- /.row -->
                <hr/>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#responses" aria-controls="home" role="tab" data-toggle="tab">Responses</a></li>
                </ul>
                @if( $form->import_status == 1 )

                    <div class="alert alert-info text-center" role="alert">
                        <p class="lead"><i class="fa fa-spin fa-spinner"></i> Importing responses...</p>
                        <p><small>This page shall <a href="javascript:window.location=''">reload</a> automatically once done.</small></p>
                    </div>

                    {{-- TODO: Make actual automatic reload. --}}

                @elseif( $form->import_status == 3 )

                    <div class="alert alert-danger text-center" role="alert">
                        <p class="lead"><i class="fa fa-exclamation-triangle"></i> Oops... Import error.</p>
                        <p><small>Consider <a href="/form/{{ $form->id }}/edit">editing</a> your URL.</small></p>
                    </div>

                @elseif( $form->import_status == 2 )

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="responses">
                            <form class="form-inline">
                                <div class="form-group">
                                    {{-- TODO: Add search field --}}
                                </div>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>
                                            <label class="checkbox" style="margin-top: -20px;">
                                                <input type="checkbox" data-toggle="checkbox">
                                            </label>
                                        </th>
                                        <th>{{ $form->responses_headers[$brief_field] }}</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $responses as $index => $response )
                                        <tr class="clickable-row" data-href="{{ url('/response/'.$response->id ) }}">
                                            <td>
                                                <label class="checkbox" style="margin-top: -20px;">
                                                    <input type="checkbox" data-toggle="checkbox">
                                                </label>
                                            </td>
                                            <td>{{ $response->data[$brief_field] }}</td>
                                            <td class="text-center">
                                                @if($response->reviews($user))
                                                    @if($response->reviews($user)->is_complete())
                                                        Done
                                                    @else
                                                        Not
                                                    @endif
                                                @else
                                                    Not
                                                @endif
                                                {{-- TODO: Check status of the response; whether reviewed et. al. --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>

                            <div class="text-center">
                                {{ $responses->links() }}
                            </div>
                        </div> <!-- #responses -->

                    </div>

                @endif

            </div> <!-- /.col-md-8 -->
        </div>
    </div>
@endsection

@section('javascript')
<script type="text/javascript">
    function deleteForm(id) {
        if (confirm('Delete this form?')) {
            $.ajax({
                type: "DELETE",
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                url: '/form/' + id, //resource
                success: function(affectedRows) {
                    if (affectedRows > 0) window.location = '/home';
                }
            });
        }
    }

    jQuery(document).ready(function($) {
        $('.clickable-row').click(function() {
            window.document.location = $(this).data('href');
        });
    });
</script>
@endsection
