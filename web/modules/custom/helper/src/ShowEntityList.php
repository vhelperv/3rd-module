<?php

namespace Drupal\helper;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\Core\Entity\EntityTypeManagerInterface;
/**
 * Class for building the list of reviews.
 */
class ShowEntityList {
  /**
   * Builds the list of reviews.
   */
  public function buildEntity() {
    // Get the current user information
    $current_user = \Drupal::currentUser();
    // Check if the current user has the administrator role
    $is_admin = in_array('administrator', $current_user->getRoles());

    // Load the Entity Type Manager.
    $entity_type_manager = \Drupal::entityTypeManager();

    // Load all entities of type 'helper'.
    $entities = $entity_type_manager->getStorage('helper')->loadMultiple();


    $entity_data = [];
    // Loop through entities and access field values.
    foreach ($entities as $entity) {
      // Check if the current user has the necessary permission
      $isAdmin = \Drupal::currentUser()->hasPermission('administer site configuration');
      $entity_data[] = [
        'user_name' => $entity->get('user_name')->value,
        'user_email' => $entity->get('user_email')->value,
        'user_phone' => $entity->get('user_phone')->value,
        'review' => $entity->get('review')->value,
        'avatar' =>  $this->buildAvatarImageMarkup($entity->get('avatar_id')->value),
        'review_image' => $this->buildReviewsImageMarkup($entity->get('review_image_id')->value),
        'created' => date('m/d/Y H:i:s', $entity->get('created')->value),
        'edit' => $is_admin ? $this->buildEditLink($entity->get('id')->value) : '',
        'delete' => $is_admin ? $this->buildDeleteLink($entity->get('id')->value) : '',
        'id' => $entity->get('id')->value,
      ];
    }

    // Build the render array for the reviews
    $infoEntity = [
      '#theme' => 'list-entity',
      '#entity_data' => $entity_data,
      '#is_admin' => $is_admin
    ];

    return $infoEntity;
  }

  /**
   * Builds markup for the avatar image.
   */
  protected function buildAvatarImageMarkup($avatarImageId) {
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
    return render($avatar_markup);
  }

  /**
   * Builds markup for the review image.
   */
  protected function buildReviewsImageMarkup($reviewImageId) {
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
    return render($review_image_markup);
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
    return render($edit_link);
  }

  /**
   * Builds a delete link for a review.
   */
  protected function buildDeleteLink($idReview) {
    // Attach library for modal window
    $delete_link['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $delete_link = [
      '#markup' => '<a href="/confirmation-delete/' . $idReview . '" class="use-ajax delete-review button" data-dialog-type="modal">Delete</a>',
    ];
    return render($delete_link);
  }
}
