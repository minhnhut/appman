<?php
    /** @var Formr $form */
    $form = $this->form();
?>

<form method="POST" style="max-width: 500px">

    <?php
    echo $form->input_text([
        'name'      => 'app_name',
        'value'     => $name ?? "",
        'label'     => 'App name',
        'readonly'  => $readonly ?? false
    ]);
    ?>

    <?php
    echo $form->input_text([
        'name'      => 'path',
        'value'     => $path ?? "",
        'label'     => 'Path',
        'readonly'  => $readonly ?? false
    ]);
    ?>

    <?php if (($readonly ?? false) == false) : ?>
    <?php
    echo $form->input_checkbox_inline([
        'name'      => 'create_path',
        'value'     => '1',
        'selected'  => $create_path ?? true,
        'label'     => 'Create path if not exists',
        'readonly'  => $readonly ?? false
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
        'readonly'  => $readonly ?? false
    ]);
    ?>

    <?php echo $form->fieldset_open('Directory strategy'); ?>

        <?php
        echo $form->input_radio_inline([
            'name'      => 'dir_strategy_link_version',
            'value'     => 'multiple',
            'selected'  => true,
            'label'     => 'Multiple versions, current version by symbolic link',
            'readonly'  => $readonly ?? false
        ]);
        ?>

    <?php echo $form->fieldset_close(); ?>

    <?php
    echo $form->input_submit([
        'name' => 'submit',
        'value' => 'Setup',
    ]);
    ?>
</form>

