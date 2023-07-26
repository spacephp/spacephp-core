<?php include(__DIR__ . '/../header.php');?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?=$items->title()?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/myadmin">Home</a></li>
              <li class="breadcrumb-item active"><?=$items->title()?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
      <?php include(__DIR__ . '/../message.php');?>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- /.row -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><a href="/myadmin/<?=$items::$collection?>/create" class="btn btn-primary">New Item</a></h3>
                <div class="card-tools">
                <form>
                  <div class="input-group input-group-sm" style="width: 150px;">
                  
                    <input type="text" name="s" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>

                    </div>
                  
                  </div>
                  </form>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                        <?php if ($items->has('thumbnail')) { ?>
                        <th>Thumbnail</th>
                        <?php } ?>
                        <?php if ($items->has('title') && $items->has('slug')) { ?>
                        <th>Title</th>
                        <?php } ?>
                    <?php foreach ($items::$fields as $field) {
                        if ($field == 'title' && ! $items->has('slug')) {
                            ?>
                        <th>Title</th>
                            <?php
                        } 
                        if (in_array($field, ['host', 'thumbnail', 'slug', 'categories', 'tags', 'title', 'content', 'description', 'variations', 'gallery', 'items'])) continue;    
                    ?>
                        <th><?=ucwords(str_replace('_', ' ', $field))?></th>
                    <?php } ?>
                    <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items->documents as $doc) { ?>
                    <tr>
                        <?php if ($items->has('thumbnail')) { ?>
                        <td><img src="<?=$doc->getThumbnail()?>" style="width:100px; heigh:100px"/></td>
                        <?php } ?>
                        <?php if ($items->has('title') && $items->has('slug')) { ?>
                        <td>
                            <a href="<?=$doc->getPermalink()?>" target="_blank"><?=$doc->title?></a>
                            <div>
                            <small>Categories: <?=$doc->getKey('categories')?></small><br/>
                            <small>Tags: <?=$doc->getKey('tags')?></small>
                            </div>
                        </td>
                        <?php } ?>
                      <?php foreach ($items::$fields as $field) { 
                        if ($field == 'title' && ! $items->has('slug')) {
                            ?>
                        <td><?=$doc->{$field}?></td>
                            <?php
                        }
                        if (in_array($field, ['host', 'thumbnail', 'slug', 'categories', 'tags', 'title', 'content', 'description', 'variations', 'gallery', 'items'])) continue;?>
                        <td><?=$doc->getKey($field)?></td>
                      <?php } ?>
                      <td><a href="/myadmin/<?=$collection?>/<?=$doc->_id?>/edit">Edit</a> | 
                        <form action="/myadmin/<?=$collection?>/<?=$doc->_id?>" method="POST">
                          <input type="hidden" name="_method" value="DELETE">
                          <button type="submit" onclick="if (! confirm('Are you sure?')) return false;">Delete</button>
                        </form>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <?php $items->links();?>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
<?php include(__DIR__ . '/../footer.php');?>