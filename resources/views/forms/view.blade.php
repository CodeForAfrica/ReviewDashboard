@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-1">
                <div class="page-header">
                    <h1>{{ $form->title }}</h1>
                </div>
                <div class="row">
                    <div class="col-sm-9">
                        <p>{{ $form->description }}</p>
                    </div>
                    <div class="col-sm-3">
                        <a class="btn btn-block btn-info" href="/form/{{ $form->id }}/edit">
                            <i class="fa fa-btn fa-pencil"></i>
                            Edit
                        </a>
                        <p></p>
                        <a href="javascript:deleteForm('{{ $form->id }}');" class="btn btn-danger btn-block">
                            <i class="fa fa-btn fa-trash"></i>
                            Delete
                        </a>
                    </div> <!-- /.col-sm-3 -->
                </div> <!-- /.row -->
                <hr/>
                <h4>Responses</h4>
                <p>Where you find it.</p>
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
