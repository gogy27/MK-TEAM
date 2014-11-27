$(function () {
    $('.base-number-format-input').keyup(function () {
        $(this).parent('.nowrap').siblings('.nowrap').find('.base-number-format').text($(this).val());
    });
});
