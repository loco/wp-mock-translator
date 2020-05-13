<?php
/*
Plugin Name: Mock Translation API for Loco Translate
Plugin URI: https://github.com/loco/wp-mock-translator
Description: Example auto-translate plugin that produces well-formed nonsense
Author: Tim Whitlock
Version: 1.0.0
Author URI: https://localise.biz/wordpress/plugin
*/
if( is_admin() ){

    // Append our api via the `loco_api_providers` filter hook.
    // This should be available all the time Loco Translate is running.
    function mock_translator_filter_apis( array $apis ){
        $apis[] = array (
            'id' => 'mock',
            'key' => 'must not be empty',
            'url' => 'https://github.com/loco/wp-mock-translator',
            'name' => 'Mock translation API',
        );
        return $apis;
    }
    add_filter('loco_api_providers','mock_translator_filter_apis',10,1);


    // Hook our translate function with 'loco_api_translate_{$id}'
    // We only need to do this when the Loco Translate Ajax hook is running.
    function mock_translator_ajax_init(){
        require __DIR__.'/translator.php';
        add_filter('loco_api_translate_mock','mock_translator_process_batch',0,3);
    }
    add_action('loco_api_ajax','mock_translator_ajax_init',0,0);
    
}
