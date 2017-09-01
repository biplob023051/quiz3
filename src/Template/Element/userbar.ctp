<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#"></a>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-right">
                <!--nocache-->
                <?php if ($this->Session->check('Auth.User.name')): ?>
                    <li>
                        <div class="user-image"></div>
                    </li>
                    <li><?= $this->Html->link(h($this->Session->read('Auth.User.name')), array('controller' => 'users', 'action' => 'settings')); ?></li>
                    <li>                            
                        <?php
                        echo $this->Html->link(__('LOGOUT'), array(
                            'controller' => 'user',
                            'action' => 'logout'
                        ));
                        ?>
                    </li>
                <?php endif; ?>
                <!--/nocache-->
            </ul>
        </div>
    </div>
</nav>
