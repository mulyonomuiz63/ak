<div style="height:100% ; background-color: #808080;
  display: flex;
  align-items: center;   justify-content: center;">
  <div style="">
    <?php if ($fil == 1) { ?>
      <img src="<?php echo base_url('uploads/jurnal/thumbnails/' . $file) ?>" style="height:90%" alt="...">
    <?php } else { ?>
      <img src="<?php echo base_url('uploads/jurnal/' . $file) ?>" style="height:80%" alt="...">
    <?php } ?>
  </div>
</div>