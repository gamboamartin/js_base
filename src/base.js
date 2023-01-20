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

let get_url = (seccion, accion, extra_params) => {
    let session = getParameterByName('session_id');
    let url = `index.php?seccion=${seccion}&accion=${accion}&ws=1&session_id=${session}`;
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
