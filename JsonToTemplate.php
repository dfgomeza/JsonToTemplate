<?php

if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'Este archivo no se puede acceder directamente.' );
}

// Clase principal de la extensión
class JsonToTemplate {
    public static function onParserBeforeStrip( &$parser, &$text, &$stripState ) {
        // Detectar la etiqueta <json template="NombreDelTemplate">{...}</json>
        $pattern = '/<json\s+template="([^"]+)">(.*?)<\/json>/s';
        $text = preg_replace_callback( $pattern, function ( $matches ) {
            $templateName = htmlspecialchars( $matches[1] ); // Nombre del template principal
            $jsonData = json_decode( $matches[2], true );   // Decodificar JSON

            if ( $jsonData === null ) {
                return '<b style="color:red">Error: Invalid JSON</b>';
            }

            // Generar la llamada al template principal
            $templateCall = '{{' . $templateName;
            $templateCall .= self::generateTemplateParams( $jsonData );
            $templateCall .= '}}';

            return $templateCall;
        }, $text );
    }

    private static function generateTemplateParams( $jsonData ) {
        $params = '';
        foreach ( $jsonData as $key => $value ) {
            if ( is_array( $value ) ) {
                if ( self::isAssocArray( $value ) ) {
                    // Generar una llamada a un sub-template para objetos anidados
                    $params .= '|' . htmlspecialchars( $key ) . '=' . self::generateSubTemplateCall( $key, $value );
                } else {
                    // Procesar listas simples
                    $params .= '|' . htmlspecialchars( $key ) . '=' . self::generateListTemplateCalls( $key, $value );
                }
            } else {
                // Agregar parámetros simples
                $params .= '|' . htmlspecialchars( $key ) . '=' . htmlspecialchars( $value );
            }
        }
        return $params;
    }

    private static function generateSubTemplateCall( $templateName, $data ) {
        // Generar una llamada a un sub-template con los datos del objeto anidado
        $subTemplateCall = '{{' . htmlspecialchars( $templateName );
        $subTemplateCall .= self::generateTemplateParams( $data );
        $subTemplateCall .= '}}';
        return $subTemplateCall;
    }

    private static function generateListTemplateCalls( $key, $items ) {
        $templateCalls = '';
        foreach ( $items as $item ) {
            $templateCalls .= '{{' . htmlspecialchars( $key ) . '|item=' . htmlspecialchars( $item ) . '}}';
        }
        return $templateCalls;
    }

    private static function isAssocArray( $array ) {
        if ( array() === $array ) return false;
        return array_keys( $array ) !== range( 0, count( $array ) - 1 );
    }
}

// Registro del hook
$wgHooks['ParserBeforeStrip'][] = 'JsonToTemplate::onParserBeforeStrip';
