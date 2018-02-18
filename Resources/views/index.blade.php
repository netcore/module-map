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
            <a href="{{ route('map::maps.create') }}" class="btn btn-primary btn-block">
                <span class="btn-label-icon left fa fa-plus"></span> Create new map
            </a>
        </div>
    </div>

    <div class="table-primary">
        <table class="table table-bordered table-stripped" id="maps-datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Identifier</th>
                    <th>Polygons</th>
                    <th>Markers</th>
                    <th>Created at</th>
                    <th>Last modified at</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maps as $map)
                    <tr>
                        <td>{{ $map->id }}</td>
                        <td>{{ $map->identifier }}</td>
                        <td>{{ $map->polygons->count() }}</td>
                        <td>{{ $map->markers->count() }}</td>
                        <td>{{ $map->created_at->format('d.m.Y H:i:s') }}</td>
                        <td>{{ $map->updated_at->format('d.m.Y H:i:s') }}</td>
                        <td class="text-right">
                            <a href="{{ route('map::maps.edit', $map) }}" class="btn btn-xs btn-success">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <a href="{{ route('map::maps.destroy', $map) }}" class="btn btn-xs btn-danger delete-map">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/map/js/index.js') }}"></script>
@endsection