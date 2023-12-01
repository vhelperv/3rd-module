<?php

namespace Drupal\helper\Form;

use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Container\ContainerInterface;

/**
 * Form controller for the review deletion confirmation form.
 */
class ConfirmationDeleteForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new ConfirmationDeleteForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, MessengerInterface $messenger) {
    $this->entityTypeManager = $entityTypeManager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface|\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete-confirmation';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the current URL
    $current_url = \Drupal::request()->getRequestUri();
    $form_state->set('current_url', $current_url);

    // Get the review ID from the URL
    $id = $this->getEntityIdFromUrl($current_url);

    $entity = $this->entityTypeManager->getStorage('helper')->load($id);
    $name = $entity ? $entity->get('user_name')->value : '';

    // Display a message asking for confirmation
    $form['title'] = [
      '#markup' => '<p>' . t("Do you agree to delete @name's review?", ['@name' => $name]) . '</p>',
    ];

    // Form actions
    $form['actions']['#type'] = 'actions';
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete')
    ];
    $form['cancel'] = [
      '#type' => 'button',
      '#value' => $this->t('Cancel'),
      '#ajax' => [
        'callback' => '::cancelFunction',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the current URL
    $current_url = $form_state->get('current_url');

    // Get the review ID from the URL
    $id = $this->getEntityIdFromUrl($current_url);

    // Database query to delete the review
    $entity = $this->entityTypeManager->getStorage('helper')->load($id);

    // Check if the entity exists before deleting.
    if ($entity) {
      $entity->delete();
    }

    // Redirect to the specified URL
    $url = Url::fromRoute('helper.show_list');
    $form_state->setRedirectUrl($url);

    drupal_flush_all_caches();

    // Display success message
    $this->messenger->addWarning('Comment deleted successfully.');
  }

  /**
   * Get the review ID from the URL.
   */
  private function getEntityIdFromUrl($url) {
    // Parse the URL.
    $url_parts = parse_url($url);

    // Extract the path.
    $path = isset($url_parts['path']) ? $url_parts['path'] : '';

    // Use regular expression to extract the review ID from the URL path
    $matches = [];
    if (preg_match('/\/confirmation-delete\/(\d+)/', $path, $matches)) {
      return $matches[1];
    }

    return null;
  }

  /**
   * Cancel function to close the confirmation dialog.
   */
  public function cancelFunction(array &$form, FormStateInterface $form_state) : AjaxResponse {
    // Create a new Ajax response
    $response = new AjaxResponse();

    // Add commands to remove the confirmation dialog elements
    $response->addCommand(new RemoveCommand('.ui-dialog'));
    $response->addCommand(new RemoveCommand('.ui-front'));

    return $response;
  }
}
