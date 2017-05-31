@extends('layouts.frontend')
@push('styles')
    {{ Html::style(asset('frontend/css/jquery.mCustomScrollbar.min.css')) }}
    {{ Html::style(asset('frontend/css/swiper.min.css')) }}
    {{ Html::style(asset('frontend/css/magnific-popup.css')) }}
    {{ Html::style(asset('frontend/css/mediaelementplayer.css')) }}
    {{ Html::style(asset('frontend/css/mediaelement-playlist-plugin.min.css')) }}
@endpush
@section('main')
<div id="app">
    <master-component></master-component>
</div>
@endsection()
@push('scripts')
    {{ Html::script(asset('frontend/js/jquery.magnific-popup.min.js')) }}
    {{ Html::script(asset('frontend/js/swiper.jquery.min.js')) }}
    {{ Html::script(asset('frontend/js/mediaelement-and-player.min.js')) }}
    {{ Html::script(asset('frontend/js/mediaelement-playlist-plugin.min.js')) }}
@endpush
