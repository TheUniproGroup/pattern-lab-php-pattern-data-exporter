<?php

namespace FabbDev\PatternLab\LineageExporter;

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
  const PLUGIN_NAME = 'lineageExporter';

  /**
   * Create the listener and subscribe it to the generate styleguide end event.
   *
   * This is the same event used to generate patternlab-data.json.
   */
  public function __construct() {
    $this->addListener('patternData.lineageHelperEnd', 'exportLineage');
  }

  /**
   * Export the lineage data.
   */
  public function exportLineage() {
    if (!$this->getConfig('enabled')) {
      return;
    }

    $dataDir = $this->getConfig('dataDir');
    if (is_null($dataDir)) {
      // Use the same default directory as patternlab-data.json.
      $dataDir = Config::getOption('publicDir') . '/styleguide/data';
    }

    $data = $this->getLineageData();

    $encoded = json_encode($data);
    if (false === $encoded) {
      error_log(__CLASS__ . ": Error JSON encoding pattern path src");
      return;
    }

    $path = $dataDir . '/patternlab-lineage.json';
    $return = file_put_contents($path, $encoded);
    if (false === $return) {
      error_log(__CLASS__ . ": Error writing $path");
    }
  }

  protected function getLineageData() {
    // The lineage data should be stored in the option data.
    // @see \PatternLab\PatternData\Helpers\LineageHelper::run()

    $data = PatternData::get();
    return [];
  }

  /**
   * Returns the requested plugin configuration.
   *
   * @param string $name
   *   The name of the config option in dotted form, eg. 'enabled', 'us.title'.
   *
   * @return string|false
   *   The configuration value, or false if it wasn't found.
   */
  protected function getConfig($name) {
    return Config::getOption('plugins.' . static::PLUGIN_NAME . ".$name");
  }

}
