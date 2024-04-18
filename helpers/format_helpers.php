<?php

function format_timestamp(string $timestamp): string
{
    return date('F j, Y, g:i a', strtotime($timestamp));
}
