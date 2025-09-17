<?php
class AppUtils {
    public static function setFlash($message, $type = 'success') {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    
    public static function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Loguear eventos en la aplicación
    public static function logEvent($message, $type = 'INFO') {
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $type = strtoupper($type);
        $logLine = "[$date] - [$time] - [$type] - $message\n";
        $logFile = __DIR__ . "/log/log" . date('Ymd') . ".txt";
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    public static function cifrar($texto, $password) {
        // Generar una clave segura a partir del password
        $clave = hash('sha256', $password, true);

        // Generar un IV (vector de inicialización) aleatorio
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));

        // Cifrar
        $cifrado = openssl_encrypt($texto, 'AES-256-CBC', $clave, OPENSSL_RAW_DATA, $iv);

        // Guardamos el IV junto con el cifrado (base64 para que sea "texto plano")
        return base64_encode($iv . $cifrado);
    }

    public static function descifrar($textoCifrado, $password) {
        $clave = hash('sha256', $password, true);

        // Decodificar base64
        $datos = base64_decode($textoCifrado);

        // Extraer el IV
        $iv_length = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($datos, 0, $iv_length);

        // Extraer el texto cifrado
        $cifrado = substr($datos, $iv_length);

        // Descifrar
        return openssl_decrypt($cifrado, 'AES-256-CBC', $clave, OPENSSL_RAW_DATA, $iv);
    }

    public static function renderSafeHtml($html) {
        // Lista de etiquetas permitidas
        /*$allowedTags = '<h1><h2><h3><h4><h5><h6><b><strong><i><em><u><p><br><ul><ol><li><div><span><blockquote>';

        // Elimina todas las etiquetas que no estén en la lista
        $textoSeguro = strip_tags($texto, $allowedTags);

        // Convierte saltos de línea en <br>
        $textoSeguro = nl2br($textoSeguro, false);
        return $textoSeguro;*/
        // Lista de etiquetas permitidas
    $allowedTags = ['h1','h2','h3','h4','h5','h6',
        'b','strong','i','em','u',
        'p','br','ul','ol','li',
        'div','span','blockquote','a'];

    // Reglas de atributos permitidos
    $allowedAttrs = [
        'a'    => ['href', 'rel', 'target'],
        'span' => ['class', 'style'],
        'div'  => ['class'],
    ];

    // Cargar en DOM
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    foreach ($xpath->query('//*') as $el) {
        $tag = $el->nodeName;

        // Si el tag no está permitido → lo eliminamos
        if (!in_array($tag, $allowedTags)) {
            $el->parentNode->removeChild($el);
            continue;
        }

        // Filtrar atributos
        $keepAttrs = $allowedAttrs[$tag] ?? [];
        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = $attr->nodeName;
            $value = $attr->nodeValue;

            if (!in_array($name, $keepAttrs)) {
                $el->removeAttribute($name);
                continue;
            }

            // Validaciones extra
            if ($name === 'href' && preg_match('/^(javascript|data):/i', $value)) {
                $el->removeAttribute($name);
            }
            if ($name === 'style' && stripos($value, 'expression') !== false) {
                $el->removeAttribute($name);
            }
        }
    }

    return $dom->saveHTML();
}

}