$(function () {
    $.nette.init();
    
    $('.base-number-format-input').keyup(function () {
        $(this).parent('.nowrap').siblings('.nowrap').find('.base-number-format').text($(this).val());
    });
    
    function netteJson(link) {
        $.getJSON(odkaz, function (payload) {
            if (payload.accepted) {
                // uspesny             
            }
            else {
                // neuspesny
            }
            $("#message").html(payload.message);

            for (var i in payload.snippets) {
                updateSnippet(i, payload.snippets[i]);
            }
        });
    }
    
    $('.remove-user').click(function(){
        e.preventDefault();
        alert($(this).attr('href'));
        $.getJSON($(this).attr('href'), function (payload) {
            if (payload.accepted) {
                // uspesny          
                alert('ide');
                $(this).parent('td').parent('tr').remove();
            }
            else {
                // neuspesny
                alert('nejde');
            }
        });
    });
    /*
    $.nette.ext('.remove-user', {
        success: function () {
            console.log('user removed');
        },
        error: function () {
            console.log('vyskytla sa chyba pri dostranovani.');
        }
    });
    */
});
