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

<?php echo $form->form_open_multipart('form', 'form', '/version_add?app=' . $appId, 'POST')
; ?>

    <?php
    echo $form->input_text([
        'name'      => 'version',
        'value'     => $versionName ?? "",
        'label'     => 'Version name',
        'style'     => 'width: 300px'
    ]);
    ?>

    <?php echo $form->fieldset_open('Deploy strategy'); ?>

    <div style="margin-bottom: 1rem">
            <div class="card">
                <?php
                echo $form->input_radio_inline([
                    'name'      => 'deploy_strategy',
                    'value'     => 'upload',
                    'selected'  => true,
                    'label'     => 'Upload'
                ]);
                ?>

                <?php
                echo $form->input_file('package', 'Deployment package (allowed: .zip, .gz)');
                ?>
            </div>

            <?php if (isset($app['s3_key']) && $app['s3_key']) : ?>
            <div class="card">
                <?php
                echo $form->input_radio_inline([
                    'name'      => 'deploy_strategy',
                    'value'     => 's3_pull',
                    'selected'  => true,
                    'label'     => 'S3 Pull'
                ]);
                ?>

                <?php
                echo $form->input_text('s3_url', 'S3 URL');
                ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <?php
                echo $form->input_radio_inline([
                    'name'      => 'deploy_strategy',
                    'value'     => 'http_pull',
                    'label'     => 'HTTP Pull'
                ]);
                ?>

                <?php
                echo $form->input_text('http_url', 'HTTP Pull URL');
                ?>
            </div>

            <?php if (isset($app['sftp_key']) && $app['sftp_key']) : ?>
            <div class="card">
                <?php
                echo $form->input_radio_inline([
                    'name'      => 'deploy_strategy',
                    'value'     => 'sftp_pull',
                    'label'     => 'SFTP Pull'
                ]);
                ?>

                <?php
                echo $form->input_text('sftp_url', 'Remote URL');
                ?>
            </div>
            <?php endif; ?>
    </div>

    <?php echo $form->fieldset_close(); ?>

    <?php
    echo $form->input_submit([
        'name' => 'submit',
        'value' => 'Upload & add version',
    ]);
    ?>

<?php echo $form->form_close(); ?>

