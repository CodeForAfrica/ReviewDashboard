@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="page-header">
                    <h1>{{ $form->title }}</h1>
                </div>
                <div class="row">
                    <div class="col-sm-10">
                        <p>{{ $form->description }}</p>
                    </div>
                    <div class="col-sm-2 text-left">
                        <p>
                            <a href="/form/{{ $form->id }}/edit" class="btn btn-block btn-sm btn-info">
                                <i class="fa fa-btn fa-pencil"></i>
                                Edit
                            </a>
                        </p>
                        <p>
                            <a href="javascript:deleteForm('{{ $form->id }}');" class="btn btn-block btn-sm btn-danger">
                                <i class="fa fa-btn fa-trash"></i>
                                Delete
                            </a>
                        </p>
                    </div> <!-- /.col-sm-3 -->
                </div> <!-- /.row -->
                <hr/>

                <h4>Responses</h4>

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

                    Yo!

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
</script>
@endsection
