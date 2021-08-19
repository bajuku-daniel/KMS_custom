<?php
namespace Drupal\kms_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
 * Provides a 'Similar Works' Block.
 *
 * @Block(
 *   id = "similarWorks",
 *   admin_label = @Translation("Similar Works"),
 *   category = @Translation("Similar Works"),
 * )
 */
class similarWorksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
    }
    $database = \Drupal::database();
    $sql = "select ti2.nid, count(*) from taxonomy_index ti1 join taxonomy_term_field_data fd1 on fd1.tid = ti1.tid and fd1.vid='tags' join taxonomy_index ti2 on ti1.tid = ti2.tid and ti1.nid<>ti2.nid where ti1.nid = :nid group by ti2.nid having count(*)>=:count order by count(*) desc";
    $query = $database->query($sql, [':nid' => $nid, ':count' => 3]);

    $result = $query->fetchAll();
    $result = array_slice($result, 0, 3);

    $similar_works = [];
    foreach ($result as $item){
      $view_builder = \Drupal::entityManager()->getViewBuilder('node');
      $node = \Drupal::entityManager()->getStorage('node')->load($item->nid);
      $similar_works[$item->nid]['node_teaser'] = $view_builder->view($node, 'teaser');
    }

    $build = [];
    $build['#theme'] = 'similar_works_block';
    $build['#data'] = $similar_works ;

    return $build;

  }

}

