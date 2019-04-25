# Pattern Lab pattern data exporter

The pattern data exporter plugin exports selected pattern data.

## Installation

To add the plugin to your project using [Composer](https://getcomposer.org/)
type:

    composer require the-unipro-group/pattern-lab-php-pattern-data-exporter

## Usage

You can configure what category of data stored by `\PatternLab\PatternData` to
export and provide a whitelist for fields.

### Configuration

The configuration lives under `plugins.patternDataExporter`.

- `enabled`: whether to export pattern data.
- `categories`: an array of category types that will be exported, eg. `pattern`,
  `patternType`, `patternSubtype`; if not specified or empty, all types will be
  exported.
- `fields`: an array of whitelisted fields to export of the items in the
  selected categories; if not specified or empty, all fields will be exported.

## Disabling the Plugin

To disable the plugin you can either directly edit `./config/config.yml` or use
the command line option:

    php core/console --config --set plugins.patternDataExporter.enabled=false
