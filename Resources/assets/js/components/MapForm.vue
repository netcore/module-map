<template>
    <div class="panel panel-default" :class="{ 'form-loading' : isLoading }">
        <div class="panel-heading">
            <span class="panel-title">{{ map ? 'Edit' : 'Create' }} map</span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel-group" id="map-accordion">

                        <!-- Settings collapse -->
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="settingsHeading">
                                <a class="accordion-toggle collapsed" data-toggle="collapse"
                                   data-parent="#map-accordion" href="#settingsCollapse">
                                    <i class="fa fa-cogs"></i> &nbsp; Map settings
                                </a>
                            </div>
                            <div id="settingsCollapse" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="identifier">Map identifier:</label>
                                        <input type="text" id="identifier" class="form-control"
                                               v-model="map.identifier">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Map center:</label>
                                        <map-coordinates-row :marker="map"></map-coordinates-row>
                                    </div>

                                    <div class="form-group m-b-0">
                                        <label for="map-zoom">Map zoom:</label>
                                        <input id="map-zoom" type="number" data-slider-min="1" data-slider-max="20"
                                               data-slider-step="1" :data-slider-value="map.zoom">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Markers collapse -->
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="markersHeading">
                                <a class="accordion-toggle collapsed" data-toggle="collapse"
                                   data-parent="#map-accordion" href="#markersCollapse">
                                    <i class="fa fa-map-marker"></i> &nbsp; Markers ({{ map.markers.length }})
                                </a>
                            </div>
                            <div id="markersCollapse" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="button m-b-1 text-right">
                                        <button type="button" class="btn btn-xs btn-success" @click="addMarker()">
                                            <i class="fa fa-plus"></i> Add marker
                                        </button>
                                    </div>

                                    <map-coordinates-row
                                            :marker="marker"
                                            :key="marker.key"
                                            :is-removable="true"
                                            v-for="marker in map.markers"
                                            @removeMarker="removeMarker(marker)">
                                    </map-coordinates-row>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="polygonsHeading">
                                <a class="accordion-toggle collapsed" data-toggle="collapse"
                                   data-parent="#map-accordion" href="#polygonsCollapse">
                                    <i class="fa fa-map-signs"></i> &nbsp; Polygons ({{ map.polygons.length }})
                                </a>
                            </div>
                            <div id="polygonsCollapse" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="m-b-1 text-right">
                                        <button type="button" class="btn btn-xs btn-success" @click="addPolygon">
                                            <i class="fa fa-plus"></i> Add polygon
                                        </button>
                                    </div>

                                    <div class="panel panel-default" v-for="polygon in map.polygons" :key="polygon.key">
                                        <div class="panel-heading">
                                            <div class="panel-heading-btn">
                                                <button type="button" class="btn btn-xs btn-success"
                                                        @click="addMarker(polygon)">
                                                    <i class="fa fa-plus"></i> Add marker
                                                </button>

                                                <button type="button" class="btn btn-xs btn-danger"
                                                        @click="removePolygon(polygon)">
                                                    <i class="fa fa-trash"></i> Remove polygon
                                                </button>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <map-coordinates-row
                                                    :key="marker.key"
                                                    :marker="marker"
                                                    :is-removable="true"
                                                    v-for="marker in polygon.markers"
                                                    @removeMarker="removeMarker(marker, polygon)">
                                            </map-coordinates-row>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div :id="mapId" class="preview-map"></div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <button type="button" class="btn btn-success" @click="saveMap()">
                <i class="fa fa-check"></i> Save
            </button>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';

    export default {
        /**
         * Component props.
         */
        props: {
            route: {
                required: true,
                type: String
            },

            mapModel: {
                required: false,
                type: Object
            },

            apiKey: {
                required: true,
                type: String
            }
        },

        /**
         * Component data.
         */
        data() {
            return {
                map: {
                    identifier: '',
                    latitude: 56.9496487,
                    longitude: 24.1051865,
                    address: 'Riga',
                    zoom: 10,
                    markers: [],
                    polygons: []
                },

                isLoading: false,
                mapInstance: null,
                slider: null,

                mapEntries: []
            };
        },

        /**
         * Computed properties.
         */
        computed: {
            mapId() {
                return `map-${this._uid}`;
            }
        },

        /**
         * Created event.
         */
        created() {
            if (this.mapModel) {
                this.map = this.mapModel;
            }
        },

        /**
         * Mounted event.
         */
        mounted() {
            this.initMapPreview();
            this.initPlugins();
        },

        /**
         * Component methods.
         */
        methods: {
            /**
             * Initialize Google Map.
             */
            initMapPreview() {
                this.mapInstance = new google.maps.Map(document.getElementById(this.mapId), {
                    zoom: this.map.zoom,
                    zoomControl: false,
                    center: {
                        lat: this.map.latitude,
                        lng: this.map.longitude
                    }
                });
            },

            /**
             * Init other jQuery plugins.
             */
            initPlugins() {
                this.slider = $('#map-zoom').slider();

                this.slider.on('change', e => {
                    this.map.zoom = parseInt($(e.target).val());
                });
            },

            /**
             * Add single marker or polygon marker entry.
             *
             * @param polygon
             */
            addMarker(polygon) {
                let container = polygon ? polygon.markers : this.map.markers;

                container.push({
                    key: this.randomString(),
                    latitude: null,
                    longitude: null,
                    address: ''
                });
            },

            /**
             * Remove single marker or polygon marker entry.
             *
             * @param marker
             * @param polygon
             */
            removeMarker(marker, polygon) {
                let container = polygon ? polygon.markers : this.map.markers;

                container.splice(
                    container.indexOf(marker), 1
                );
            },

            /**
             * Add polygon.
             */
            addPolygon() {
                this.map.polygons.push({
                    key: this.randomString(),
                    markers: []
                });
            },

            /**
             * Remove polygon.
             *
             * @param polygon
             */
            removePolygon(polygon) {
                this.map.polygons.splice(
                    this.map.polygons.indexOf(polygon), 1
                );
            },

            /**
             * Redraw map with markers and polygons.
             */
            drawMap() {
                _.each(this.mapEntries, entry => {
                    entry.instance.setMap(null);
                });

                this.mapEntries = [];

                // Draw single markers.
                _.each(this.map.markers, marker => {
                    if (!this.isValidCoords(marker.latitude, marker.longitude)) {
                        return;
                    }

                    let markerEntry = new google.maps.Marker({
                        position: {
                            lat: marker.latitude,
                            lng: marker.longitude
                        }
                    });

                    this.mapEntries.push({
                        type: 'marker',
                        instance: markerEntry
                    });

                    markerEntry.setMap(this.mapInstance);
                });

                // Draw polygons.
                _.each(this.map.polygons, polygon => {
                    let polygonMarkers = [];

                    _.each(polygon.markers, marker => {
                        if (this.isValidCoords(marker.latitude, marker.longitude)) {
                            polygonMarkers.push({
                                lat: marker.latitude,
                                lng: marker.longitude
                            });
                        }
                    });

                    if (polygonMarkers.length < 3) {
                        return;
                    }

                    let polygonEntry = new google.maps.Polygon({
                        paths: polygonMarkers,
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.7,
                        strokeWeight: 1,
                        fillColor: '#FF0000',
                        fillOpacity: 0.3
                    });

                    this.mapEntries.push({
                        type: 'polygon',
                        instance: polygonEntry
                    });

                    polygonEntry.setMap(this.mapInstance);
                });
            },

            /**
             * Validate given coordinates.
             *
             * @param latitude
             * @param longitude
             * @return {*}
             */
            isValidCoords(latitude, longitude) {
                let inRange = (min, number, max) => {
                    return _.isNumber(number) && (number >= min) && (number <= max);
                };

                return inRange(-90, latitude, 90) && inRange(-180, longitude, 180);
            },

            /**
             * Generate random string with requested length.
             *
             * @param length
             * @return {string}
             */
            randomString(length = 10) {
                let text = '';
                let possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

                for (let i = 0; i < length; i++) {
                    text += possible.charAt(Math.floor(Math.random() * possible.length));
                }

                return text;
            },

            /**
             * Save handler.
             */
            saveMap() {
                this.isLoading = true;

                let promise = axios({
                    url: this.route,
                    method: this.mapModel ? 'PUT' : 'POST',
                    data: this.map
                });

                promise
                    .then(res => {
                        if (res.data.redirect) {
                            window.location.replace(res.data.redirect);
                        }

                        if (res.data.success) {
                            $.growl.success({
                                message: res.data.success
                            });
                        }
                    })
                    .catch(err => {
                        if (!err.response || err.response.status !== 422) {
                            $.growl.error({
                                message: 'Whoops.. Server error!'
                            });

                            console.error(err.response || err);

                            return;
                        }

                        let errors = err.response.data.errors;
                        let error = errors[Object.keys(errors)[0]][0];

                        $.growl.error({
                            title: 'Validation error',
                            message: error
                        });
                    })
                    .then(() => {
                        this.isLoading = false;
                    });
            }
        },

        /**
         * Component data watchers.
         */
        watch: {
            // Map zoom level.
            'map.zoom'(zoom) {
                this.mapInstance.setZoom(zoom);
                this.slider.slider('setValue', zoom);
            },

            // Map center latitude.
            'map.latitude'() {
                if (!this.isValidCoords(this.map.latitude, this.map.longitude)) {
                    return;
                }

                this.mapInstance.setCenter({
                    lat: this.map.latitude,
                    lng: this.map.longitude
                });
            },

            // Map center longitude.
            'map.longitude'() {
                if (!this.isValidCoords(this.map.latitude, this.map.longitude)) {
                    return;
                }

                this.mapInstance.setCenter({
                    lat: this.map.latitude,
                    lng: this.map.longitude
                });
            },

            'map.markers': {
                deep: true,
                handler() {
                    this.drawMap();
                }
            },

            'map.polygons': {
                deep: true,
                handler() {
                    this.drawMap();
                }
            }
        },

        /**
         * Register child components.
         */
        components: {
            'map-coordinates-row': require('./MapCoordinatesRow.vue')
        }
    };
</script>

<style lang="scss">
    .preview-map {
        width: 100%;
        display: block;
        min-height: 500px;
        background: #ccc;
    }

    .marker-field {
        display: flex;
        background: #eee;
        padding: 10px;
        margin-bottom: 10px;

        .inputs-row {
            flex: 1;
        }

        .button-row {
            flex: 0 0 40px;
            text-align: right;
        }
    }
</style>