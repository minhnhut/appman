<?php
    /** @var Formr $form */
    $form = $this->form();
?>

<form method="POST" style="max-width: 700px">

    <div class="tabs tabs1">

        <ul class="header">
            <li>General</li>
            <li>S3 Credentials</li>
            <li>SSH Credentials</li>
            <li>API</li>
        </ul>

        <ul class="body">
            <li>

                <?php echo $form->fieldset_open('Icon'); ?>
                <div class="row">
                    <?php for ($i = 0; $i < 3; $i++) : ?>
                        <?php for ($j = 0; $j < 3; $j++) : ?>
                        <div class="column" style="text-align: center;">
                            <label>
                                <?php echo $this->insert('partial::icon', ['icon' => $i*3 + $j]); ?><br/>
                                <?php
                                    echo $form->input_radio_inline(
                                            "app_icon",
                                            "",
                                            strval($i*3 + $j),
                                            "",
                                            "",
                                            "",
                                        $i*3 + $j == ($app_icon ?? 0)
                                    );
                                ?>
                            </label>
                        </div>
                        <?php endfor; ?>
                    <?php endfor; ?>
                </div>
                <?php echo $form->fieldset_close(); ?>


                <?php
                echo $form->input_text([
                    'name'      => 'name',
                    'value'     => $name ?? "",
                    'label'     => 'App name',
                ]);
                ?>

                <?php if ($editMode ?? false) : ?>
                <hr>
                <p>
                    <small style="color: red">
                        <strong>Head up!</strong>. Bellow items are not editable at runtime.<br/>
                        Please use Versions control page to switch between versions.
                    </small>
                </p>
                <?php endif; ?>

                <?php
                echo $form->input_text([
                    'name'      => 'path',
                    'value'     => $path ?? "",
                    'label'     => 'Path',
                    'disabled'  => $editMode ?? false
                ]);
                ?>

                <?php if (($editMode ?? false) == false) : ?>
                    <?php
                    echo $form->input_checkbox_inline([
                        'name'      => 'create_path',
                        'value'     => '1',
                        'selected'  => $create_path ?? true,
                        'label'     => 'Create path if not exists',
                        'disabled'  => $editMode ?? false
                    ]);
                    ?>
                <?php endif; ?>

                <?php
                echo $form->input_text([
                    'name'      => 'version',
                    'value'     => $version ?? "",
                    'label'     => 'Version',
                    'placeholder' => 'v0.0',
                    'style'     => 'max-width: 100px',
                    'disabled'  => $editMode ?? false
                ]);
                ?>

                <?php echo $form->fieldset_open('Directory strategy'); ?>

                <?php
                echo $form->input_radio_inline([
                    'name'      => 'dir_strategy_link_version',
                    'value'     => 'multiple',
                    'selected'  => true,
                    'label'     => 'Multiple versions, current version by symbolic link',
                    'readonly'  => $editMode ?? false
                ]);
                ?>

                <?php echo $form->fieldset_close(); ?>

                <?php
                echo $form->input_text([
                    'name'      => 'current_folder',
                    'value'     => $current_folder ?? "",
                    'label'     => 'Current folder. Used for switching version',
                    'placeholder' => 'current',
                ]);
                ?>

                <?php
                echo $form->input_textarea([
                    'id'        => 'extra_links',
                    'name'      => 'extra_links',
                    'value'     => isset($extra_links) ? implode("\r\n", $extra_links) : "",
                    'label'     => 'Extra links, one per line, format: "sourcePath:targetPath"'
                ]);
                ?>

                <?php
                echo $form->input_textarea([
                    'id'        => 'ignore_files',
                    'name'      => 'ignore_files',
                    'value'     => isset($ignore_files) ? implode("\r\n", $ignore_files) : "common",
                    'label'     => 'List of files should be ignored in app folder (to not be listed as a version)'
                ]);
                ?>

                <?php
                echo $form->input_submit([
                    'name' => 'submit',
                    'value' => 'Submit',
                ]);
                ?>
            </li>
            <li>
                <p>Set bellow information, if you want to use S3 Pull deployment strategy</p>

                <?php
                echo $form->input_text([
                    'name'      => 's3_key',
                    'value'     => $s3_key ?? "",
                    'label'     => 'Access key',
                ]);
                ?>

                <?php
                echo $form->input_text([
                    'name'      => 's3_secret',
                    'value'     => $s3_secret ?? "",
                    'label'     => 'Secret',
                ]);
                ?>

                <?php
                echo $form->input_text([
                    'name'      => 's3_version',
                    'value'     => $s3_version ?? "latest",
                    'label'     => 'Client version',
                ]);
                ?>

                <?php
                echo $form->input_text([
                    'name'      => 's3_bucket_name',
                    'value'     => $s3_bucket_name ?? "",
                    'label'     => 'Bucket name',
                ]);
                ?>

                <?php
                echo $form->input_text([
                    'name'      => 's3_region',
                    'value'     => $s3_region ?? "",
                    'label'     => 'Region',
                ]);
                ?>

                <?php
                echo $form->input_text([
                    'name'      => 's3_endpoint',
                    'value'     => $s3_endpoint ?? "",
                    'label'     => 'Endpoint',
                ]);
                ?>

                <?php
                echo $form->input_submit([
                    'name' => 'submit',
                    'value' => 'Submit',
                ]);
                ?>

            </li>
            <li>
                <p>Set bellow information, if you want to use SFTP Pull deployment strategy</p>
            </li>
            <li>
                <p>Allow to call AppMan operations for this application via API. Leave API Key blank to disable this
                    function
                </p>

                <?php
                echo $form->input_text([
                    'name'      => 'api_key',
                    'value'     => $api_key ?? "",
                    'label'     => 'API Key',
                ]);
                ?>

                <p>
                    <a href="#" id="btn-genkey">Generate random key</a>
                </p>

                <?php
                echo $form->input_submit([
                    'name' => 'submit',
                    'value' => 'Submit',
                ]);
                ?>
            </li>
        </ul>

    </div>

</form>

<script>
    function makeid(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
    $(function() {
        $(".tabs1").tabs({
            touch: false,
            animate: false
        });
        $('#btn-genkey').on('click', function(e) {
            e.preventDefault();
            $('#api_key').attr('value', makeid(48));
        })
    })
</script>

