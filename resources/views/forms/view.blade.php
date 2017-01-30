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
                        <p  class="collapse" id="collapseDescription">{!! nl2br($linkify->process($form->description)) !!}</p>
                        <p><a data-toggle="collapse" href="#collapseDescription" aria-expanded="false" aria-controls="collapseExample">+ Description</a></p>
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
                    <li role="presentation" class="active"><a href="#summary" aria-controls="reviews-summary" role="tab" data-toggle="tab">Summary</a></li>
                    <li role="presentation"><a href="#not-reviewed" aria-controls="not-reviewed" role="tab" data-toggle="tab">Not Reviewed</a></li>
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

                        <div role="tabpanel" class="tab-pane active" id="summary">
                            <br/>

                            <table class="table table-hover table-bordered table-condensed" id="reviews-summary-table">
                                <thead>
                                <tr>
                                    <th><small>#</small></th>
                                    <th><small>{{ $form->responses_headers[$brief_field] }}</small></th>
                                    @foreach($form->ratings_config as $config)
                                        <th><small>{{ $config["title"] }}</small></th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($form->responses as $key => $response)
                                    <tr class="clickable-row" data-href="{{ url('/response/'.$response->id ) }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ url('/response/'.$response->id) }}" target="_blank">
                                                {{ $response->data[$brief_field] }}
                                            </a></td>
                                        @if( !$response->reviews($user) )
                                            @foreach($form->ratings_config as $key => $config)
                                                <td><small>-</small></td>
                                            @endforeach
                                        @else
                                            @foreach($form->ratings_config as $key => $config)
                                                @if($response->reviews($user)->feedback[$key])
                                                    <td>{{ $response->reviews($user)->feedback[$key] }}</td>
                                                @else
                                                    <td><small>-</small></td>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div role="tabpanel" class="tab-pane" id="not-reviewed">
                            <br/>

                            <p>These are links to the responses <strong>not yet</strong> reviewed:</p>
                            <ol>
                                @foreach( $form->responses($user)->reviewed_not_urls as $index => $url )
                                    <li><a href="{{ $url }}" target="_blank">{{ $url }}</a></li>
                                @endforeach
                            </ol>
                        </div>

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

        $('#reviews-summary-table').DataTable({
            scrollY: 400,
            paging: false
        });

    });

    // Javascript to enable link to tab
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        if(history.pushState) {
            history.pushState(null, null, e.target.hash);
        } else {
            window.location.hash = e.target.hash; //Polyfill for old browsers
        }
    });


</script>
@endsection
