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

    <div class="container-fluid" style="height: 100%;">

        <div class="row" style="height: 100%;">
            <div class="col-sm-6 col-sm-offset-2" style="border-right: solid 1px #eee;" id="left">
                <h4 class="page-header">Response</h4>
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
            <div class="col-sm-4 bg-info" id="right">
                <div style="max-width: 350px;">
                    <h4 class="page-header">Review Panel</h4>

                    <p class="text-center" id="no-config" style="display: none;">
                        <em>There is nothing here yet. <a href="/form/{{ $form->id }}/ratings/config">Add a new config</a> to get started.</em>
                    </p>

                    <div class="reviews-panel"></div>
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
        .review {
            padding-bottom: 21px;
        }
    </style>
@endsection

@section('javascript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>
    <script type="text/x-handlebars-template" id="template-review">
        <div class="review" id="@{{ id }}">
            <h6>@{{ title }}</h6>
            <p><small>@{{ description }}</small></p>
            <div class="type @{{ type }}"></div>
        </div>
    </script>

    <script type="text/x-handlebars-template" id="template-review-type-text">
        <textarea class="form-control" name="review" placeholder="Write your review here." rows="3">@{{ review }}</textarea>
    </script>
    <script type="text/x-handlebars-template" id="template-review-type-numeric">
        <input type="number" class="form-control" name="review" placeholder="0" value="@{{ review }}">
    </script>
    <script type="text/x-handlebars-template" id="template-review-type-yes_no">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-info btn-wide @{{ review_yes }}">
                <input type="radio" name="review" id="option1" autocomplete="off" value="yes"> Yes
            </label>
            <label class="btn btn-info btn-wide @{{ review_no }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="no"> No
            </label>
        </div>
    </script>
    <script type="text/x-handlebars-template" id="template-review-type-stars">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-info @{{ review_1 }}">
                <input type="radio" name="review" id="option1" autocomplete="off" value="1"> 1
            </label>
            <label class="btn btn-info @{{ review_2 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="2"> 2
            </label>
            <label class="btn btn-info @{{ review_3 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="3"> 3
            </label>
            <label class="btn btn-info @{{ review_4 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="4"> 4
            </label>
            <label class="btn btn-info @{{ review_5 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="5"> 5
            </label>
        </div>
    </script>

    <script type="text/javascript">

        var source;
        var template;
        var html;

        function addConfig(index, config) {

            config.id = index;

            $('#no-config').hide();

            source   = $('#template-review').html();
            template = Handlebars.compile(source);
            html = template(config);
            $('.reviews-panel').append(html);

            switch (config.type) {
                case 'text':
                    source   = $('#template-review-type-text').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id).append(html);
                    break;
                case 'numeric':
                    source   = $('#template-review-type-numeric').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id).append(html);
                    break;
                case 'yes_no':
                    source   = $('#template-review-type-yes_no').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id).append(html);
                    break;
                case 'stars':
                    source   = $('#template-review-type-stars').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id).append(html);
                    break;
                default:
                    source   = $('#template-review-type-text').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id).append(html);
            }


        }


        $( document ).ready(function() {


            @if(count($form->ratings_config) == 0)
                $('#no-config').show();
            @else
                @foreach($form->ratings_config as $index => $config)
                    addConfig( {{ $index }}, {!! json_encode($config) !!} );
                @endforeach
            @endif

        });
    </script>
@endsection