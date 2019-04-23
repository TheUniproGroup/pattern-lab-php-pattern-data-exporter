<?php

namespace TheUniproGroup\PatternLab\PatternDataExporter;

use PatternLab\Config;
use PatternLab\Listener;
use PatternLab\PatternData;

/**
 * Provides a Pattern Lab listener to export pattern path src data.
 *
 * It's used by the pattern partial loader to recognise patterns.
 */
class PatternLabListener extends Listener {

  /**
   * The plugin's name in the Pattern Lab configuration and composer.json.
   */
  const PLUGIN_NAME = 'patternDataExporter';

  /**
   * Create the listener and subscribe it to the generate styleguide end event.
   *
   * This is the same event used to generate patternlab-data.json.
   */
  public function __construct() {
    $this->addListener('builder.generateStyleguideEnd', 'exportData');
  }

  /**
   * Export the pattern data.
   */
  public function exportData() {
    if (!$this->getConfig('enabled')) {
      return;
    }

    $dataDir = $this->getConfig('dataDir');
    if (is_null($dataDir)) {
      // Use the same default directory as patternlab-data.json.
      $dataDir = Config::getOption('publicDir') . '/styleguide/data';
    }

    $data = $this->getData();

    $encoded = json_encode($data);
    if (false === $encoded) {
      error_log(__CLASS__ . ": Error JSON encoding pattern path src");
      return;
    }

    $path = $dataDir . '/patternlab-pattern-data.json';
    $return = file_put_contents($path, $encoded);
    if (false === $return) {
      error_log(__CLASS__ . ": Error writing $path");
    }
  }

  /**
   * Get the filtered pattern data.
   *
   * @return array
   *   The filtered data.
   */
  protected function getData() {
    // Get all the data.
    $data = PatternData::get();

    // Filter by category if appropriate.
    $categories = $this->getConfig('categories');
    if ($categories) {
      $subset = array_filter($data, function ($item) use ($categories) {
        return in_array($item['category'], $categories);
      });
    }
    else {
      // Don't filter anything.
      $subset = $data;
    }

    // Filter the resulting fields if appropriate.
    $fields = $this->getConfig('fields') ?: [];
    if ($fields) {
      $allowed_keys = array_flip($fields);
      return array_map(function (array $pattern) use ($allowed_keys) {
        return array_intersect_key($pattern, $allowed_keys);
      }, $subset);
    }
    else {
      // Don't filter anything.
      return $subset;
    }
  }

  /**
   * Returns the requested plugin configuration.
   *
   * @param string $name
   *   The name of the config option in dotted form, eg. 'enabled', 'us.title'.
   *
   * @return mixed|false
   *   The configuration value, or false if it wasn't found.
   */
  protected function getConfig($name) {
    return Config::getOption('plugins.' . static::PLUGIN_NAME . ".$name");
  }

}
