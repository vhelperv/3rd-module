<?php

namespace Drupal\helper\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\helper\Entity\HelperEntity;

/**
 * Form controller for editing a review entity.
 *
 * Extends the GetInfoForm class and provides functionality to build and submit
 * the review edit form with AJAX validation and submission handling.
 */
class EditEntity extends GetInfoForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'review-edit-form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Call the parent buildForm method.
    $form = parent::buildForm($form, $form_state);

    // Get the review ID from the route parameters.
    $route_match = \Drupal::routeMatch();
    $route_parameters = $route_match->getParameters();
    $id = $route_parameters->get('id');

    // Check if $id is available.
    if (!empty($id)) {
      // Load the entity based on the ID.
      $entity = \Drupal::entityTypeManager()->getStorage('helper')->load($id);

      // Set default values for form elements.
      if ($entity) {
        $form['user_name']['#default_value'] = $entity->get('user_name')->value;
        $form['user_email']['#default_value'] = $entity->get('user_email')->value;
        $form['user_phone']['#default_value'] = $entity->get('user_phone')->value;
        $form['review']['#default_value'] = $entity->get('review')->value;

        // Handle file fields (avatar and review_image).
        if (!empty($entity->get('avatar_id')->value)) {
          $avatar = File::load($entity->get('avatar_id')->value);
          if ($avatar) {
            $form['avatar']['#default_value'] = [$avatar->id()];
          }
        }

        if (!empty($entity->get('review_image_id')->value)) {
          $review_image = File::load($entity->get('review_image_id')->value);
          if ($review_image) {
            $form['review_image']['#default_value'] = [$review_image->id()];
          }
        }
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state): AjaxResponse {
    // Process form submission.
    $values = $form_state->getValues();
    $userName = $values['user_name'];
    $userEmail = $values['user_email'];
    $userPhone = $values['user_phone'];
    $userReview = $values['review'];
    $flag = TRUE;

    // Initialize AjaxResponse object for handling AJAX responses.
    $response = new AjaxResponse();

    if (mb_strlen($userName, 'UTF-8') < 2 || mb_strlen($userName, 'UTF-8') > 100) {
      $flag = FALSE;
    }
    elseif (!preg_match('/^[a-zA-Z\-_@.]+$/', $userEmail)) {
      $flag = FALSE;
    }
    elseif (!str_contains($userEmail, '@')) {
      $flag = FALSE;
    }
    elseif (substr($userEmail, -1) === '@') {
      $flag = FALSE;
    }
    elseif (preg_match('/[^0-9]/', $userPhone)) {
      $flag = FALSE;
    }
    elseif (mb_strlen($userPhone) < 10) {
      $flag = FALSE;
    }

    // If all validations pass, insert data into the 'helper' table.
    if ($flag === TRUE) {
      // Process avatar file.
      $avatar_file = $values['avatar'];
      $avatar_id = NULL;

      if (!empty($avatar_file[0])) {
        $avatar = File::load($avatar_file[0]);

        if ($avatar) {
          $avatar_id = $avatar->id();
          $avatar->setPermanent();
          $avatar->save();
        }
      }

      // Process review image file.
      $review_image_file = $values['review_image'];
      $review_image_id = NULL;

      if (!empty($review_image_file[0])) {
        $review_image = File::load($review_image_file[0]);

        if ($review_image) {
          $review_image_id = $review_image->id();
          $review_image->setPermanent();
          $review_image->save();
        }
      }

      // Get the review ID from the route parameters.
      $route_match = \Drupal::routeMatch();
      $route_parameters = $route_match->getParameters();
      $id = $route_parameters->get('id');

      // Load the existing entity by ID.
      $entity = HelperEntity::load($id);

      // Check if the entity exists.
      if ($entity) {
        // Update the fields.
        $entity->set('user_name', $values['user_name']);
        $entity->set('user_email', $values['user_email']);
        $entity->set('user_phone', $values['user_phone']);
        $entity->set('review', $values['review']);
        $entity->set('avatar_id', $avatar_id);
        $entity->set('review_image_id', $review_image_id);

        // Save the updated entity.
        $entity->save();
      }

      // Display success message.
      $this->messenger->addMessage('Comment updated successfully.', 'status');

      // Redirect to the specified URL.
      $url = Url::fromRoute('helper.show_list');
      $redirect_command = new RedirectCommand($url->toString());
      $response->addCommand($redirect_command);
    }

    return $response;
  }

}
