<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $client->client_id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $client->client_id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Clients'), ['action' => 'index']) ?></li>
    </ul>
</div>
<div class="clients form large-10 medium-9 columns">
    <?= $this->Form->create($client); ?>
    <fieldset>
        <legend><?= __('Edit Client') ?></legend>
        <?php
            echo $this->Form->input('redirect_uri');
            echo $this->Form->input('parent_model');
            echo $this->Form->input('parent_id');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
