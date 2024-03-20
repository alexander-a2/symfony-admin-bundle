$(document).ready(function () {
    $('input[data-date-picker]').datetimepicker({
        timepicker: false,
        format: 'Y-m-d',
    });
    $('select[data-selectpicker]').selectpicker({
    })
    $('select[data-selectpicker-with-search]').selectpicker({
        liveSearch: true,
    })
});