<?php $this->layout('_layout'); ?>

<h2>Edit app</h2>

<?php $this->insert('partial::form', array_merge(['readonly' => true], $app)); ?>
