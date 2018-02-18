<template>
    <div class="marker-field">
        <div class="inputs-row">
            <div class="input-group">
                <span class="input-group-addon">Latitude:</span>
                <input type="number" class="form-control" v-model.number="marker.latitude">

                <span class="input-group-addon">Longitude:</span>
                <input type="number" class="form-control" v-model.number="marker.longitude">

                <div class="input-group-btn">
                    <button type="button" class="btn btn-default" @click="showAddressInput = !showAddressInput">
                        <i class="fa fa-search"></i> Lookup by address
                    </button>
                </div>
            </div>

            <div class="input-group m-t-1" v-if="showAddressInput">
                <input type="text" class="form-control" placeholder="Enter address" v-model="marker.address"
                       @keyup.enter="findCoordinatesByAddress" :disabled="processing">

                <div class="input-group-btn">
                    <button type="button" class="btn btn-get-address" @click="findCoordinatesByAddress()"
                            :disabled="processing">
                        <i class="fa fa-globe"></i> Get address coords
                    </button>
                </div>
            </div>
        </div>

        <div class="button-row" v-if="isRemovable">
            <button type="button" class="btn btn-danger" @click="removeMarker()">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';

    export default {
        props: {
            marker: {
                required: true,
                type: Object
            },

            isRemovable: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                showAddressInput: false,
                processing: false
            };
        },

        methods: {
            removeMarker() {
                this.$emit('removeMarker');
            },

            findCoordinatesByAddress() {
                this.processing = true;

                if (_.isEmpty(this.marker.address) || this.marker.address.length < 3) {
                    return $.growl.error({
                        message: 'Address should contain at least 3 characters!'
                    });
                }

                let request = axios.get('https://maps.google.com/maps/api/geocode/json', {
                    params: {
                        address: this.marker.address,
                        sensor: false,
                        key: this.$parent.apiKey
                    }
                });

                request.then(({data: response}) => {
                    if (response.status === 'ZERO_RESULTS') {
                        return $.growl.error({
                            message: 'Nothing found!'
                        });
                    } else if (response.status !== 'OK') {
                        return $.growl.error({
                            message: 'Unknown Google Geocode service error!'
                        });
                    }

                    this.marker.latitude = response.results[0].geometry.location.lat;
                    this.marker.longitude = response.results[0].geometry.location.lng;

                    this.showAddressInput = false;
                }).then(() => {
                    this.processing = false;
                });
            }
        }
    };
</script>