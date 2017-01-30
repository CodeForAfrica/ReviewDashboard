@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-md-offset-2">
                <h3 class="page-header">
                    <a href="/form/{{ $form->id }}">{{ $form->title }}</a>
                    <small>. {{ $page['title'] }}</small></h3>
                <div class="row">
                    <div class="col-sm-9">
                        <p class="lead">Collaborators on this project</p>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-info btn-block" data-toggle="modal" data-target="#modal-invite">Invite others</button>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"> Select all
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-9 text-right">
                                <button class="btn btn-link" onclick="removeUsers();">REMOVE</button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        SET PERMISSIONS <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Viewer</a></li>
                                        <li><a href="#">Reviewer</a></li>
                                        <li><a href="#">Administrator</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-list list-group"></div>
                </div>

            </div> <!-- /.col-sm-8 -->
        </div>
    </div>

    <div class="modal fade" id="modal-invite" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/form/{{ $form->id }}/share" method="post" class="form-horizontal">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Invite someone else to this project</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Name:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">E-Mail Address:</label>
                            <div class="col-sm-8">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}"required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Permissions:</label>
                            <div class="col-sm-8">
                                <select name="role_id" class="form-control select select-primary select-block mbl">
                                    <option value="2">Reviewer</option>
                                    <option value="3">Viewer</option>
                                    <option value="1">Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-wide">Add</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('styles')

@endsection

@section('javascript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>

    <script type="text/x-handlebars-template" id="template-user">
        <a href="#" class="list-group-item user-single">
            <div class="checkbox">
                <label>
                    <input type="checkbox" data-user-id="@{{ id }}">
                    <h5 class="list-group-item-heading">@{{ name }} <small>@{{ role }}</small></h5>
                    <p class="list-group-item-text">
                        @{{ email }} <br/>
                        <small>@{{ reviews_done }} <small>out of @{{ responses_total }}</small> reviews done</small>
                    </p>
                </label>
            </div>
        </a>
    </script>

    <script type="text/javascript">
        var source;
        var template;
        var html;

        var users_selected = [];

        function showUser(index, user) {
            source   = $('#template-user').html();
            template = Handlebars.compile(source);
            html = template(user);
            $('.users-list').append(html);

            $(':checkbox').radiocheck();
        }
        
        function removeUsers() {
            var payload = {
                '_token': '{{ csrf_token() }}',
                'users' : users_selected
            };

            $.ajax({
                type: "DELETE",
                data: payload,
                url: '/form/{{ $form->id }}/share',
                always: function( worked ) {
                    window.location = '/form/{{ $form->id }}/share';
                }
            });
        }

        $( document ).ready(function() {

            @foreach($users as $index => $user)
                showUser( {{ $index }}, {!! json_encode($user) !!} );
            @endforeach

            $(':checkbox').on('change.radiocheck', function() {
                var count_before = users_selected.length;
                var user_selected = $(this).attr('data-user-id');

                users_selected = jQuery.grep(users_selected, function(value) {
                    return value != user_selected;
                });

                if (count_before == users_selected.length){
                    users_selected.push(user_selected);
                }
            });

        });


    </script>
@endsection
