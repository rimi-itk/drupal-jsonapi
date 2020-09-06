<?php

namespace Drupal\jsonapi_stuff\Plugin\jsonapi\FieldEnhancer;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\jsonapi_extras\Plugin\ResourceFieldEnhancerBase;
use Shaper\Util\Context;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Perform manipulations of a body field.
 *
 * @ResourceFieldEnhancer(
 *   id = "file",
 *   label = @Translation("File"),
 *   description = @Translation("File")
 * )
 */
class FileEnhancer extends ResourceFieldEnhancerBase implements ContainerFactoryPluginInterface {
  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Constructs a new JSONFieldEnhancer.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param \Drupal\jsonapi_stuff\Plugin\jsonapi\FieldEnhancer\string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The entity repository.
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, EntityRepositoryInterface $entityRepository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityRepository = $entityRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity.repository'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'include_url' => TRUE,
      'url_name' => 'url',
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
    $output = $data;

    if ($configuration['include_url']) {
      /** @var \Drupal\file\Entity\File $file */
      $file = $this->entityRepository->loadEntityByUuid('file', $data['id']);

      // Note: We have to add new data to meta in relationships.
      $output['meta'][$configuration['url_name'] ?: 'url'] = $file->createFileUrl(FALSE);
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
      'include_url' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Include url'),
        '#default_value' => $settings['include_url'],
      ],
      'url_name' => [
        '#type' => 'textfield',
        '#title' => $this->t('Url name'),
        '#default_value' => $settings['url_name'],
      ],
    ];
  }

}
