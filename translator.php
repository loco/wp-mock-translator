<?php
/**
 * Hook fired as a filter for the "mock" translation api
 * 
 * @param string[] input strings
 * @param Loco_Locale target locale for translations
 * @param array our own api configuration
 * @return string[] output strings
 */
function mock_translator_process_batch( array $sources, Loco_Locale $Locale, array $config ){
    $targets = array();
    foreach( $sources as $i => $source ){
        $targets[$i] = mock_translator_translate_text($source);
    }
    return $targets;
}


/**
 * @param string
 * @return string
 */
function mock_translator_translate_text( $source ){
    // TODO handle well-formed HTML
    $target = '';
    while( is_string($source) && '' !== $source ){
        // Protect URLs, printf formatting and HTML entities
        if( preg_match('!^https?://\\S*!',$source,$match) || 
            preg_match('/^&(#\\d+|#x[0-9a-f]|[a-z]+);/i',$source,$match) ||
            preg_match('!^</?[a-z]+.*>!iU',$source,$match) ||
            preg_match('/^%(?:\\d+\\$)?(?:\'.|[-+0 ])*\\d*(?:\\.\\d+)?[suxXbcdeEfFgGo%]/',$source,$match)
        ){
            $target .= $match[0];
        }
        // else 'translate' if it looks wordy
        else if( preg_match('/^[a-z]+/ui',$source,$match) ){
            $target .= mock_translator_translate_word($match[0]);
        }
        // else use whatever this is up to the next unicode character, which is probably punctuation.
        else if( preg_match('/^./u',$source,$match) ){
            $target .= $match[0];
        }
        // else bail with whatever's left, which should be impossible unless string isn't utf8
        else {
            $target .= $source;
            break;
        }
        // truncate source and continue
        $length = strlen($match[0]);
        $source = substr($source,$length);
    }
    return $target;
}


/**
 * @param string
 * @return string
 */
function mock_translator_translate_word( $source ){
    $target = strrev($source);
    // reverse title casing if applicable
    if( preg_match('/^[A-Z][a-z]+/',$source) ){
        $target = mb_convert_case($target,MB_CASE_TITLE,'UTF-8');
    }
    return $target;
}
