@extends('layouts.app')

@section('content')
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse" style="top: 53px;;">
        <div class="container-fluid">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/response/'.$prev_id) }}">
                        <i class="fa fa-arrow-left fa-btn"></i> Previous Application
                    </a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ url('/response/'.$next_id) }}">
                        Next Application <i class="fa fa-arrow-right fa-btn"></i>
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="height: 100%;">

        <div class="row" style="height: 100%;">
            <div class="col-sm-8" style="border-right: solid 1px #eee;" id="left">
                <div class="page-header">
                    <h4>Response</h4>
                </div>
                @foreach( $form->responses_headers as $index => $header)
                    <p><strong>{{ $header }}</strong></p>
                    <p>
                        @if(count(count_chars($response->data[$index], 1)) == 0)
                            -
                        @else
                            {{ $response->data[$index] }}
                        @endif
                    </p>
                @endforeach
            </div>
            <div class="col-sm-4" id="right">
                <div class="page-header">
                    <h4>Review Panel</h4>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
@endsection

@section('styles')
    <style>
        body {
            padding-top: 106px;
        }
        body, html {
            margin: 0;
            overflow: hidden;
            height:100%;
        }
        #left, #right{
            height:100%;
            overflow-y: scroll;
        }
    </style>
@endsection
