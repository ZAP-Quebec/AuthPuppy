<h1><?php echo __('Nodes list') ?></h1>

<p><?php echo __('There are %1% active nodes in the network.', array('%1%' => Doctrine_Core::getTable('Node')->getActiveNodes()->count())) ?></p>
<p><?php echo __('There are %1% active connections.', array('%1%' => Doctrine_Core::getTable('Connection')->getActiveConnections()->count())) ?></p>

<a href="<?php echo url_for('node_index'); ?>"><?php echo __("Remove filters"); ?></a>
<?php include_partial('node/filterForm', array('form' => $filter)) ?>

<table>
<thead>
  <tr>
    <th><?php echo __('Node Name') ?></th>
    <th><?php echo __('GW ID') ?></th>
    <th><?php echo __('WAN IP') ?></th>
    <th><?php echo __('WD Uptime') ?></th>
    <th><?php echo __('Last HB') ?></th>
    <th><?php echo __('Active Connections') ?></th>
    <th><?php echo __('Deployment status') ?></th>
  </tr>
</thead>
<?php foreach ($nodes as $node) : ?>
  <?php if ($node->isOnline()) : ?>
    <tr class="online">
  <?php else : ?>
    <tr class="offline">
  <?php endif ?>

    <td><a href="<?php echo url_for('node/show?id='.$node->getId()) ?>"><?php echo $node->getName(); ?></a></td>
    <td><?php echo $node->getGwId(); ?></td>
    <td><?php echo $node->getLastHeartbeatIp(); ?></td>
    <td>
      <?php if ($node->getLastHeartbeatWifidogUptime()) : ?>
      <?php echo time_ago_in_words(time() - $node->getLastHeartbeatWifidogUptime()); ?>
      <?php endif ?>
    </td>
    <td>
      <?php if ($node->getLastHeartbeatAt()) : ?>
      <?php echo time_ago_in_words(date_format(date_create($node->getLastHeartbeatAt()), "U")); ?>
      <?php endif ?>
    </td>
    <td><?php echo $node->getNumActiveConnections(); ?></td>
    <td><?php echo $node->getDeploymentStatus(); ?></td>

  </tr>
<?php endforeach ?>
</table>

  <a href="<?php echo url_for('node/new') ?>"><?php echo __("New"); ?></a>
