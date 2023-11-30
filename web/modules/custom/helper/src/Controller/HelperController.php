<?php

namespace Drupal\helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\helper\Form\GetInfoForm;
use Drupal\helper\ShowEntityList;

class HelperController extends ControllerBase {
  public function getInfoForm() {
    $form = \Drupal::formBuilder()->getForm(GetInfoForm::class);
    return [
      '#theme' => 'get-info',
      '#form' => $form
    ];
  }
  public function showReviewList() {
    drupal_flush_all_caches();
    $reviewsList = new ShowEntityList();
    $infoEntity = $reviewsList->buildEntity();
    return $infoEntity;
  }
  public function editReview() {

  }
}
