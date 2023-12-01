<?php

namespace Drupal\helper\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\helper\Entity\HelperEntity;


/**
 * Form class for collecting user information for the guest book.
 */
class GetInfoForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'get_info';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Username field
    $form['user_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter your name:'),
      '#required' => TRUE,
      '#description' => $this->t('The name must be between 2 and 100 characters long.'),
      '#suffix' => '<div id="name-field-wrapper" class="error"></div>',
      '#ajax' => [
        'callback' => '::validateName',
        'event' => 'input',
      ],
    ];

    // User email field
    $form['user_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Enter your email:'),
      '#required' => TRUE,
      '#description' => $this->t('The email address can only contain Latin letters, the underscore character (_), or the hyphen character (-).'),
      '#suffix' => '<div id="email-field-wrapper" class="error"></div>',
      '#ajax' => [
        'callback' => '::validateEmail',
        'event' => 'input',
      ],
    ];

    // User phone field
    $form['user_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Enter your phone number:'),
      '#required' => TRUE,
      '#description' => $this->t('The number must be in the XXXXXXXXXX format.'),
      '#placeholder' => '(XXX)XXXXXXX',
      '#suffix' => '<div id="phone-field-wrapper" class="error"></div>',
      '#attributes' => [
        'maxlength' => 10,
      ],
      '#ajax' => [
        'callback' => '::validatePhone',
        'event' => 'input',
      ],
    ];
    // Avatar managed file field
    $form['avatar'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Avatar Upload(optional):'),
      '#multiple' => FALSE,
      '#description' => $this->t('Add your photo to the comment with the extension jpg, jpeg, or png. The maximum size is 2MB'),
      '#upload_location' => 'public://helper/avatar',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [2 * 1024 * 1024],
      ],
    ];

    $form['review'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enter your comment:'),
      '#required' => TRUE,
      '#suffix' => '<div id="review-field-wrapper" class="error"></div>'
    ];

    // Image for review managed file field
    $form['review_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload a photo to the comment(optional):'),
      '#multiple' => FALSE,
      '#description' => $this->t('Add your photo(avatar) with the extension jpg, jpeg, or png. The maximum size is 2MB'),
      '#upload_location' => 'public://helper/review_image',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
        'file_validate_size' => [5 * 1024 * 1024],
      ],
    ];

    // Form actions
    $form['actions']['#type'] = 'actions';
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#ajax' => [
        'callback' => '::ajaxSubmit',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * Ajax callback to validate the username.
   */
  public function validateName(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $userName = $form_state->getValue('user_name');
    $errorMessageInvalid = '<span id="name-error-message" style="color: red; font-size: 15px;">The name must be between 2 and 100 characters long.</span>';

    // Check if the username length is within the valid range
    if (mb_strlen($userName, 'UTF-8') < 2 || mb_strlen($userName, 'UTF-8') > 100) {
      $response->addCommand(new RemoveCommand('#name-error-message'));
      $response->addCommand(new AppendCommand('#name-field-wrapper', $errorMessageInvalid));
      $response->addCommand(new InvokeCommand('#edit-user-name', 'addClass', ['error']));
    } else {
      $response->addCommand(new RemoveCommand('#name-error-message'));
      $response->addCommand(new InvokeCommand('#edit-user-name', 'removeClass', ['error']));
    }

    return $response;
  }

  /**
   * Ajax callback to validate the user email.
   */
  public function validateEmail(array &$form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    $userEmail = $form_state->getValue('user_email');

    // Error messages for different validation scenarios
    $errorMessageInvalid = '<span id="email-error-message" style="color: red; font-size: 15px;">The email address is invalid.</span>';
    $errorMessageMustContain = '<span id="email-error-message" style="color: red; font-size: 15px;">Email must contain @.</span>';
    $errorMessageMissingDomain = '<span id="email-error-message" style="color: red; font-size: 15px;">A domain is required. For example: @gmail.com</span>';

    // Validate the email address format using a regular expression
    if (!preg_match('/^[a-zA-Z\-_@.]+$/', $userEmail)) {
      $this->addEmailErrorCommands($response, $errorMessageInvalid);
    } elseif (!str_contains($userEmail, '@')) {
      $this->addEmailErrorCommands($response, $errorMessageMustContain);
    } elseif (substr($userEmail, -1) === '@') {
      $this->addEmailErrorCommands($response, $errorMessageMissingDomain);
    } else {
      $this->removeEmailErrorCommands($response);
    }

    return $response;
  }

  /**
   * Helper method to add email error commands.
   */
  private function addEmailErrorCommands(AjaxResponse &$response, $errorMessage): void {
    $response->addCommand(new InvokeCommand('#edit-user-email', 'addClass', ['error']));
    $response->addCommand(new RemoveCommand('#email-error-message'));
    $response->addCommand(new AppendCommand('#email-field-wrapper', $errorMessage));
  }

  /**
   * Helper method to remove email error commands.
   */
  private function removeEmailErrorCommands(AjaxResponse &$response): void {
    $response->addCommand(new InvokeCommand('#edit-user-email', 'removeClass', ['error']));
    $response->addCommand(new RemoveCommand('#email-error-message'));
  }

  /**
   * Ajax callback to validate the user phone.
   */
  public function validatePhone(array &$form, FormStateInterface $form_state): AjaxResponse
  {
    $response = new AjaxResponse();
    $userPhone = $form_state->getValue('user_phone');
    $errorMessageInvalidFormat = '<span id="phone-error-message" style="color: red; font-size: 15px;">You can use only whole numbers</span>';
    $errorMessageShortNumber = '<span id="phone-error-message" style="color: red; font-size: 15px;">The number must have 10 digits</span>';

    // Check if the phone number contains non-numeric characters
    if (preg_match('/[^0-9]/', $userPhone)) {
      $response->addCommand(new RemoveCommand('#phone-error-message'));
      $response->addCommand(new AppendCommand('#phone-field-wrapper', $errorMessageInvalidFormat));
      $response->addCommand(new InvokeCommand('#edit-user-phone', 'addClass', ['error']));
    } elseif(mb_strlen($userPhone) < 10) {
      $response->addCommand(new RemoveCommand('#phone-error-message'));
      $response->addCommand(new AppendCommand('#phone-field-wrapper', $errorMessageShortNumber));
      $response->addCommand(new InvokeCommand('#edit-user-phone', 'addClass', ['error']));
    } else {
      $response->addCommand(new RemoveCommand('#phone-error-message'));
      $response->addCommand(new InvokeCommand('#edit-user-phone', 'removeClass', ['error']));
    }

    return $response;
  }

  /**
   * Ajax callback to handle form submission.
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state): AjaxResponse {
    // Process form submission
    $values = $form_state->getValues();
    $userName = $values['user_name'];
    $userEmail = $values['user_email'];
    $userPhone = $values['user_phone'];
    $userReview = $values['review'];
    $flag = TRUE;

    // Initialize AjaxResponse object for handling AJAX responses
    $response = new AjaxResponse();

    // Validate user name
    if (trim($userName) == '') {
      $flag = FALSE;
      // Display error message for empty user name
      $errorMessageEmpty = '<span id="name-error-message" style="color: red; font-size: 15px;">Please enter your name</span>';
      $response->addCommand(new InvokeCommand('#edit-user-name', 'addClass', ['error']));
      $response->addCommand(new RemoveCommand('#name-error-message'));
      $response->addCommand(new AppendCommand('#name-field-wrapper', $errorMessageEmpty));
    } elseif (mb_strlen($userName, 'UTF-8') < 2 || mb_strlen($userName, 'UTF-8') > 100) {
      $flag = FALSE;
    }

    // Validate user email
    if (trim($userEmail) == '') {
      $flag = FALSE;
      // Display error message for empty user email
      $errorMessageEmpty = '<span id="email-error-message" style="color: red; font-size: 15px;">Please enter your email</span>';
      $response->addCommand(new InvokeCommand('#edit-user-email', 'addClass', ['error']));
      $response->addCommand(new RemoveCommand('#email-error-message'));
      $response->addCommand(new AppendCommand('#email-field-wrapper', $errorMessageEmpty));
    } elseif (!preg_match('/^[a-zA-Z\-_@.]+$/', $userEmail)) {
      $flag = FALSE;
    } elseif (!str_contains($userEmail, '@')) {
      $flag = FALSE;
    } elseif (substr($userEmail, -1) === '@') {
      $flag = FALSE;
    }

    // Validate user phone
    if (trim($userPhone) == '') {
      $flag = FALSE;
      // Display error message for empty user phone
      $errorMessageEmpty = '<span id="phone-error-message" style="color: red; font-size: 15px;">Please enter your phone number</span>';
      $response->addCommand(new InvokeCommand('#edit-user-phone', 'addClass', ['error']));
      $response->addCommand(new RemoveCommand('#phone-error-message'));
      $response->addCommand(new AppendCommand('#phone-field-wrapper', $errorMessageEmpty));
    } elseif (preg_match('/[^0-9]/', $userPhone)) {
      $flag = FALSE;
    } elseif(mb_strlen($userPhone) < 10) {
      $flag = FALSE;
    }

    // Validate user review
    if (trim($userReview) == '') {
      $flag = FALSE;
      // Display error message for empty review text area
      $errorMessageEmpty = '<span id="review-error-message" style="color: red; font-size: 15px;">Please enter your comment</span>';
      $response->addCommand(new InvokeCommand('#edit-review', 'addClass', ['error']));
      $response->addCommand(new RemoveCommand('#review-error-message'));
      $response->addCommand(new AppendCommand('#review-field-wrapper', $errorMessageEmpty));
    }

    // If all validations pass, insert data into the 'helper' table
    if ($flag === TRUE) {
      // Process avatar file
      $avatar_file = $values['avatar'];
      $avatar_id = null;

      if (!empty($avatar_file[0])) {
        $avatar = File::load($avatar_file[0]);

        if ($avatar) {
          $avatar_id = $avatar->id();
          $avatar->setPermanent();
          $avatar->save();
        }
      }

      // Process review image file
      $review_image_file = $values['review_image'];
      $review_image_id = null;

      if (!empty($review_image_file[0])) {
        $review_image = File::load($review_image_file[0]);

        if ($review_image) {
          $review_image_id = $review_image->id();
          $review_image->setPermanent();
          $review_image->save();
        }
      }

      // Insert data into the helper entity
      HelperEntity::create([
        'user_name' => $values['user_name'],
        'user_email' => $values['user_email'],
        'user_phone' => $values['user_phone'],
        'review' => $values['review'],
        'avatar_id' => $avatar_id,
        'review_image_id' => $review_image_id
      ])->save();

      // Display success message
      \Drupal::messenger()->addStatus('Comment added successfully.');
      drupal_flush_all_caches();

      // Redirect to the specified URL
      $url = Url::fromRoute('helper.show_list');
      $redirect_command = new RedirectCommand($url->toString());
      $response->addCommand($redirect_command);

    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
