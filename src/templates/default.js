hljs.initHighlightingOnLoad();
jQuery(document).ready(function(){
    $('.alert').on('click', function(e){
        $(this).parent().find('.explain').toggleClass('hide');
    });

    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
});
