+function() {
    $('a.read-more').on('click', function(e) {
        e.preventDefault();
        $(this).parent().find('.read-more')[$(this).hasClass('in') ? 'removeClass' : 'addClass']('in');
    })

    $('.pwstrength').pwstrength({bootstrap3: true, usernameField: '#signup input[name=email]'});
    
    setTimeout(function() {
        $('.alert-fixed-top').fadeOut();
    }, 3000);
}();

// Password reset
+function() {
    function confirmPassword() {
        var form = $(this).is('form') ? this : this.form;
        var password         = $(form).find('input[name=password]');
        var password_confirm = $(form).find('input[name=password-confirm]');
        
        if (password.val() && password_confirm.val() && password.val() !== password_confirm.val()) {
            password_confirm[0].setCustomValidity('Passwords should match');
        } else {
            password_confirm[0].setCustomValidity('');
        }
    }
    
    $('#reset-password :password').on('change', confirmPassword);
}();

$(function() {
    if (location.href.match(/#require-login/)) $('#login').modal('show');
    $('.background.carousel').carousel({ pause: 'no', interval: 5000 }).addClass('running');
});
