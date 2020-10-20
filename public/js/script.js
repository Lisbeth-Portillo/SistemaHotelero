function actualizar() { location.reload(); }
$("#registro").on('click', function() {
    var nombres = $('input[name="nombres"]').val();
    var apellidos = $('input[name="apellidos"]').val();
    var identificacion = $('input[name="identificacion"]').val();
    var identificacion_id = $('select[name="identificacion_id"]').val();
    var token = $('input[name="_token"]').val();
    actualizar();
    $.ajax({
        headers: { 'X-CSRF-TOKEN': token },
        method: 'POST',
        dataType: "json",
        url: "/reserva/cliente",
        data: {
            nombres: nombres,
            apellidos: apellidos,
            identificacion: identificacion,
            identificacion_id: identificacion_id,
        },
        success: function(data) {
            alert(data.mensaje);
        },
    });

});

$('#limpiar').on('click', function() {
    document.getElementById('form').reset();
});