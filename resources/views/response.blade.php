@extends('layouts.app')

@section('content')
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse" style="top: 53px; z-index: 20;">
        <div class="container-fluid">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="javascript:submitReview('prev')">
                    <i class="fa fa-arrow-left fa-btn"></i> Previous Application
                </a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="javascript:submitReview('next')">
                    Next Application <i class="fa fa-arrow-right fa-btn"></i>
                </a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid" style="height: 100%;">

        <div class="row" style="height: 100%;">
            <div class="col-sm-8 col-md-7 col-md-offset-1 col-lg-6 col-lg-offset-2" style="border-right: solid 1px #eee;" id="left">
                <h4 class="page-header">Response <small style="color: gray;">. Form: <a href="/form/{{ $response->form->id }}">{{ $response->form->title }}</a> </small></h4>
                @foreach( $form->responses_headers as $index => $header)
                    @if($header == 'WHY impactAFRICA SHORTLISTED THIS PROJECT:')
                        <br/>
                        <div class="alert alert-info">
                    @endif
                    <p><strong>{{ $header }}</strong></p>
                    <p>
                        @if(count(count_chars($response->data[$index], 1)) == 0)
                            -
                        @else
                            {!! nl2br($response->data[$index]) !!}
                        @endif
                    </p>
                    @if($header == 'WHY impactAFRICA SHORTLISTED THIS PROJECT:')
                        </div>
                    @endif
                @endforeach
                <p class="text-center text-muted"><smal>~ fin ~</smal></p>
            </div>
            <div class="col-sm-4 bg-info" id="right">
                <div style="max-width: 350px; padding-bottom: 35px;">
                    <h4 class="page-header">Review Panel</h4>

                    <p class="text-center" id="no-config" style="display: none;">
                        <em>There is nothing here yet. <a href="/form/{{ $form->id }}/ratings/config">Add a new config</a> to get started.</em>
                    </p>

                    <div class="reviews-panel"></div>

                    <hr/>
                    <div class="text-right">
                        <a href="/form/{{ $response->form->id }}" class="btn btn-default btn-lg">Cancel</a>
                        <i style="width: 7px; height: 1px;" class="fa"> </i>
                        <a href="javascript:submitReview('next');" class="btn btn-primary btn-lg btn-wide">Save</a>
                    </div>

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
            <div class="type" data-review-type="@{{ type }}"></div>
        </div>
    </script>

    <script type="text/x-handlebars-template" id="template-review-type-text">
        <textarea class="form-control" name="review" placeholder="Write your feedback here." rows="3">@{{ feedback }}</textarea>
    </script>
    <script type="text/x-handlebars-template" id="template-review-type-numeric">
        <input type="number" class="form-control" name="review" placeholder="0" value="@{{ feedback }}">
    </script>
    <script type="text/x-handlebars-template" id="template-review-type-yes_no">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-info btn-wide @{{ feedback_yes }}">
                <input type="radio" name="review" id="option1" autocomplete="off" value="yes"> Yes
            </label>
            <label class="btn btn-info btn-wide @{{ feedback_no }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="no"> No
            </label>
        </div>
    </script>
    <script type="text/x-handlebars-template" id="template-review-type-stars">
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-info @{{ feedback_1 }}">
                <input type="radio" name="review" id="option1" autocomplete="off" value="1"> 1
            </label>
            <label class="btn btn-info @{{ feedback_2 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="2"> 2
            </label>
            <label class="btn btn-info @{{ feedback_3 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="3"> 3
            </label>
            <label class="btn btn-info @{{ feedback_4 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="4"> 4
            </label>
            <label class="btn btn-info @{{ feedback_5 }}">
                <input type="radio" name="review" id="option2" autocomplete="off" value="5"> 5
            </label>
        </div>
    </script>

    <script type="text/javascript">

        var source;
        var template;
        var html;

        var feedback = [];
        
        function submitReview( after ) {
            var payload = {
                '_token': '{{ csrf_token() }}',
                'reviews': []
            };

            $('.review .type').each(function ( index ) {

                payload.reviews[index] = $( this ).find('input').val();
                switch ($( this ).attr('data-review-type')) {
                    case 'text':
                        payload.reviews[index] = $( this ).find('textarea').val();
                        break;
                    case 'numeric':
                        payload.reviews[index] = $( this ).find('input').val();
                        break;
                    case 'yes_no':
                        payload.reviews[index] = $( this ).find('.active input').val();
                        break;
                    case 'stars':
                        payload.reviews[index] = $( this ).find('.active input').val();
                        break;
                    default:
                        payload.reviews[index] = $( this ).find('textarea').val();
                }
            });


            $.ajax({
                type: "PUT",
                data: payload,
                url: '/response/{{ $response->id }}',
                success: function( worked ) {
                    if (after == 'prev') {
                        window.location = '{{ url('/response/'.$prev_id) }}';
                    } else {
                        window.location = '{{ url('/response/'.$next_id) }}';
                    }
                }
            });
        }

        function addConfig(index, config) {

            config.id = index;
            if (feedback[index] == null){
                feedback[index] = '';
            }
            config.feedback = feedback[index];

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
                    $('.reviews-panel .review#' + config.id + ' .type').append(html);
                    break;
                case 'numeric':
                    source   = $('#template-review-type-numeric').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id+' .type').append(html);
                    break;
                case 'yes_no':
                    switch (config.feedback) {
                        case 'yes':
                            config.feedback_yes = 'active';
                            break;
                        case 'no':
                            config.feedback_no = 'active';
                            break;
                    }
                    source   = $('#template-review-type-yes_no').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id+' .type').append(html);
                    break;
                case 'stars':
                    switch (parseInt(config.feedback)) {
                        case 1:
                            config.feedback_1 = 'active';
                            break;
                        case 2:
                            config.feedback_2 = 'active';
                            break;
                        case 3:
                            config.feedback_3 = 'active';
                            break;
                        case 4:
                            config.feedback_4 = 'active';
                            break;
                        case 5:
                            config.feedback_5 = 'active';
                            break;
                    }
                    source   = $('#template-review-type-stars').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id+' .type').append(html);
                    break;
                default:
                    source   = $('#template-review-type-text').html();
                    template = Handlebars.compile(source);
                    html = template(config);
                    $('.reviews-panel .review#'+config.id+' .type').append(html);
            }
            
        }


        $( document ).ready(function() {

            @if(count($form->ratings_config) == 0)
                $('#no-config').show();
            @else
                feedback = {!! json_encode( $review->feedback ) !!};
                if ( feedback == null) {
                    feedback = [];
                }
                @foreach($form->ratings_config as $index => $config)
                    addConfig( {{ $index }}, {!! json_encode($config) !!} );
                @endforeach
            @endif

        });
    </script>
@endsection