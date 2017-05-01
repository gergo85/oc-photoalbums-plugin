<?php namespace Graker\PhotoAlbums;

use Backend;
use System\Classes\PluginBase;
use Event;

/**
 * PhotoAlbums Plugin Information File
 */
class Plugin extends PluginBase
{

  /**
   * Returns information about this plugin.
   *
   * @return array
   */
  public function pluginDetails()
  {
    return [
      'name'        => 'PhotoAlbums',
      'description' => 'Create, display and manage galleries of photos arranged in albums',
      'author'      => 'Graker',
      'icon'        => 'icon-camera-retro',
      'homepage'    => 'https://github.com/graker/photoalbums',
    ];
  }

  /**
   * Registers any front-end components implemented in this plugin.
   *
   * @return array
   */
  public function registerComponents()
  {
    return [
      'Graker\PhotoAlbums\Components\Photo' => 'singlePhoto',
      'Graker\PhotoAlbums\Components\Album' => 'photoAlbum',
      'Graker\PhotoAlbums\Components\AlbumList' => 'albumList',
      'Graker\PhotoAlbums\Components\RandomPhotos' => 'randomPhotos',
    ];
  }

  /**
   * Registers any back-end permissions used by this plugin.
   * At the moment there's one permission allowing overall management of albums and photos
   *
   * @return array
   */
  public function registerPermissions()
  {
    return [
      'graker.photoalbums.manage_albums' => [
        'label' => 'Manage photo albums',
        'tab' => 'Photo albums',
      ],
    ];
  }

  /**
   * Registers back-end navigation items for this plugin.
   *
   * @return array
   */
  public function registerNavigation()
  {
    return [
      'photoalbums' => [
        'label' => 'Photo albums',
        'url' => Backend::url('graker/photoalbums/albums'),
        'icon'        => 'icon-camera-retro',
        'permissions' => ['graker.photoalbums.manage_albums'],
        'order'       => 500,

        'sideMenu' => [
          'upload_photos' => [
            'label'       => 'Upload photos',
            'icon'        => 'icon-upload',
            'url'         => Backend::url('graker/photoalbums/upload/form'),
            'permissions' => ['graker.photoalbums.manage_albums'],
          ],
          'new_album' => [
            'label'       => 'New album',
            'icon'        => 'icon-plus',
            'url'         => Backend::url('graker/photoalbums/albums/create'),
            'permissions' => ['graker.photoalbums.manage_albums'],
          ],
          'posts' => [
            'label'       => 'Albums',
            'icon'        => 'icon-copy',
            'url'         => Backend::url('graker/photoalbums/albums'),
            'permissions' => ['graker.photoalbums.manage_albums'],
          ],
          'new_photo' => [
            'label'       => 'New photo',
            'icon'        => 'icon-plus-square-o',
            'url'         => Backend::url('graker/photoalbums/photos/create'),
            'permissions' => ['graker.photoalbums.manage_albums'],
          ],
          'photos' => [
            'label'       => 'Photos',
            'icon'        => 'icon-picture-o',
            'url'         => Backend::url('graker/photoalbums/photos'),
            'permissions' => ['graker.photoalbums.manage_albums'],
          ],
        ],
      ],
    ];
  }


  /**
   *
   * Custom column types definition
   *
   * @return array
   */
  public function registerListColumnTypes() {
    return [
      'is_front' => [$this, 'evalIsFrontListColumn'],
      'image' => [$this, 'evalImageListColumn'],
    ];
  }


  /**
   *
   * Special column to show photo set to be album's front in album's relations list
   *
   * @param $value
   * @param $column
   * @param $record
   * @return string
   */
  public function evalIsFrontListColumn($value, $column, $record) {
    return ($value == $record->id) ? 'Yes' : '';
  }


  /**
   *
   * Column to render image thumb for Photo model
   *
   * @param $value
   * @param $column
   * @param $record
   * @return string
   */
  function evalImageListColumn($value, $column, $record) {
    if ($record->has('image')) {
      $thumb = $record->image->getThumb(
        isset($column->config['width']) ? $column->config['width'] : 200,
        isset($column->config['height']) ? $column->config['height'] : 200,
        ['mode' => 'auto']
      );
    } else {
      // in case the file attachment was manually deleted for some reason
      $thumb = '';
    }
    return "<img src=\"$thumb\" />";
  }


  /**
   * boot() implementation
   *  - Register listener to markdown.parse
   */
  public function boot() {
    Event::listen('markdown.parse', 'Graker\PhotoAlbums\Classes\MarkdownPhotoInsert@parse');
  }

}
