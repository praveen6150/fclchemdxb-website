<?php
// This file handles both list and view based on $page variable
if (($page ?? 'enquiries') === 'enquiry_view'):
?>

<div class="breadcrumb">
    <a href="/admin/enquiries">Enquiries</a>
    <span>/</span>
    <span>View Enquiry #<?php echo $enquiry['id']; ?></span>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-envelope" style="color:#C8102E;margin-right:8px;"></i>Enquiry Details</h2>
        <div style="display:flex;gap:8px;">
            <a href="mailto:<?php echo e($enquiry['email']); ?>" class="btn btn-sm btn-success">
                <i class="fas fa-reply"></i> Reply
            </a>
            <?php if(isAdmin()): ?>
            <a href="/admin/enquiries/delete/<?php echo $enquiry['id']; ?>" class="btn btn-sm btn-danger confirm-delete">
                <i class="fas fa-trash"></i> Delete
            </a>
            <?php endif; ?>
            <a href="/admin/enquiries" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <div class="page-card-body">
        <div class="enquiry-detail">
            <div class="enquiry-row"><div class="enquiry-label">Name</div><div class="enquiry-value"><strong><?php echo e($enquiry['name'] ?? '-'); ?></strong></div></div>
            <div class="enquiry-row"><div class="enquiry-label">Email</div><div class="enquiry-value"><a href="mailto:<?php echo e($enquiry['email'] ?? ''); ?>" style="color:#C8102E;"><?php echo e($enquiry['email'] ?? '-'); ?></a></div></div>
            <div class="enquiry-row"><div class="enquiry-label">Phone</div><div class="enquiry-value"><?php echo e($enquiry['phone'] ?? '-'); ?></div></div>
            <div class="enquiry-row"><div class="enquiry-label">Industry</div><div class="enquiry-value"><span class="badge badge-primary"><?php echo e($enquiry['industry'] ?? '-'); ?></span></div></div>
            <div class="enquiry-row"><div class="enquiry-label">Date</div><div class="enquiry-value"><?php echo e($enquiry['created_at'] ?? '-'); ?></div></div>
            <div class="enquiry-row">
                <div class="enquiry-label">Message</div>
                <div class="enquiry-value" style="white-space:pre-wrap;background:#f8f9fa;padding:12px;border-radius:8px;line-height:1.6;">
                    <?php echo e($enquiry['message'] ?? '-'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<?php
$enquiries = array_reverse(readJson('enquiries.json'));
$success   = flash('success');
?>
<?php if($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo e($success); ?></div><?php endif; ?>

<div class="page-card">
    <div class="page-card-header">
        <h2><i class="fas fa-envelope" style="color:#C8102E;margin-right:8px;"></i>Enquiries
            <span class="badge badge-primary" style="margin-left:8px;"><?php echo count($enquiries); ?></span>
        </h2>
    </div>
    <div class="page-card-body" style="padding:0;">
        <?php if(empty($enquiries)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No enquiries received yet.<br>They will appear here when visitors submit the contact form.</p>
        </div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Industry</th><th>Date</th><th>Actions</th>
                </tr></thead>
                <tbody>
                    <?php foreach($enquiries as $enq): ?>
                    <tr>
                        <td><?php echo $enq['id']; ?></td>
                        <td><strong><?php echo e($enq['name'] ?? '-'); ?></strong></td>
                        <td><?php echo e($enq['email'] ?? '-'); ?></td>
                        <td><?php echo e($enq['phone'] ?? '-'); ?></td>
                        <td><span class="badge badge-info"><?php echo e($enq['industry'] ?? '-'); ?></span></td>
                        <td><?php echo e($enq['created_at'] ?? '-'); ?></td>
                        <td style="display:flex;gap:6px;">
                            <a href="/admin/enquiries/view/<?php echo $enq['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                            <a href="mailto:<?php echo e($enq['email']); ?>" class="btn btn-sm btn-success"><i class="fas fa-reply"></i></a>
                            <?php if(isAdmin()): ?>
                            <a href="/admin/enquiries/delete/<?php echo $enq['id']; ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>
