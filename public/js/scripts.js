console.log('Salut')

$('form button[type="submit"]').click(function() {
    $(this).text('Chargement...').attr('disabled', true)
    $(this).closest('form').submit()
})

$(document).ready(function() {
    $('select').chosen()
})
