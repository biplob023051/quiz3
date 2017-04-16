<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-warning" onclick="this.classList.add('hidden');"><?= $message ?></div>