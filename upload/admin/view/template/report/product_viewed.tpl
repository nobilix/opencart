<?php echo $header; ?>
<div class="page-header">
  <div class="container">
    <div class="pull-right"><a href="<?php echo $reset; ?>" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $button_reset; ?></a></div>
    <h1><i class="fa fa-bar-chart-o fa-lg"></i> <?php echo $heading_title; ?></h1>
  </div>
</div>
<div id="content" class="container">
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="table-responsive">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <td class="text-left"><?php echo $column_name; ?></td>
          <td class="text-left"><?php echo $column_model; ?></td>
          <td class="text-right"><?php echo $column_viewed; ?></td>
          <td class="text-right"><?php echo $column_percent; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php if ($products) { ?>
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="text-left"><?php echo $product['name']; ?></td>
          <td class="text-left"><?php echo $product['model']; ?></td>
          <td class="text-right"><?php echo $product['viewed']; ?></td>
          <td class="text-right"><?php echo $product['percent']; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
          <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <div class="row">
    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
  </div>
</div>
<?php echo $footer; ?>