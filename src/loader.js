class Loader {

    static #loader = () => {
        return `<div id="loader" class="loadingio-spinner-spinner-fbpis5xeagh" style="display: none;">
                        <div class="ldio-kjp6horjcfr"><div></div><div></div><div></div><div></div><div></div><div>
                        </div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>`;
    }

    static #load_contenido = (ruta_load, dataform, ajax_response, ajax_error) => {
        $.ajax({
            async: true,
            type: 'POST',
            url: ruta_load,
            data: dataform,
            contentType: false,
            processData: false,
            success: ajax_response,
            error: ajax_error
        });
    };

    static #load_loader = (screen) => {
        $(document).ajaxStart(function () {
            $(screen).fadeIn();
        }).ajaxStop(function () {
            $(screen).fadeOut();
        })
    }

    static load = (identifier, ruta_load, dataform, ajax_response, ajax_error) => {
        $(identifier).append(Loader.#loader());
        Loader.#load_loader('#loader');
        Loader.#load_contenido(ruta_load, dataform, ajax_response, ajax_error);
    }
}