<?php $settings = readJson('settings.json'); ?>

<?php if($success ?? null): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e($success); ?></div><?php endif; ?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-cog" style="color:#C8102E;margin-right:8px;"></i>Site Settings</h2>
    </div>
    <div class="page-card-body">
        <form method="POST" action="/admin/settings">

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-building" style="color:#C8102E;margin-right:8px;"></i>Company Information
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="<?php echo e($settings['site_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="tagline" class="form-control" value="<?php echo e($settings['tagline'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"><?php echo e($settings['address'] ?? ''); ?></textarea>
            </div>

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin:24px 0 16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-phone" style="color:#C8102E;margin-right:8px;"></i>Contact Details
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo e($settings['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($settings['email'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Working Hours</label>
                    <input type="text" name="working_hours" class="form-control" value="<?php echo e($settings['working_hours'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Working Days</label>
                    <input type="text" name="working_days" class="form-control" value="<?php echo e($settings['working_days'] ?? ''); ?>">
                </div>
            </div>

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin:24px 0 16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-share-alt" style="color:#C8102E;margin-right:8px;"></i>Social Media
            </h3>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fab fa-linkedin" style="color:#0077b5;"></i> LinkedIn URL</label>
                    <input type="url" name="linkedin" class="form-control" value="<?php echo e($settings['linkedin'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook URL</label>
                    <input type="url" name="facebook" class="form-control" value="<?php echo e($settings['facebook'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fab fa-twitter" style="color:#1da1f2;"></i> Twitter URL</label>
                    <input type="url" name="twitter" class="form-control" value="<?php echo e($settings['twitter'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-youtube" style="color:#ff0000;"></i> YouTube URL</label>
                    <input type="url" name="youtube" class="form-control" value="<?php echo e($settings['youtube'] ?? ''); ?>">
                </div>
            </div>

            <h3 style="font-size:15px;font-weight:700;color:#1a1a2e;margin:24px 0 16px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">
                <i class="fas fa-search" style="color:#C8102E;margin-right:8px;"></i>SEO
            </h3>
            <div class="form-group">
                <label>Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="2"><?php echo e($settings['meta_description'] ?? ''); ?></textarea>
            </div>

            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
            </div>
        </form>
    </div>
</div>
