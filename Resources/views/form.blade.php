@extends('admin::layouts.master')

@section('content')

    {{-- Breadcrumbs::render('admin.maps.index') --}}

    <div class="page-header">
        <h1>
            <span class="text-muted font-weight-light">
                <i class="page-header-icon fa fa-map"></i> Maps
            </span>
        </h1>

        <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
            <a href="{{ route('map::maps.index') }}" class="btn btn-primary btn-block">
                <span class="btn-label-icon left fa fa-times"></span> Back to list
            </a>
        </div>
    </div>

    @php
        $key = setting()->maps()->get('api_key');
    @endphp

    <div id="maps-app">
        <map-form
                route="{{ isset($map) ? route('map::maps.update', $map) : route('map::maps.store')}}"
                :map-model="{{ isset($map) ? $map->format()->forVue() : 'null' }}"
                api-key="{{ $key }}">
        </map-form>
    </div>

@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $key }}"></script>
    <script src="{{ versionedAsset('assets/map/js/form.js') }}"></script>
@endsection