<?php if(isset($_SESSION['mensagem'])): ?>
<div class="alert alert-success"><?= $_SESSION['mensagem'] ?></div>
<?php unset($_SESSION['mensagem']); endif; ?>