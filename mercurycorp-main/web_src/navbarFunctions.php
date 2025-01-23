<?PHP

function url(){

    if($_SERVER["HTTP_HOST"]=="127.0.0.1" || $_SERVER["HTTP_HOST"]=="localhost")
        return "http://".$_SERVER["HTTP_HOST"]."/mercurycorp/web_src/";
    else
        return "https://".$_SERVER["HTTP_HOST"]."/mercurycorp/web_src/";
}




?>