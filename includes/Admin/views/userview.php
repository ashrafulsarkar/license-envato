<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'User List', 'envatolicenser' ); ?></h1>
    <hr class="wp-header-end">
    <?php
    
    $table->prepare_items();
    $table->display();
    ?>
</div>