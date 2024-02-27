function getAbsolutePath() {
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    const regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function integra_new_option(container, descripcion, value, data = "", data_value = " ") {
    let new_option = new_option_sl(descripcion, value, data, data_value);
    $(new_option).appendTo(container);
}


function new_option_sl(descripcion, value, data = "", data_value = " ") {

    if (data !== "") {
        data_value = (data_value !== "") ? "=" + data_value : "";

        return `<option value ="${value}" ${data}${data_value}>${descripcion}</option>`;
    }
    return `<option value ="${value}">${descripcion}</option>`;
}

let get_data = (url, acciones) => {
    fetch(url)
        .then(response => response.text())
        .then(value => {
            try {
                return JSON.parse(value)
            } catch (e) {
                throw new Error(value);
            }
        })
        .then(data => acciones(data))
        .catch(err => {
            let response = err.message;
            document.body.innerHTML = response.replace('[]', '')
        });
}

let get_url = (seccion, accion, extra_params, ws = "1") => {
    let session = getParameterByName('session_id');
    let url = `index.php?seccion=${seccion}&accion=${accion}&ws=${ws}&session_id=${session}`;
    let objects_params = Object.entries(extra_params)
    objects_params.forEach(function (value, index, array) {
        let param = value[0];
        let val = value[1];
        let nuevo = `&${param}=${val}`;
        url = url.concat(nuevo)
    });
    return url;
}

let ajax = (url, acciones) => {
    fetch(url)
        .then(response => response.text())
        .then(value => {
            try {
                return JSON.parse(value)
            } catch (e) {
                throw new Error(value);
            }
        })
        .then(data => acciones(data))
        .catch(err => {
            let response = err.message;
            document.body.innerHTML = response.replace('[]', '')
        });
}

function add_option(descripcion, value, data = [], data_obj = {}) {

    if (Array.isArray(data) && data.length){
        let data_value = "";

        data.forEach(function (value, index, array) {
            let prop_value = data_obj[value];
            data_value += `data-${value}=${prop_value} `;
        });

        return `<option value ="${value}" ${data_value}>${descripcion}</option>`;
    }

    return `<option value ="${value}">${descripcion}</option>`;
}

function add_new_option(container, descripcion, value, data = [], data_obj = {}) {
    let new_option = add_option(descripcion, value, data, data_obj);
    $(new_option).appendTo(container);
}

const get_data2 = function (seccion, accion, extra_params, identificador, extra_data = [], selects = []) {

    const url = get_url(seccion, accion, extra_params);

    ajax(url, function (data) {

        identificador.empty();

        selects.forEach(function (value, index, array) {

            if (typeof value !== 'object') {
                alert(`${value.selector} no es un objeto`);
                return;
            }

            if (value[0].tagName !== 'SELECT') {
                alert(`${value.selector} no es un objeto select`);
                return;
            }

            value.empty();
            add_new_option(value, 'Selecciona una opción', '-1');
            value.selectpicker('refresh');
        });

        add_new_option(identificador, 'Selecciona una opción', '-1');

        data.registros.forEach(function (value, index, array) {
            add_new_option(identificador, value[`${seccion}_descripcion_select`], value[`${seccion}_id`], extra_data,
                value);
        });

        identificador.selectpicker('refresh');
    });
};

const mask_formato = (cadena) => {
    let salida = "";
    let aux = '';

    for (var i = 0; i < cadena.length; i++) {
        let value = cadena.substring(i, i + 1);
        if (cadena.substring(i, i + 1) === '0'){
            aux = '\\'
        }
        salida += `${aux}${value}`
    }
    return salida;
}
$(".descarga_excel").click(function() {
    $('.dataTables_filter').find('input').each(function() {
        let seccion = getParameterByName('seccion');
        let session = getParameterByName('session_id');

        let input_search = $(".descarga_excel");
        let url = `index.php?seccion=${seccion}&accion=descarga_excel&session_id=${session}`;
        let search_inp = $(this).val();
        let url_completa = url+'&texto_busqueda='+search_inp;
        input_search.attr('href',url_completa);

        console.log(input_search.attr('href'));
    });
});


/**
 * Función para gestionar la selección de productos en una tabla y actualizar el valor de un campo de entrada.
 * @param {object} datatable - Instancia de DataTable
 * @param {string} input_producto - Selector del elemento de entrada en el DOM donde se actualizarán los productos seleccionados.
 * @param {function} callback - Función que se ejecuta después de actualizar los productos seleccionados. Retorna los producos productos seleccionados.
 * @returns {array} - Un array que contiene los identificadores de productos seleccionados.
 */
const seleccionar_producto = (datatable, input_producto, callback) => {
    let timer = null;
    let productos_seleccionados = [];

    clearTimeout(timer);

    timer = setTimeout(() => {
        let selectedData = datatable.rows({ selected: true }).data();

        productos_seleccionados = [];

        selectedData.each(function (value, index, data) {
            productos_seleccionados.push(value.com_producto_id);
        });

        $(input_producto).val(productos_seleccionados);

        callback(productos_seleccionados);
    }, 500);
};

/**
 * Función para gestionar el evento de envío de un formulario de alta de productos.
 * @param {string} formulario - Selector del formulario en el DOM al que se le asignará el evento de envío.
 * @param {string} identificador - Selector del elemento en el DOM asociado a la tabla de productos.
 * @param {object} datatable - Instancia de DataTable
 * @param {string} input_producto - Selector del elemento de entrada en el DOM donde se actualizarán los productos seleccionados.
 */
const alta_productos = (formulario, identificador, datatable, input_producto) => {

    $(formulario).on('submit', function (e) {
        let productos_seleccionados = seleccionar_producto(identificador, datatable, input_producto)

        if (productos_seleccionados.length === 0) {
            e.preventDefault()
            alert("Seleccione un producto")
        }
    });
}