<?php $this->layout('_layout'); ?>

<form method="POST" style="max-width: 500px">
    <h2>Setup new App</h2>

    <label for="app_name">App name</label>
    <input id="app_name" required name="app_name" type="text">

    <label for="path">Path</label>
    <input id="path" required name="path" type="text">

    <label for="create_path">
        <input type="checkbox" name="create_path" id="create_path">
        Create path if not exists
    </label>

    <label for="version">Initial version</label>
    <input id="version" name="version" type="text" placeholder="v0.0" style="max-width: 100px">

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


