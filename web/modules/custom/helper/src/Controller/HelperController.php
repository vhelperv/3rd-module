<?php

namespace Drupal\helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\helper\Form\GetInfoForm;
use Drupal\helper\ShowEntityList;
use Drupal\helper\Form\EditEntity;
use Psr\Container\ContainerInterface;

class HelperController extends ControllerBase {
  /**
   * Drupal\Core\Pager\PagerManagerInterface definition.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * MyController constructor.
   *
   * @param \Drupal\Core\Pager\PagerManagerInterface $pagerManager
   *   The pager manager.
   */
  public function __construct(PagerManagerInterface $pagerManager) {
    $this->pagerManager = $pagerManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = $container->get('pager.manager');
    return new static($instance);
  }

  /**
   * {@inheritdoc}
   */
  public function showEntityList() {
    // Get the current user information
    $current_user = \Drupal::currentUser();
    // Check if the current user has the administrator role
    $is_admin = in_array('administrator', $current_user->getRoles());
    $build = [];

    // Get data.
    $items = $this->dataEntityList();
    $total = count($items);
    $limit = 5;

    // Initialize pager and get current page.
    $pager = $this->pagerManager->createPager($total, $limit);
    $currentPage = $pager->getCurrentPage();

    // Use currentPage to limit items for the page.
    $items = array_slice($items, $currentPage * $limit, $limit);

    // Display items.
    foreach ($items as $item) {
      $build[] = [
        '#theme' => 'list-entity',
        '#is_admin' => $is_admin,
        '#items' => [
          '#item' => $item
        ]
      ];
    }

    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;
  }

  /**
   * Get data from the entity list.
   *
   * @return array
   *   The entity list.
   */
  protected function dataEntityList() {
    $reviewsList = new ShowEntityList();
    $infoEntity = $reviewsList->buildEntity();
    return $infoEntity;
  }

  public function getInfoForm() {
    $form = \Drupal::formBuilder()->getForm(GetInfoForm::class);
    return [
      '#theme' => 'get-info',
      '#form' => $form
    ];
  }
  public function editEntity() {
    $form = \Drupal::formBuilder()->getForm(EditEntity::class);
    return [
      '#theme' => 'get-info',
      '#form' => $form,
    ];
  }
}
