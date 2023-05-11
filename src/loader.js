class Loader {

    static #loader = () => {
        return `<div id="loader" class="loadingio-spinner-spinner-fbpis5xeagh" style="display: none;">
                        <div class="ldio-kjp6horjcfr"><div></div><div></div><div></div><div></div><div></div><div>
                        </div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>`;
    }

    static #modal_loader = () => {
        return `<div id="modal-load" class="modal-load"><div class="loadingio-spinner-spinner-fbpis5xeagh" style="display: none;">
                        <div class="ldio-kjp6horjcfr"><div></div><div></div><div></div><div></div><div></div><div>
                        </div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></div>`;
    }

    static #execute_ajax = (url, dataform, ajax_response, ajax_error) => {
        $.ajax({
            async: true,
            type: 'POST',
            url: url,
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

    static load = (identifier, url, dataform, ajax_response, ajax_error) => {
        $(identifier).append(Loader.#loader());
        Loader.#load_loader('#loader');
        Loader.#execute_ajax(url, dataform, ajax_response, ajax_error);
        $( '#loader' ).remove();
    }

    static post = (identifier, url, dataform, ajax_response, ajax_error) => {
        /*$(".login-body").css("position", "relative");
        $(".login-body").append(Loader.#modal_loader());*/
        Loader.#load_loader('#modal-load');
        Loader.#execute_ajax(url, dataform, ajax_response, ajax_error);

    }
}