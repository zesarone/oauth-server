<h1><?= $authParams['client']->getName() ?> would like to access:</h1>

<ul>
    <?php foreach ($authParams['scopes'] as $scope): ?>
        <li>
            <?= $scope->getId() ?>: <?= $scope->getDescription() ?>
        </li>
    <?php endforeach; ?>
</ul>
<?= $this->Form->create(null); ?>
    <input type="submit" value="Approve" name="authorization">
    <input type="submit" value="Deny" name="authorization">
<?= $this->Form->end(); ?>