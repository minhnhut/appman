<?php $this->layout('_layout'); ?>
<?php
    /** @var Formr $form */
    $form = $this->form();
?>

<div class="clearfix">
    <div class="float-left">
        <h2><?=$app['name']?> - Add a version</h2>
    </div>
</div>

<?php echo $form->form_open_multipart('form', 'form', '/version_add?app=' . $appId, 'POST', 'style="max-width: 500px"')
; ?>

    <?php
    echo $form->input_text([
        'name'      => 'version',
        'value'     => $versionName ?? "",
        'label'     => 'Version name',
    ]);
    ?>

    <div class="clearfix">
        <?php
        echo $form->input_file('package', 'Deployment package (allowed: .zip, .gz)');
        ?>
    </div>

    <?php
    echo $form->input_submit([
        'name' => 'submit',
        'value' => 'Upload & add version',
    ]);
    ?>

<?php echo $form->form_close(); ?>

