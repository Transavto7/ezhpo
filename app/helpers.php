<?php

/**
 * фанк...
 */
if(!function_exists('user'))
{
    function user()
    {
        return auth()->user();
    }
}
