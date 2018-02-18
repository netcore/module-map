'use strict';

$(() => {
    let dataTable = $('#maps-datatable').dataTable();
    let wrapper = dataTable.parent().parent();

    wrapper.find('.table-caption').html('Maps');
    wrapper.find('input[type=search]').attr('placeholder', 'Find map...');
});