<?php

namespace Drupal\helper\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the MyModuleData entity.
 *
 * @ContentEntityType(
 *   id = "helper",
 *   label = @Translation("Helper Data"),
 *   base_table = "helper",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "user_name" = "user_name",
 *     "user_email" = "user_email",
 *     "user_phone" = "user_phone",
 *     "created" = "created",
 *   },
 * )
 */

class HelperEntity extends ContentEntityBase implements ContentEntityInterface {
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the my custom entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the my custom entity.'))
      ->setReadOnly(TRUE);
    $fields['user_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the person filling the form.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 100);
    $fields['user_email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Email'))
      ->setDescription(t('The email address of the person filling the form.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255);
    $fields['user_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Phone'))
      ->setDescription(t('The phone number of the person filling the form.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 10);
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setReadOnly(TRUE);

    return $fields;
  }

}
