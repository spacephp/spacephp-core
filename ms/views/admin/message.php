<?php if (isset($_SESSION['message'])) { ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <?=$_SESSION['message']?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
unset($_SESSION['message']);
}
?>
<?php if (isset($_SESSION['error'])) { ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <?=$_SESSION['error']?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
unset($_SESSION['error']);
}
?>