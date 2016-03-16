@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="page-header">
                    <h3>Profile</h3>
                </div>
                <form>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label text-right">Name:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" placeholder="Name" value="{{ $user->name }}">
                        </div>
                    </div>
                    <br/> <br/>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label text-right">Email:</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="email" placeholder="Email" value="{{ $user->email }}">
                        </div>
                    </div>
                    <br/> <br/>
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <a class="btn btn-danger btn-wide" href="{{ url('/auth/google/redirect') }}">
                                @if($user->google_token == null)
                                    Connect Google
                                @else
                                    Reconnect Google
                                @endif
                            </a>
                        </div>
                    </div>
                    <br/>
                    <hr/>
                    <div class="text-left">
                        <button type="submit" class="btn btn-primary btn-wide mrm">Save</button>
                        <a href="/home" class="btn btn-default btn-wide">Cancel</a>
                    </div>
                </form>
            </div> <!-- /.col-md-4 -->
        </div>
    </div>
@endsection
