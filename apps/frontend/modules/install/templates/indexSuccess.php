<form action="<?php echo url_for("@install_index_page?page=$page") ?>" method="post">

      <?php $partial = "page$page";
        include_partial("install/$partial", array('data' => $sf_data));
        ?>

</form>

<?php if ($hasEvents) { ?>
  <p>Execution results: </p><?php 
  foreach ($sf_data->getRaw('events') as $event) {
    print $event . "<br/>";
  }
}



