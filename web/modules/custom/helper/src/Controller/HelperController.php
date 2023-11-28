<?php

namespace Drupal\helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\helper\Form\GetInfoForm;

class HelperController extends ControllerBase {
  public function getInfoForm() {
    $form = \Drupal::formBuilder()->getForm(GetInfoForm::class);
    return [
      '#theme' => 'get-info',
      '#form' => $form
    ];
  }
}
