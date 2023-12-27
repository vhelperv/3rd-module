<?php

namespace Drupal\helper;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Class for building the list of reviews.
 */
class ShowEntityList {

  /**
   * Builds the list of reviews.
   */
  public function buildEntity() {
    // Get the current user information.
    $current_user = \Drupal::currentUser();
    // Check if the current user has the administrator role.
    $is_admin = in_array('administrator', $current_user->getRoles());

    // Load all entities of type 'helper'.
    $query = \Drupal::entityQuery('helper')
      ->sort('created', 'DESC');

    $ids = $query->execute();
    $entities = \Drupal::entityTypeManager()->getStorage('helper')->loadMultiple($ids);
    $entity_data = [];
    // Loop through entities and access field values.
    foreach ($entities as $entity) {
      $entity_data[] = [
        'user_name' => $entity->get('user_name')->value,
        'user_email' => $entity->get('user_email')->value,
        'user_phone' => $entity->get('user_phone')->value,
        'review' => $entity->get('review')->value,
        'avatar' => $this->buildAvatarImageMarkup($entity->get('avatar_id')->value),
        'review_image' => $this->buildReviewsImageMarkup($entity->get('review_image_id')->value),
        'created' => date('m/d/Y H:i:s', $entity->get('created')->value),
        'edit' => $is_admin ? $this->buildEditLink($entity->get('id')->value) : '',
        'delete' => $is_admin ? $this->buildDeleteLink($entity->get('id')->value) : '',
        'id' => $entity->get('id')->value,
      ];
    }

    return $entity_data;
  }

  /**
   * Builds markup for the avatar image.
   */
  protected function buildAvatarImageMarkup($avatarImageId) {
    $avatar_markup = [];
    // Check if avatarImageId is not provided or is NULL.
    if (!$avatarImageId) {
      $module_path = drupal_get_path('module', 'helper');
      // Use a default image URL when no avatar is provided.
      $default_avatar_url = base_path() . $module_path . '/misc/icons/default_avatar.png';

      $avatar_markup = [
        '#theme' => 'image',
        '#uri' => $default_avatar_url,
        '#alt' => t('User Avatar'),
        '#attributes' => [
          'class' => ['responsive-image'],
          'id' => 'default-avatar',
        ],
        '#prefix' => '<div class="image-container" id="image-container-default-avatar">',
        '#suffix' => '</div>',
      ];

      return $avatar_markup;
    }

    $avatar = File::load($avatarImageId);

    if ($avatar) {
      $avatar_url = file_create_url($avatar->getFileUri());

      $avatar_markup = [
        '#theme' => 'image',
        '#uri' => $avatar_url,
        '#alt' => t('User Avatar'),
        '#attributes' => [
          'class' => ['responsive-image'],
          'id' => 'responsive-image-' . $avatarImageId,
        ],
        '#prefix' => '<div class="image-container" id="image-container-' . $avatarImageId . '">',
        '#suffix' => '</div>',
      ];
    }
    return $avatar_markup;
  }

  /**
   * Builds markup for the review image.
   */
  protected function buildReviewsImageMarkup($reviewImageId) {
    $review_image_markup = [];
    if ($reviewImageId) {
      $reviewImage = File::load($reviewImageId);

      if ($reviewImage) {
        $review_image_url = file_create_url($reviewImage->getFileUri());

        $review_image_markup = [
          '#theme' => 'image',
          '#uri' => $review_image_url,
          '#alt' => t('User Avatar'),
          '#attributes' => [
            'class' => ['responsive-image'],
            'id' => 'responsive-image-' . $reviewImageId,
          ],
          '#prefix' => '<div class="image-container" id="image-container-' . $reviewImageId . '">',
          '#suffix' => '</div>',
        ];
      }
    }
    return $review_image_markup;
  }

  /**
   * Builds an edit link for a review.
   */
  protected function buildEditLink($idReview) {
    $url = Url::fromRoute('helper.edit_review', ['id' => $idReview]);
    $edit_link = Link::fromTextAndUrl('Edit', $url)->toRenderable();
    $edit_link['#attributes']['class'][] = 'edit-review';
    $edit_link['#attributes']['class'][] = 'button';
    $edit_link['#attributes']['id'] = 'edit-review-' . $idReview;
    return $edit_link;
  }

  /**
   * Builds a delete link for a review.
   */
  protected function buildDeleteLink($idReview) {
    // Attach library for modal window.
    $delete_link['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $url = Url::fromRoute('helper.form-submit-delete', ['id' => $idReview])->toString();
    $delete_link = [
      '#markup' => '<a href="' . $url . '" class="use-ajax delete-review button" data-dialog-type="modal">Delete</a>',
    ];
    return $delete_link;
  }

}
