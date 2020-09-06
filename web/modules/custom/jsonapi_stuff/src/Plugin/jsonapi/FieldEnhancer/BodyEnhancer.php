<?php

namespace Drupal\jsonapi_stuff\Plugin\jsonapi\FieldEnhancer;

use Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerBase;
use Shaper\Util\Context;

/**
 * Perform manipulations of a body field.
 *
 * @ResourceFieldEnhancer(
 *   id = "body",
 *   label = @Translation("Body"),
 *   description = @Translation("Body")
 * )
 */
class BodyEnhancer extends ResourceFieldEnhancerBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'include_processed' => TRUE,
      'processed_name' => 'full',
      'include_summary' => TRUE,
      'summary_name' => 'summary',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function doTransform($data, Context $context) {
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  protected function doUndoTransform($data, Context $context) {
    $configuration = $this->getConfiguration();
    $output = [];

    if ($configuration['include_processed'] && !empty($data['processed'])) {
      $output[$configuration['processed_name'] ?: 'processed'] = $data['processed'];
    }
    if ($configuration['include_summary'] && !empty($data['summary'])) {
      $output[$configuration['summary_name'] ?: 'summary'] = $data['summary'];
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getOutputJsonSchema() {
    return [
      'oneOf' => [
        ['type' => 'object'],
        ['type' => 'null'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsForm(array $resource_field_info) {
    $settings = empty($resource_field_info['enhancer']['settings'])
      ? $this->getConfiguration()
      : $resource_field_info['enhancer']['settings'];

    return [
      'include_processed' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Include processed'),
        '#default_value' => $settings['include_processed'],
      ],
      'processed_name' => [
        '#type' => 'textfield',
        '#title' => $this->t('Processed name'),
        '#default_value' => $settings['processed_name'],
      ],
      'include_summary' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Include summary'),
        '#default_value' => $settings['include_summary'],
      ],
      'summary_name' => [
        '#type' => 'textfield',
        '#title' => $this->t('Summary name'),
        '#default_value' => $settings['summary_name'],
      ],
    ];
  }

}
