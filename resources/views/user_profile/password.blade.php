<div class="ibox">
    <div class="ibox-content">
        <h2>Change Password</h2>
        <form data-parsley-validate action="/account/password" method="post" class="form-horizontal">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="form-group">
                <label class="col-md-3 control-label">Current Password</label>
                <div class="col-md-5">
                    <input type="password" name="current" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">New Password</label>
                <div class="col-md-5">
                    <input id="new" type="password" name="new" class="form-control" data-parsley-minlength="6" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Confirm Password</label>
                <div class="col-md-5">
                    <input type="password" name="confirm" class="form-control" data-parsley-equalto="#new" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-8">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </div>
        </form>
    </div>
</div>
