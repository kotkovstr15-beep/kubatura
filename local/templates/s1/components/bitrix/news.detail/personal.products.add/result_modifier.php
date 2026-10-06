<?php

function prepareUrlToClearFormat(string $url): string
{
    $url =  str_replace(array('https://', 'http://'), '', $url);
    $url = preg_replace('/\s\s+/', '', $url);
    $url = preg_replace('/[\\/]$/', '', $url);


    return $url;

}