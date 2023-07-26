<?php include('header.php') ;?>
<div class="content-wrapper" style="min-height: 2171.6px;">
    
    <style>.form-control{color:#fff}</style>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
            <?php include('message.php');?>
            <form class="form-horizontal" action="/ms/v1/settings" method="POST">
                <?php foreach ($options as $key => $option) { ?>
                <?php if ($option['type'] == 'section') { ?>
                    <hr/>
                    <h2 class="mb-3"><?=$option['title']?></h2>
                <?php continue; } ?>
                <div class="form-group row">
                  <label for="<?=$key?>" class="col-sm-4 col-form-label"><?=$option['title']?></label>
                  <div class="col-sm-8">
                    <?php switch ($option['type']) {
                        case 'text':
                            echo '<input type="text" class="form-control" name="' . $key . '" id="' . $key . '" placeholder="' . (isset($option['placeholder'])?$option['placeholder']:'') . '" value="' . $site->getKey($key) . '"/>';
                            break;
                        case 'textarea':
                            echo '<textarea class="form-control" name="' . $key . '" id="' . $key . '" placeholder="' . (isset($option['placeholder'])?$option['placeholder']:'') . '">' . $site->getKey($key) . '</textarea>';
                            break;
                    } ?>
                    
                  </div>
                </div>
                <?php } ?>
                
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info">Save</button>
                </div>
                <!-- /.card-footer -->
              </form>
            </div>
        </div>
        
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
<?php include('footer.php');?>