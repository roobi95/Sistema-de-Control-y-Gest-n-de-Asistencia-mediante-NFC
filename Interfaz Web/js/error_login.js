$(document).ready(function(){
    // Espera a que el documento esté listo
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    $('#errorDiv').text(error ? error : "");
});
