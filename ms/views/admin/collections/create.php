<?php include(__DIR__ . '/../header.php');?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Create</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/myadmin">Home</a></li>
              <li class="breadcrumb-item active">Create</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form method="POST" action="/myadmin/<?=$collection?>">
      <div class="row">
        <div class="col-md-8">
            <?php if ($model::has('title') && $model::has('slug')) { ?>
            <input type="text" name="title" class="form-control mb-3" placeholder="Title"/>
            <input type="text" name="slug" class="form-control mb-3" placeholder="Slug"/>
            <?php if ($model::has('content')) { ?>
            <textarea class="form-control mb-3" rows="20" name="content"></textarea>
            <?php } ?>
            <?php if ($model::has('description') && $collection == 'products') { ?>
            <textarea class="form-control mb-3" rows="20" name="description"></textarea>
            <?php } ?>
            <?php if ($model::has('description') && $collection == 'projects') { ?>
            <textarea class="form-control mb-3" rows="20" name="description"></textarea>
            <?php } ?>
            <?php if ($model::has('variations')) { ?>
            <textarea class="form-control mb-3" rows="20" name="variations" placeholder="Variation - json format"></textarea>
            <?php } ?>
            <?php if ($model::has('gallery')) { ?>
            <textarea class="form-control mb-3" rows="10" name="gallery" placeholder="Gallery - seperate by ||"></textarea>
            <?php } ?>
            <?php } else { ?>
            <?php foreach ($model::$fields as $field) { ?>
                <?php if (in_array($field, ['host', 'ip', 'user_agent', 'post_id'])) continue;?>
                <div class="form-group row">
                    <label for="<?=$field?>" class="col-sm-2 col-form-label"><?=ucwords(str_replace('_', ' ', $field))?></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="<?=$field?>" name="<?=$field?>" placeholder="<?=($field=='categories'||$field=='tags')?'Sepearate by ,':''?>"></textarea>
                    </div>
                </div>
            <?php } 
            }
            ?>
        </div>
        <div class="col-md-4">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                Action
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <button type="submit" class="btn btn-primary">Publish</button>
              <?php if ($model::has('title') && $model::has('slug')) { ?>
              <div class="accordion mt-3" id="accordionExample">
              <?php foreach ($model::$fields as $field) { ?>
                <?php if ($field == 'description' && $collection == 'products') continue; ?>
                <?php if ($field == 'description' && $collection == 'projects') continue; ?>
                <?php if (in_array($field, ['title', 'slug', 'host', 'content', 'gallery', 'variations'])) continue;?>
                <div class="card">
                    <button class="btn btn-secondary form-control text-left" type="button" data-toggle="collapse" data-target="#<?=$field?>" aria-expanded="true" aria-controls="collapseOne">
                    <?=ucwords(str_replace('_', ' ', $field))?>
                    </button>
                    <div id="<?=$field?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <textarea name="<?=$field?>" class="form-control" placeholder="<?=($field=='categories'||$field=='tags')?'Sepearate by ,':''?>"><?=$field=='published_on'?date('Y-m-d H:i:s', time()):''?></textarea>
                        </div>
                    </div>
                </div>
                <?php } ?>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <!-- /.col-->
      </div>
      <!-- ./row -->
      </form>
    </section>
    <!-- /.content -->
  </div>
<?php include(__DIR__ . '/../footer.php');?>