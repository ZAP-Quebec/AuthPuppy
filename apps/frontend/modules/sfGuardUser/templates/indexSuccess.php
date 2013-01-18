<?php use_helper('I18N', 'Date') ?>
<?php include_partial('sfGuardUser/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('User list', array(), 'messages') ?></h1>

  <?php include_partial('sfGuardUser/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('sfGuardUser/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('sfGuardUser/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('sf_guard_user_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('sfGuardUser/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('sfGuardUser/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('sfGuardUser/list_actions', array('helper' => $helper)) ?>
      <li><?php echo link_to(__('Groups'), '@sf_guard_group'); ?></li><li><?php echo link_to(__('Permissions'), '@sf_guard_permission'); ?></li>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('sfGuardUser/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
