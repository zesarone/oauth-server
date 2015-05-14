<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Client'), ['action' => 'edit', $client->client_id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Client'), ['action' => 'delete', $client->client_id], ['confirm' => __('Are you sure you want to delete # {0}?', $client->client_id)]) ?> </li>
        <li><?= $this->Html->link(__('List Clients'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Client'), ['action' => 'add']) ?> </li>
    </ul>
</div>
<div class="clients view large-10 medium-9 columns">
    <h2><?= h($client->client_id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Client Id') ?></h6>
            <p><?= h($client->client_id) ?></p>
            <h6 class="subheader"><?= __('Client Secret') ?></h6>
            <p><?= h($client->client_secret) ?></p>
            <h6 class="subheader"><?= __('Redirect Uri') ?></h6>
            <p><?= h($client->redirect_uri) ?></p>
            <h6 class="subheader"><?= __('Parent Model') ?></h6>
            <p><?= h($client->parent_model) ?></p>
            <h6 class="subheader"><?= __('Parent Id') ?></h6>
            <p><?= h($client->parent_id) ?></p>
        </div>
    </div>
</div>