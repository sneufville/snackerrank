<?php

function build_upload_path($original_filename, $upload_sub_folder_name = 'public_images'): string
{
    $current_folder = dirname(__FILE__);
    $path_segments = [$current_folder, $upload_sub_folder_name, basename($original_filename)];
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

function is_image_file($temporary_path, $new_path): bool
{
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}
