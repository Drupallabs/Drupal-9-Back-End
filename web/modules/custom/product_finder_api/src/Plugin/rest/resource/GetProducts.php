<?php

namespace Drupal\product_finder_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\node\Entity\Node;

/**
 * Provides a Demo Resource.
 *
 * @RestResource(
 *   id = "products",
 *   label = @Translation("Get Products"),
 *   uri_paths = {
 *     "canonical" = "/get/products"
 *   }
 * )
 */
class GetProducts extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $nids = \Drupal::entityQuery('node')->condition('type', 'productfinder_question')->execute();
    $nodes = Node::loadMultiple($nids);
    $response = $this->showNodes($nodes);
    return new ResourceResponse($response);
  }

  /**
   * Function For Showing Nodes.
   */
  private function showNodes($nodes) {
    foreach ($nodes as $key => $node) {
      $output[] =
      [
        'id' => $key,
        'title' => $node->get('title')->value,
        'field_before_content' => $node->get('field_before_content')->value,
        'field_description' => $node->get('field_description')->value,
        'field_answer_field_1' => $this->innerNodes($node, 1),
        'field_answer_field_2' => $this->innerNodes($node, 2),
      ];
    }
    return $output;
  }

  /**
   * Function To Get Paragraph Fields
   */
  private function innerNodes($node, $field_answer) {
    foreach ($node->{'field_answer_field_' . $field_answer} as $reference) {
      $paragraph_fields[] = [
        'field_answer_ref_id' => $reference->entity->field_answer_ref_id->value,
        'field_answer_title' => $reference->entity->field_answer_title->value,
        'field_answer_type' => $reference->entity->field_answer_type->value,
        'field_next_question' => $this->showNextQuestionFields($reference),
        'field_product_link' => $this->showProductLinkFields($reference),
        'field_category_link' => $this->showCategoryFields($reference),
        'field_shade_selector' => $this->showShadeSelectorFields($reference),
      ];
    }
    return $paragraph_fields;
  }

  /**
   * Function To Get Next Question Fields.
   */
  private function showNextQuestionFields($reference) {
    $paras = $reference->entity->field_next_question->referencedEntities();
    foreach ($paras as $para) {
      $fieldValue = $para->get('title')->value;
    }
    return $fieldValue;
  }

  /**
   * Function To Get Product Link Fields.
   */
  private function showProductLinkFields($reference) {
    $paras = $reference->entity->field_product_link->referencedEntities();
    foreach ($paras as $para) {
      $fieldValue = $para->get('title')->value;
    }
    return $fieldValue;
  }

  /**
   * Function To Get Category Fields.
   */
  private function showCategoryFields($reference) {
    $paras = $reference->entity->field_category_link->referencedEntities();
    foreach ($paras as $para) {
      $fieldValue = $para->get('name')->value;
    }
    return $fieldValue;
  }

  /**
   * Function To Get Shade Selector Fields.
   */
  private function showShadeSelectorFields($reference) {
    $paras = $reference->entity->field_shade_selector->referencedEntities();
    foreach ($paras as $para) {
      $fieldValue = $para->get('title')->value;
    }
    return $fieldValue;
  }

}
