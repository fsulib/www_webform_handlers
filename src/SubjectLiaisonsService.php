<?php

namespace Drupal\www_webform_handlers;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class SubjectLiaisonsService {

  /**
   * An entity type manager interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new SubjectLiaisonsService object
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $etm
   */
  public function __construct(EntityTypeManagerInterface $etm) {
    $this->entityManager = $etm;
  }

  public function getEmailsByDepartmentId($id) {
    $emails = [];
    if (!empty($id)) {
      $userStorage = $this->entityManager->getStorage('user');
      $liaisons = $userStorage->loadByProperties(['field_liaison_fsu_department' => $id]);
      foreach ($liaisons as $liaison) {
        $email = $liaison->getEmail();
        array_push($emails, $email);
      }
    }
    return $emails;
  }
}
