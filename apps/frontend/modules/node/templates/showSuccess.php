<table>
  <tbody>
    <tr>
      <th><?php echo __("Name:"); ?></th>
      <td><?php echo $node->getName() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Gw:"); ?></th>
      <td><?php echo $node->getGwId() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Description:"); ?></th>
      <td><?php echo $node->getDescription() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Deployment status:"); ?></th>
      <td><?php echo $node->getDeploymentStatus() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Created at:"); ?></th>
      <td><?php echo $node->getCreatedAt() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Updated at:"); ?></th>
      <td><?php echo $node->getUpdatedAt() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Civic number:"); ?></th>
      <td><?php echo $node->getCivicNumber() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Street name:"); ?></th>
      <td><?php echo $node->getStreetName() ?></td>
    </tr>
    <tr>
      <th><?php echo __("City:"); ?></th>
      <td><?php echo $node->getCity() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Province:"); ?></th>
      <td><?php echo $node->getProvince() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Country:"); ?></th>
      <td><?php echo $node->getCountry() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Postal Code:"); ?></th>
      <td><?php echo $node->getPostalCode() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Phone number:"); ?></th>
      <td><?php echo $node->getPublicPhoneNumber() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Email:"); ?></th>
      <td><?php echo $node->getPublicEmail() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Mass transit info:"); ?></th>
      <td><?php echo $node->getMassTransitInfo() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Last heartbeat at:"); ?></th>
      <td><?php echo $node->getLastHeartbeatAt() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Last heartbeat ip:"); ?></th>
      <td><?php echo $node->getLastHeartbeatIp() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Last heartbeat sys uptime:"); ?></th>
      <td><?php echo $node->getLastHeartbeatSysUptime() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Last heartbeat sys memfree:"); ?></th>
      <td><?php echo $node->getLastHeartbeatSysMemfree() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Last heartbeat sys load:"); ?></th>
      <td><?php echo $node->getLastHeartbeatSysLoad() ?></td>
    </tr>
    <tr>
      <th><?php echo __("Last heartbeat wifidog uptime:"); ?></th>
      <td><?php echo $node->getLastHeartbeatWifidogUptime() ?></td>
    </tr>
  </tbody>
</table>

<a href="<?php echo url_for('node/edit?id='.$node->getId()) ?>"><?php echo __("Edit"); ?></a>
&nbsp;
<a href="<?php echo url_for('node/index') ?>"><?php echo __("List"); ?></a>
