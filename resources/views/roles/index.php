<div class="row">
    <div class="col-md-12">
        <h1 class="page-header"><i class="fa fa-group"></i> Roles</h1>
    </div>
</div>

<div class="row filter-block">
    <div class="col-md-12">
        <div class="">
            <a href="/roles/create" class="btn-flat success">
                <i class="fa fa-plus"></i>
                New
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="datatable table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                   <tr>
                        <td><?php echo $role->id; ?></td>
                        <td><?php echo $role->name; ?></td>
                        <td><a href="/roles/edit/<?php echo $role->id; ?>" class="btn btn-default"><i class="fa fa-pencil"></i></a></td>
                   </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
