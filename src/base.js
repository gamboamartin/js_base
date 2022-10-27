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
function integra_new_option(container, descripcion, value,data= "",data_value = " "){
    let new_option =new_option_sl(descripcion,value,data,data_value);
    $(new_option).appendTo(container);
}


function new_option_sl(descripcion,value,data= "",data_value = " "){

    if (data !== ""){
        data_value = (data_value !== "")? "="+data_value:"";
        return `<option value ="${value}" ${data}${data_value}>${descripcion}</option>`;
    }
    return `<option value ="${value}">${descripcion}</option>`;
}
