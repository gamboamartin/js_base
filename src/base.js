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

    if (Array.isArray(data) && data.length) {
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
        if (cadena.substring(i, i + 1) === '0') {
            aux = '\\'
        }
        salida += `${aux}${value}`
    }
    return salida;
}
$(".descarga_excel").click(function () {
    $('.dataTables_filter').find('input').each(function () {
        let seccion = getParameterByName('seccion');
        let session = getParameterByName('session_id');

        let input_search = $(".descarga_excel");
        let url = `index.php?seccion=${seccion}&accion=descarga_excel&session_id=${session}`;
        let search_inp = $(this).val();
        let url_completa = url + '&texto_busqueda=' + search_inp;
        input_search.attr('href', url_completa);

        console.log(input_search.attr('href'));
    });
});

/**
 * Función que realiza una solicitud AJAX para llevar a cabo la operación de alta de datos en una sección específica.
 * @param {string} seccion - Identificador de la sección a la que pertenece la operación de alta.
 * @param {object} data - Objeto que contiene los datos a enviar en la solicitud POST.
 * Puede ser un objeto vacío si no se requieren datos adicionales.
 * @param {function} acciones - Función opcional que se ejecutará después de que la operación de alta
 * se haya completado exitosamente.
 */
const alta = (seccion, data = {}, acciones = function () {
}) => {
    const url = get_url(seccion, "alta_bd", {});

    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        success: function (json) {
            acciones();

            if (json.hasOwnProperty("error")) {
                alert(json.mensaje_limpio);
            }
        },
        error: function (xhr, status) {
            alert('Error: Ocurrió un error al ejecutar la petición');
            console.error({xhr, status});
        }
    });
};

/**
 * Función para gestionar el evento de envío de un formulario de alta de productos.
 * @param {string} formulario - Selector del formulario en el DOM al que se le asignará el evento de envío.
 * @param {array} seleccionados - Array que contiene los identificadores de productos seleccionados.
 * @throws {Error} - Se lanzará un error si la longitud del array de productos seleccionados es igual a cero.
 */
const alta_productos = (formulario, seleccionados) => {
    $(formulario).on('submit', function (e) {
        if (seleccionados.length === 0) {
            e.preventDefault();
            alert("Seleccione un producto");
        }
    });
};

/**
 * Función que gestiona el evento de clic en un botón de alta, recopila los datos mediante una función de callback,
 * realiza la operación de alta y ejecuta una función de callback después de completar la operación.
 * @param {object} boton - Objeto jQuery que representa el botón de alta al que se le asignará el evento de clic.
 * @param {string} seccion - Identificador de la sección a la que pertenece la operación de alta.
 * @param {function} callback_data - Función de callback que devuelve los datos necesarios para la operación de alta.
 * @param {function} callback_respuesta - Función de callback que se ejecutará después de que la operación de alta
 * se haya completado exitosamente.
 */
const alta_registro = (boton, seccion, callback_data, callback_respuesta) => {

    boton.click(function () {
        const datos = callback_data();

        if (datos === undefined) {
            return;
        }

        alta(seccion, datos, () => {
            callback_respuesta();
        });
    });
}

function stopDefAction(event) {
    event.preventDefault();
}
function elimina_bd(event){
    if (confirm("Estas seguro de eliminar el registro ?") === false) {
        stopDefAction(event);
    }
}

/**
 * Función que genera y devuelve un array de configuración para columnDefs de DataTable,
 * especialmente diseñado para integrar botones de eliminación en la última columna de una tabla.
 * @param {string} seccion - Parámetro que representa la sección a la que pertenecen los datos en la tabla.
 * @param {array} columns - Array que contiene las definiciones de columnas de la DataTable.
 * @returns {array} - Array de configuración columnDefs que incluye un botón de eliminación en la última columna.
 */
const columnDefs_callback_default = (seccion, columns) => {
    return [
        {
            targets: columns.length - 1,
            render: function (data, type, row) {
                let sec = getParameterByName('seccion');
                let acc = getParameterByName('accion');
                let registro_id = getParameterByName('registro_id');

                let url = $(location).attr('href');

                url = url.replace(acc, "elimina_bd");
                url = url.replace(sec, seccion);
                url = url.replace(registro_id, row[`${seccion}_id`]);
                return `<button  data-url="${url}" class="btn btn-danger btn-sm">Elimina</button>`;
            }
        }
    ]
}

/**
 * Función que realiza una solicitud AJAX para llevar a cabo la operación de eliminación
 * de un registro de una sección específica.
 * @param {string} url - URL a la que se enviará la solicitud POST para la eliminación del registro.
 * @param {function} acciones - Función opcional que se ejecutará después de que la operación de eliminación
 * se haya completado exitosamente.
 */
