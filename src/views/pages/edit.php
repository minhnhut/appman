<?php $this->layout('_layout'); ?>

<h2>Edit app</h2>

<?php if ($success ?? false) : ?>
    <p id="alert" style="transition: all 300ms ease; color: greenyellow; background: green; padding: 6px">
        App config was updated successfully.
    </p>
<?php endif; ?>

<?php $this->insert('partial::form', array_merge(['editMode' => true], $app)); ?>

<script>
    $(function() {
        setTimeout(function () {
            $('#alert').css({'opacity': '0.2'});
        }, 3000)
    });
</script>
