/*Este script se usará para que, al crear preguntas y seleccionar un 
número de de opciones en el desplegable, muestre esa cantidad de cajas de texto*/
$(document).ready(function () {  // Se ejecuta cuando el documento está listo.
        // Funcionalidad para mostrar/ocultar el formulario
        $('#showFormButton').click(function() {
            $('#showFormButton').hide(); 
            $('#NewQuestionForm').show(); 
            $('#succesMessage').hide();
        });
    
    $('#numFields').change(function () {

        /* Se activa cuando el valor del desplegable (que es un selector)  con id 'numFields' cambia(de ahi el método "change").
        Se almacena en la variable "num" el número de campos seleccionados en el desplegable*/
        var num = $(this).val(); 
        var inputFields = '';
        /*Si el número es mayor que cero, se crea esa cantidad de "div".
         donde figurarán un label con el número de la opción y su caja de texto.*/
        if (num) {
            for (var i = 1; i <= num; i++) {
                // Se crea un bucle que se ejecuta desde 1 hasta el valor de 'num'.
                // En cada iteración, se añade un div con un label y un input al string 'inputFields'.
                inputFields += '<div class="form-group"><label for="respuesta' + i + '">Respuesta ' + i + ':</label><input type="text" id="respuesta' + i + '" name="respuesta' + i + '" class="form-control" required></div>';
            }
        } else {
            // Si el valor de 'num' cambiase a cero, se vacía el contenido del elemento con id 'inputFields'.
            $('#inputFields').html('');
        }

        // Se actualiza el contenido del elemento con id 'inputFields' con el string correspondiente.
        $('#inputFields').html(inputFields);
    });
});