const elimina = (url, acciones = function () {
}) => {
    $.ajax({
        url: url,
        type: 'POST',
        success: function (json) {
            acciones();

            if (json.includes('error')) {
                alert("Error al eliminar el registro")
            }
        },
        error: function (xhr, status) {
            alert('Error, ocurrio un error al ejecutar la peticion');
            console.log({xhr, status})
        }
    });
}

/**
 * Función que gestiona el evento de clic en botones de eliminación dentro de una DataTable
 * y realiza la eliminación del registro asociado.
 * @param {object} datatable - Instancia de DataTable a la que se aplicará el evento de clic en botones de eliminación.
 */
const elimina_registro = (datatable) => {
    datatable.on('click', 'button', function (e) {
        const url = $(e.target).data("url");

        elimina(url, function () {
            datatable.ajax.reload();
        })
    });
}

/**
 * Función para gestionar la selección de productos en una tabla y actualizar el valor de un campo de entrada.
 * @param {object} datatable - Instancia de DataTable asociada a la tabla donde se realizará la selección de productos.
 * @param {string} input_producto - Selector del elemento de entrada en el DOM donde se actualizarán los identificadores de productos seleccionados.
 * @param {function} callback - Función que se ejecuta después de actualizar los productos seleccionados.
 * Recibe como parámetro el array de identificadores de productos.
 */
const seleccionar_producto = (datatable, input_producto, callback) => {
    let timer = null;
    let productos_seleccionados = [];

    clearTimeout(timer);

    timer = setTimeout(() => {
        let selectedData = datatable.rows({selected: true}).data();

        productos_seleccionados = [];

        selectedData.each(function (value, index, data) {
            productos_seleccionados.push(value.com_producto_id);
        });

        $(input_producto).val(productos_seleccionados);

        callback(productos_seleccionados);
    }, 500);
};

/**
 * Función que permite la selección de productos al hacer clic en las filas de una tabla DataTable.
 * @param {string} identificador - Selector del elemento HTML que representa la tabla y se utilizará para vincular el evento de clic.
 * @param {object} datatable - Instancia de DataTable asociada a la tabla donde se realizará la selección de productos.
 * @param {string} input_producto - Selector del elemento de entrada en el DOM donde se actualizarán los identificadores de productos seleccionados.
 * @param {function} callback - Función que se ejecuta después de actualizar los productos seleccionados.
 * Recibe como parámetro el array de identificadores de productos.
 */
const seleccionar_tabla = (identificador, datatable, input_producto, callback) => {
    $(identificador).on('click', 'thead:first-child, tbody', function (e) {
        seleccionar_producto(datatable, input_producto, callback);
    });
}


/**
 * Función que inicializa y configura una instancia DataTable.
 * @param {string} seccion - Parámetro que representa la sección a la que pertenecen los datos en la tabla.
 * @param {array} columns - Array que contiene las definiciones de columnas de la DataTable.
 * @param {array} filtros - Array opcional que contiene los filtros a aplicar en la carga de datos.
 * @param {array} extra_join - Array opcional que contiene información adicional de joins a aplicar en la carga de datos.
 * @param {function} columnDefsCallback - Función opcional que define las columnDefs específicas para la tabla.
 * Si no se proporciona, se utilizarán las columnDefs por defecto.
 * @param selectsActive
 * @param accion
 * @param extra_params
 * @param options
 * @returns {object} - Instancia DataTable configurada para la sección especificada.
 */
const table = (seccion, columns, filtros = [], extra_join = [], columnDefsCallback = null,
               selectsActive = false, accion = "data_ajax", extra_params = {},
               options= {paging: true, info: true, searching: true}) => {

    let $columnDefs = columnDefs_callback_default(seccion, columns);

    if (columnDefsCallback) {
        $columnDefs = columnDefsCallback(seccion, columns);
    }

    const ruta_load = get_url(seccion, accion, extra_params);

    let _columns = check_column(columns, selectsActive);
    let _checks = check(selectsActive);

    return new DataTable(`#table-${seccion}`, {
        dom: 'Bfrtip',
        retrieve: true,
        paging: options.paging,
        info: options.info,
        searching: options.searching,
        ajax: {
            "url": ruta_load,
            'data': function (data) {
                data.filtros = {
                    filtro: filtros,
                    extra_join: extra_join
                }
            },
            "error": function (jqXHR, textStatus, errorThrown) {
                let response = jqXHR.responseText;
                console.log(response)
            }
        },
        columns: _columns,
        columnDefs: $columnDefs,
        'select': _checks.select,
        'order': _checks.order
    });
}

check_column = function (columns, selectsActive = false) {
    if (selectsActive === false) {
        return columns;
    }

    return [
        {
            title: "Id",
            data: null,
            'checkboxes': {'selectRow': true}
        },
        ...columns
    ]
};

check = function (column) {
    let salida = {'select': {'style': 'single'}, 'order': []};

    if (column === true) {
        salida.select = {'style': 'multi'};
        salida.order = [[1, 'asc']];
    }
    return salida;
}


