# Drupal 8 KMS Module
This Module provides a Custom Block called **Similar Works** that outputs similar nodes that share at least 3 common tags.

The output is defined as $view_builder->view($node, 'teaser'); and atm there are no configuration settings added.

## Template
You can override the block twig template in your Theme or in the module Directory. The .html.twig file is called **similarWorks.html.twig** and is located under the *templates* directory

## SQL
`select ti2.nid, count(*) from taxonomy_index ti1 join taxonomy_term_field_data fd1 on fd1.tid = ti1.tid and fd1.vid='tags' join taxonomy_index ti2 on ti1.tid = ti2.tid and ti1.nid<>ti2.nid where ti1.nid = :nid group by ti2.nid having count(*)>=:count order by count(*) desc`
