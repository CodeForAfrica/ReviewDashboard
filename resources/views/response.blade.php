@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-8" style="border-right: solid 1px #eee;">
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
            <div class="col-sm-4">
                <div data-spy="affix" data-offset-top="0" style="width: inherit;">
                    <div style="padding-right: 30px;">
                        <div class="page-header">
                            <h4>Review Panel</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
@endsection
