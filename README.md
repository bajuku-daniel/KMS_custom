# Drupal 8 KMS Module
This Module provides a Custom Block called **Similar Works** that outputs similar nodes as LI elements that share at least 3 common tags and second List as LI elements of the Matching Tags.
If you want to Limit the Results Uncomment and edit the Line **32** in "**similarWorksBlock.php**" file.

The Entity output is defined as `$view_builder->view($node, 'teaser');` and atm there are no configuration settings added.

The Tag List output is defined as `tag_name` and `tag_id`

## Template
You can override the block twig template in your Theme or in the module Directory. The .html.twig file is called **similarWorks.html.twig** and is located under the *templates* directory

For the styling and the JS logic 2 Data-Atributes are added to the LI elements:
`data-tag-id-list` _(comma seperated list of tag_ids)_ and `data-tag-id`.



## SQL
`
select ti2.nid, count(*), GROUP_CONCAT(ti2.tid order by ti2.tid) as tids from taxonomy_index ti1 join taxonomy_term_field_data fd1 on fd1.tid = ti1.tid and fd1.vid='tags' join taxonomy_index ti2 on ti1.tid = ti2.tid and ti1.nid<>ti2.nid join node n1 on ti1.nid = n1.nid join node n2 on ti2.nid = n2.nid where ti1.nid = :nid and n1.type = n2.type group by ti2.nid having count(*)>=:count order by count(*) desc
`
