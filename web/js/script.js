$(function () {
    $( "input.js-datepicker" ).datepicker({
        startDate: new Date(),
        endDate: "+12m",
        language: "fr",
        ignoreReadonly: true,
        daysOfWeekDisabled: "0,2",
        dateFormat: "dd-mm-yy"});
    $('input.datepicker_birth').each(function () {
        $(this).after('<p class="help-text">Format demandé : 25/12/1985</p>');
    })
});

$(function () {
    $( "input.datepicker-js" ).datepicker({
        endDate: new Date(),
        language: "fr",
        ignoreReadonly: true,
        startView: 2,
        dateFormat: "dd-mm-yy"});
    $('input.datepicker_birth').each(function () {
        $(this).after('<p class="help-text">Format demandé : 25/12/1985</p>');
    })
});
