<?php
    /** @var Formr $form */
    $form = $this->form();
?>

<form method="POST" style="max-width: 500px">

    <?php
    echo $form->input_text([
        'name' => 'app_name',
        'value' => $app_name ?? "",
        'label' => 'App name'
    ]);
    ?>

    <?php
    echo $form->input_text([
        'name' => 'path',
        'value' => $path ?? "",
        'label' => 'Path'
    ]);
    ?>

    <?php
    echo $form->input_checkbox_inline([
        'name' => 'create_path',
        'value' => '1',
        'selected' => $create_path ?? true,
        'label' => 'Create path if not exists'
    ]);
    ?>

    <?php
    echo $form->input_text([
        'name' => 'version',
        'value' => $version ?? "",
        'label' => 'Initial version',
        'placeholder' => 'v0.0',
        'style' => 'max-width: 100px'
    ]);
    ?>

    <fieldset>
        <legend>Directory strategy</legend>
        <label style="font-weight: normal">
            <input type="radio" name="dir_strategy_link_version" checked>
            Multiple versions, current version by symbolic link
        </label>
    </fieldset>

    <p>
        <input class="button" type="submit" value="Setup">
    </p>
</form>

