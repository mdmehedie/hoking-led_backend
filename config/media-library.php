<?php

return [

    /*
     * The disk on which to store uploaded files and derived images by default. Choose
     * one of the disks you've configured in config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The maximum file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 10, // 10MB

    /*
     * This queue connection will be used to generate derived images.
     * Leave empty to use the default queue connection.
     */
    'queue_connection_name' => '',

    /*
     * This queue will be used to generate derived images.
     * Leave empty to use the default queue.
     */
    'queue_name' => '',

    /*
     * By default all conversions will be performed on a queue.
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS_BY_DEFAULT', true),

    /*
     * The fully qualified class name of the media model.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * The fully qualified class name of the file adder.
     */
    'file_adder' => Spatie\MediaLibrary\FileAdder\FileAdder::class,

    /*
     * The class that contains the strategy for determining a media file's path.
     */
    'path_generator' => App\Services\MediaPathGenerator::class,

    /*
     * Here you can specify which conversions should be performed on a media item.
     */
    'conversions' => [],

    /*
     * The class that contains the strategy for determining a media file's url.
     */
    'url_generator' => null,

    /*
     * Moves uploaded file to the specified disk.
     */
    'generate_thumbnails_for_temporary_uploads' => true,

    /*
     * Here you can specify which conversions should be performed on a temporary media item.
     */
    'temporary_upload_conversions' => [],

    /*
     * FFMPEG & FFProbe binaries paths, only used if you try to generate video
     * thumbnails and have installed the php-ffmpeg/php-ffmpeg composer package.
     */
    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

];
