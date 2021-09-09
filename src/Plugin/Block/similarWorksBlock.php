<?php
namespace Drupal\kms_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\taxonomy\Entity\Term;
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
    // get the actual entity
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
    }

    // create the Drupal Database logic
    $database = \Drupal::database();
    $sql    = "select ti2.nid, count(*), GROUP_CONCAT(ti2.tid order by ti2.tid) as tids from taxonomy_index ti1 join taxonomy_term_field_data fd1 on fd1.tid = ti1.tid and fd1.vid='tags' join taxonomy_index ti2 on ti1.tid = ti2.tid and ti1.nid<>ti2.nid join node n1 on ti1.nid = n1.nid join node n2 on ti2.nid = n2.nid where ti1.nid = :nid and n1.type = n2.type and exists (select 1 from node__field_freigabe ff where ff.entity_id = n2.nid and ff.field_freigabe_target_id=28) group by ti2.nid having count(*)>=:count order by count(*) desc limit :limit";
    $query  = $database->query($sql, [':nid' => $nid, ':count' => 3, ':limit' => 40]);
    $result = $query->fetchAll();

    $similar_works = [];
    $similar_tag_id_list = [];

    // run through all results and collect the required infos
    foreach ($result as $item){
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($item->nid);
      // ok - lets get the teaser - you can change the view_mode here or attach some more infos like "full" ..
      $similar_works[$item->nid]['node_teaser'] = $view_builder->view($node, 'teaser');

      // add the tag id list to the node and push out duplicates
      $tids_node = array_unique(explode(',', $item->tids));
      $similar_works[$item->nid]['node_tags'] = implode(',', $tids_node);

      // handle the tag ids list and build the array scheme
      foreach($tids_node as $tid) {
        if (!in_array($tid, $similar_tag_id_list)) {
          $term = Term::load($tid);
          $name = $term->getName();
          $similar_tag_id_list[$tid] = array(
            'tag_name' => $name,
            'tag_id' => $tid
          );
        }
      }
    }

    // build the block theme structure
    $build = [];
    $build['#theme'] = 'similar_works_block';
    $build['#data'] = $similar_works ;
    $build['#tag_list'] = $similar_tag_id_list ;
    // attach the js file
    $build['#attached']['library'][] = 'kms_custom/similarWorks' ;

    // finally return the stuff
    return $build;
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

}

