<?php
    wp_enqueue_script('vendor-multi-select');
    wp_enqueue_style('vendor-multi-select');
    wp_enqueue_script('vendor-quicksearch');
    $args=array(
        'post_type' => 'noo_company',
        'post_status' => 'publish',
    );
    $company_query = new WP_Query($args);
    $candidate_id = get_current_user_id();
    $value = !empty( $candidate_id) ? get_user_meta( $candidate_id,'block_company',true) : array();
?>
<div class="notify-candidate col-md-12">
    <p>
        <?php echo esc_html__('Block Companies to whom you do not wish to reveal your resume. You have an option to not show your resume to selected companies, by blocking the company name.','noo') ?>
    </p>
</div>
<form autocomplete="on" method="post" novalidate="novalidate">
    <input type="hidden" name="action" value="candidate_block_company">
    <input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>">
    <div class="block">
        <select id='searchable' name="block_company[]" multiple='multiple'>
            <?php   while ( $company_query->have_posts()) :  $company_query->the_post();
                global $post;
                ?>
                <?php if(empty($post->post_title)) {
                    continue;
                }
                    $selected = (in_array((string)$post->ID,$value))? 'selected="selected"' : '';
                ?>
                <option <?php echo $selected ?> value='<?php echo $post->ID; ?>'><?php echo $post->post_title; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="block-company">
        <input type="submit" value="<?php echo esc_html__('Save Settings','noo'); ?> " class="btn-primary btn">
    </div>

</form>
<?php $placeholder_company = esc_html__('Companies registered with us.','noo');
      $placeholder_blocked = esc_html__('Companies marked as "Blocked" by you.','noo');
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#searchable').multiSelect({
            selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='<?php echo $placeholder_company ?>'>",
            selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='<?php echo  $placeholder_blocked ?>'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(){
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(){
                this.qs1.cache();
                this.qs2.cache();
            }
        });
    })

</script>