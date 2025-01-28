<?php
/**
 * Hook fired as a filter for the "mock" translation api
 * 
 * @param string[] $targets translated strings, initially empty
 * @param string[][] $items input messages with keys, "source", "context" and "notes"
 * @param Loco_Locale $Locale target locale for translations
 * @param array $config This api's configuration
 * @return string[] Translated strings
 */
function mock_translator_process_batch( array $targets, array $items, Loco_Locale $Locale, array $config ){
    foreach( $items as $i => $item ){
        $targets[$i] = mock_translator_translate_text($item['source']);
    }
    return $targets;
}


/**
 * @param string $source
 * @return string
 */
function mock_translator_translate_text( $source ){
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
        // else 'translate' if it looks wordy (ascii only here)
        else if( preg_match('/^[a-z]+/i',$source,$match) ){
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
    // TODO string reverse needs to be utf8-safe
    $target = strrev($source);
    // reverse title casing if applicable
    if( preg_match('/^[A-Z][a-z]+/',$source) ){
        $target = mb_convert_case($target,MB_CASE_TITLE,'UTF-8');
    }
    return $target;
}
