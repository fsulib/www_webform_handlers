<?php

namespace Drupal\www_webform_handlers\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandler\EmailWebformHandler;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\www_webform_handlers\SubjectLiaisonsService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Emails a webform submission to selected subject liaison librarians.
 *
 * @WebformHandler(
 *    id = "email_liaisons",
 *    label = @Translation("Email Liaisons"),
 *    category = @Translation("Notification"),
 *    description = @Translation("Sends a webform submission to subject liaison librarians based on the selected FSU Department."),
 *    cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *    results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *    submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 *    tokens = TRUE,
 * )
 */

 class EmailLiaisonsWebformHandler extends EmailWebformHandler  {

  /**
   * The Subject Liaisons Service
   *
   * @var \Drupal\www_webform_handlers\SubjectLiaisonsService
   */
  protected $liaisonsService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->liaisonsService = $container->get('www_webform_handlers.subject_liaisons');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $configForm = parent::buildConfigurationForm($form, $form_state);
    $configForm['to']['to_mail'] = [];
    $configForm['to']['to_mail'] = [
      '#type' => 'details',
      '#title' => $this->t('To email'),
      '#description' => $this->t('This is automatically set to the Subject Liaison email for the FSU Department selected on the form.')
    ];

    return $configForm;
  }


  public function sendMessage(webformSubmissionInterface $webform_submission, array $message) {
    $submission_array = $webform_submission->getData();

    # only send if status is new
    if ($submission_array['item_status'] == 'new') {
      #get subject liaison emails from selected department
      $fsu_department = $submission_array['fsu_department'];
      $recipients = $this->liaisonsService->getEmailsByDepartmentId($fsu_department);

      $message['to_mail'] = join(",", $recipients);

      return parent::sendMessage($webform_submission, $message);
    }
  }
}
